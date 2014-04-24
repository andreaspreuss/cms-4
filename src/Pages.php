<?php
namespace vestibulum;

/**
 * Menu helper (Be careful, all affected files are loaded into memory!!!)
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Pages {

	/** @var \RecursiveIterator */
	public $iterator;

	/**
	 * @param \RecursiveIterator $iterator
	 */
	public function __construct(\RecursiveIterator $iterator) {
		$this->iterator = $iterator;
	}

	/**
	 * Create Files object instance from path
	 *
	 * @param string $path
	 * @param array|callable $filter
	 * @return \vestibulum\Pages
	 */
	public static function from($path, $filter = ['index', '404']) {
		$iterator = new \RecursiveDirectoryIterator(realpath($path), \RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator->setInfoClass('\\Vestibulum\\File');

		$filter = is_callable($filter) ? $filter : function (File $item) use ($filter) {
			return $item->isValid((array)$filter);
		};

		return new self(new \RecursiveCallbackFilterIterator($iterator, $filter));
	}

	/**
	 * Return File items as sorted array
	 *
	 * @param string $column
	 * @param int $sort
	 * @return array
	 */
	public function toArraySorted($column = 'order', $sort = SORT_NATURAL) {
		$array = $this->toArray();

		$sorting = function (&$array) use (&$sorting, $column, $sort) {
			$arr = [];
			foreach ($array as $key => $row) {
				$arr[$key] = $row->$column;
				if (isset($row->children)) $sorting($row->children, $column, $sort);
			}
			array_multisort($arr, $sort, $array);
		};

		$sorting($array);
		return $array;
	}

	/**
	 * Return File items as array
	 *
	 * @return array
	 */
	public function toArray() {
		$toArray = function (\RecursiveIterator $iterator) use (&$toArray) {
			$array = [];
			foreach ($iterator as $file) {
				/** @var File $file */
				$current = $file;
				if ($iterator->hasChildren()) {
					$current->children = $toArray($iterator->getChildren());
				}
				$array[] = $current; // append current element
			}
			return $array;
		};

		return $toArray($this->iterator);
	}
}