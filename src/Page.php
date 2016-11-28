<?php
namespace cms;

use parsers\MetadataParser as Metadata;
use parsers\TitleParser as Title;

/**
 * Sphido file with metadata
 *
 * @property string id
 * @property string class
 * @property string title
 * @property string order
 * @property string date
 * @property string access
 * @property string name
 * @property string basename
 * @property string dir
 * @property string file
 * @property string status
 * @property string template
 * @property string latte
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Page extends \SplFileInfo {

	/** @var array */
	protected $meta;

	/** @var string|null */
	protected $content;

	/** @var array */
	public $children;

	public function __construct($file = null, array $meta = [], $content = null) {
		parent::__construct($file);
		$this->meta = $this->getMeta($meta);
		$this->content = $content;
	}

	/**
	 * Return current file metadata
	 *
	 * @param array $meta
	 * @return array
	 */
	public function getMeta(array $meta = []) {
		if ($this->meta) return array_merge($meta, $this->meta);

		$title = Title::parse($this->getContent()) ?: ucfirst($this->getName());

		$default = [
			'id' => md5($this->getContent() . $this->getRealPath()),
			'class' => preg_replace('/[.]/', '', strtolower($this->getName())),
			'title' => $title,
			'order' => $title,
			'date' => $this->isFile() || $this->isDir() ? $this->getCTime() : null,
			'created' => $this->isFile() || $this->isDir() ? $this->getCTime() : null,
			'access' => $this->isFile() || $this->isDir() ? $this->getATime() : null,
			'name' => $this->getName(),
			'basename' => $this->getFilename(),
			'dir' => $this->isDir() ? $this->getDir() : null,
			'file' => $this->isFile() ? $this->getRealPath() : null
		];

		$parsed = (array)Metadata::parse($this->getContent());

		return filter(
			self::class . '::' . __FUNCTION__,
			array_merge($default, $meta, $parsed),
			$default, $meta, $parsed
		);
	}

	/**
	 * Return automatic description
	 * Notice: getDescription use slow function shorten() in some cases
	 *
	 * @return mixed
	 */
	public function getDescription() {
		return filter('get_description', $this->description);
	}

	/**
	 * Return link to file
	 *
	 * @param string|null $src
	 * @return string
	 */
	public function getSlug($src = null) {
		$slug = str_replace(
			realpath($src), '',
			$this->isDir() ? $this->getRealPath() : $this->getDir() . '/' .
			$this->getName() !== 'index' ? $this->getName() : null
		);
		return filter(self::class . '::' . __FUNCTION__, $slug, $this);
	}

	/**
	 * @param null|string $src
	 * @return string
	 */
	public function getUrl($src = null) {
		return filter(self::class . '::' . __FUNCTION__, url($this->getSlug($src ? $src : \dir\content())));
	}

	/**
	 * Return file metadata value
	 *
	 * @param string $name
	 * @return null
	 */
	public function __get($name) {
		return filter(
			[self::class. '::get', self::class . '::' . $name],
			array_key_exists($name, $this->meta) ? $this->meta[$name] : null
		);
	}

	/**
	 * Set meta value
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		$this->meta[$name] = filter([self::class . '::set', self::class . '::' . $name], $value);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function __isset($name) {
		return array_key_exists($name, $this->meta);
	}


	/**
	 * Return name of file without extension
	 *
	 * @return string
	 */
	public function getName() {
		return filter(self::class . '::' . __FUNCTION__, $this->getBasename('.' . $this->getExtension()));
	}

	/**
	 * Check extension.
	 *
	 * @param string $ext
	 * @return bool
	 */
	public function is($ext) {
		return $this->getExtension() === $ext;
	}

	/**
	 * Return current directory
	 *
	 * @return string
	 */
	public function getDir() {
		return $this->isDir() ? $this->getRealPath() : dirname($this->getRealPath());
	}

	/**
	 * @param array $skip
	 * @return bool
	 */
	public function isValid(array $skip = []) {
		if ($this->isDir()) return !in_array($this->getRealPath(), $skip);
		return preg_match('#md|html|phtml|latte#i', $this->getExtension()) && !in_array($this->getName(), $skip);
	}

	/**
	 * Return file content
	 *
	 * @return string
	 */
	public function getContent() {
		if (isset($this->content)) return $this->content;

		// FIXME can be problem if index.php or index.latte
		if ($this->isDir()) {
			return $this->content =
				is_file($file = $this . '/index.html') || is_file($file = $this . '/index.md') ? file_get_contents($file) : '';
		}

		return $this->content = $this->isFile() ? file_get_contents($this->getRealPath()) : '';
	}

	/**
	 * Set file content
	 *
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Create new File instance from path
	 *
	 * @param string $path
	 * @param array $meta
	 * @return static
	 */
	public static function fromPath($path, array $meta = []) {
		if (
			// try found file
			is_file($file = $path . '.html') ||
			is_file($file = $path . '.md') ||
			is_file($file = $path . '.latte') ||
			is_file($file = $path . '.phtml') ||
			// index file from in dir
			is_dir($path) && (
				is_file($file = $path . '/index.html') ||
				is_file($file = $path . '/index.md') ||
				is_file($file = $path . '/index.latte') ||
				is_file($file = $path . '/index.phtml')
			)
		) {
			return new static(realpath($file), $meta, null);
		}
	}
}
