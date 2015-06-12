<?php
namespace cms;

/**
 * Multiple pages loaders
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
	 * Create Pages object instance from path
	 *
	 * @param string $path
	 * @param array|callable $filter
	 * @param string $class
	 * @return \cms\Pages
	 */
	public static function from($path, $filter = ['index', '404'], $class = Page::class) {
		$iterator = new \RecursiveDirectoryIterator(realpath($path), \RecursiveDirectoryIterator::SKIP_DOTS);
		if (class_exists($class)) $iterator->setInfoClass($class);

		$filter = is_callable($filter) ? $filter : function (Page $item) use ($filter) {
			return $item->isValid((array)$filter);
		};

		return new self(new \RecursiveCallbackFilterIterator($iterator, $filter));
	}

	/**
	 * Return all items as sorted array
	 * Notice: Can be memory greedy when load many files.
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
	 * Return items as nested array
	 * Notice: Can be memory greedy when load many files.
	 *
	 * @return array
	 */
	public function toArray() {
		$toArray = function (\RecursiveIterator $iterator) use (&$toArray) {
			$array = [];
			foreach ($iterator as $file) {
				/** @var Page $file */
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