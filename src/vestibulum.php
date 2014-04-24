<?php
namespace vestibulum;

use SplFileInfo;

/**
 * Vestibulum: Really deathly simple CMS
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Vestibulum extends \stdClass {

	use Config;
	use Request;

	/** @var File */
	public $file;
	/** @var string */
	public $content;

	public function __construct() {
		$this->requires();
		$this->file = $this->getFile((array)$this->config()->meta);
		$this->functions();
	}

	/**
	 * Requires PHP first
	 */
	public function requires() {
		is_file($php = getcwd() . $this->getRequest() . '.php') ? include_once $php : null;
		is_file($php = $this->src() . $this->getRequest() . '.php') ? include_once $php : null;
	}

	/**
	 * Auto include functions.php
	 */
	public function functions() {
		global $cms;
		$cms = $this; // create link to $this
		is_file($functions = getcwd() . '/functions.php') ? include_once $functions : null;
	}

	/**
	 * Return current file
	 *
	 * @param array $meta
	 * @return File
	 */
	public function getFile(array $meta = []) {
		$file = File::fromRequest($this->src() . $this->getRequest(), $meta);
		if ($file === null) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
			$file = File::fromRequest($this->src() . '/404', $meta);
		}
		return $file ? : new File($this->src(), $meta, '<h1>404 Page not found</h1>');
	}

	/**
	 * @param File $file
	 * @return object
	 */
	public function getMeta(File $file) {
		return (object)$file->getMeta((array)$this->config()->meta);
	}

	protected function render() {
		// Content

		$this->content = str_replace('%url%', $this->url(), $this->file->getContent());

		// FIXME and find better way how to save to cache
		if ($this->file->getExtension() === 'md') {

			// @see https://github.com/erusev/parsedown/pull/105
			$this->content = preg_replace('/<!--(.*)-->/Uis', '', $this->content, 1); // first only

			$cache = isset($this->config()->markdown['cache']) && $this->config()->markdown['cache'] ? realpath(
				$this->config()->markdown['cache']
			) : false;
			if ($cache && is_dir($cache) && is_writable($cache)) {
				$cacheFile = $cache . '/' . md5($this->file);
				if (!is_file($cacheFile) || @filemtime($this->file) > filemtime($cacheFile)) {
					$this->content = \Parsedown::instance()->parse($this->content);
					file_put_contents($cacheFile, $this->content);
				} else {
					$this->content = file_get_contents($cacheFile);
				}
			} else {
				$this->content = \Parsedown::instance()->parse($this->content);
			}
		}

		$ext = pathinfo($this->file->template, PATHINFO_EXTENSION);

		// phtml - for those who have an performance obsession :-)

		if ($ext === 'phtml' || $ext === 'php') {
			ob_start();
			extract(get_object_vars($this));
			require($this->file->template);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		// twig

		if ($ext === 'twig') {
			$loader = new \Twig_Loader_Filesystem($this->config->templates);
			$twig = new \Twig_Environment($loader, $this->config->twig);
			$twig->addExtension(new \Twig_Extension_Debug());
			$twig->addExtension(new \Twig_Extension_StringLoader());

			// undefined filters callback
			$twig->registerUndefinedFilterCallback(
				function ($name) {
					return function_exists($name) ?
						new \Twig_SimpleFilter($name, function () use ($name) {
							return call_user_func_array($name, func_get_args());
						}, ['is_safe' => ['html']]) : false;
				}
			);

			$twig->addFunction('url', new \Twig_SimpleFunction('url', [$this, 'url']));

			// undefined functions callback
			$twig->registerUndefinedFunctionCallback(
				function ($name) {
					return function_exists($name) ?
						new \Twig_SimpleFunction($name, function () use ($name) {
							return call_user_func_array($name, func_get_args());
						}) : false;
				}
			);

			return $twig->render($this->file->template, get_object_vars($this));
		}
	}

	/**
	 * Render string content
	 *
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->render();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

}

/**
 * Extract metadata from file
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Metadata {

	/**
	 * Extract main title from markdown
	 *
	 * @param string $content
	 * @return null|string
	 */
	public static function parseTitle($content) {
		$pattern = '/<h1[^>]*>([^<>]+)<\/h1>| *# *([^\n]+?) *#* *(?:\n+|$)/isU';
		if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
			$first = reset($matches);
			return trim(end($first));
		}
	}

	/**
	 * Shorten plain text content
	 *
	 * @see https://github.com/nette/utils/blob/master/src/Utils/Strings.php
	 *
	 * @param $content
	 * @param int $length
	 * @return mixed
	 */
	public static function shorten($content, $length = 128) {
		$s = static::text($content);

		if (strlen(utf8_decode($s)) > $length) {
			if (preg_match('#^.{1,' . $length . '}(?=[\s\x00-/:-@\[-`{-~])#us', trim($s), $matches)) {
				return reset($matches);
			}
			return (function_exists('mb_substr') ? mb_substr($s, 0, $length, 'UTF-8') : iconv_substr(
				$s, 0, $length, 'UTF-8'
			));
		}

		return $s;
	}

	/**
	 * Return plain text from markdown and HTML mix
	 *
	 * @see https://gist.github.com/jbroadway/2836900
	 *
	 * @param string $content
	 * @return mixed
	 */
	public static function text($content) {
		$rules = array(
			'/(#+) ?(.*)/' => '\2', // headers
			'/\[([^\[]+)\]\(([^\)]+)\)/' => '\1', // links
			'/(\*\*|__)(.*?)\1/' => '\2', // bold
			'/(\*|_)(.*?)\1/' => '\2', // emphasis
			'/\~\~(.*?)\~\~/' => '\1', // del
			'/\:\"(.*?)\"\:/' => '\1', // quote
			'/`(.*?)`/' => '\1', // inline code
			'/\s+/' => ' ' // strip spaces
		);

		return trim(preg_replace(array_keys($rules), array_values($rules), strip_tags($content)));
	}

	/**
	 * Parse content and getting metadata
	 *
	 * @param $content
	 * @return array
	 */
	public static function parseMeta($content) {
		preg_match('/<!--(.*)-->/sU', $content, $matches);
		if ($matches && $ini = end($matches)) {
			return parse_ini_string(str_replace(':', '=', $ini), false, INI_SCANNER_RAW);
		}
	}
}

trait Request {

	/** @var string */
	private $request;

	/**
	 * Return requested URL
	 *
	 * @return mixed
	 */
	public function getRequest() {
		if (isset($this->request)) return $this->request;
		$this->request = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
		return $this->request = preg_replace(['#\?.*#', '#/?index.php#'], ['', ''], urldecode($this->request));
	}

	/**
	 * Return current URL
	 *
	 * @param null $url
	 * @return string
	 */
	public static function url($url = null) {
		return (isset($_SERVER['HTTPS']) && strcasecmp(
			$_SERVER['HTTPS'], 'off'
		) ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
			(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '')) .
		($_SERVER["SERVER_PORT"] == '80' ? null : ':' . $_SERVER["SERVER_PORT"]) . '/' . ltrim(
			parse_url($url, PHP_URL_PATH), '/'
		);
	}
}


/**
 * Simple singleton config object
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Config {

	/** @var \stdClass */
	private $config;

	/**
	 * Return src dirname
	 *
	 * @return string
	 */
	public function src() {
		return (isset($this->config()->src) ? realpath($this->config()->src) : $this->config()->src = getcwd() . '/src/');
	}

	/**
	 * Return configuration
	 *
	 * @return \stdClass
	 */
	public function config() {
		return $this->config ? $this->config : $this->config = (object)array_replace_recursive(
			[
				'title' => 'Vestibulum',
				'twig' => [
					'cache' => false,
					'autoescape' => false,
					'debug' => false,
				],
				'markdown' => [
					'cache' => false,
				],
				'src' => getcwd() . '/src/',
				'templates' => getcwd(),
				'meta' => [
					'template' => 'index.twig',
				]
			],
			@include(getcwd() . '/config.php') // intentionally @
		);
	}
}

/**
 * Vestibulum file with metadata
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
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class File extends \SplFileInfo {

	use Metadata;

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

		$title = $this->parseTitle($this->getContent()) ? : ucfirst($this->getName());

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
			'file' => $this->isFile() ? $this->getRealPath() : null,
		];

		return array_merge($default, $meta, (array)$this->parseMeta($this->getContent()));
	}

	/**
	 * Return automatic description
	 *
	 * @return mixed
	 */
	public function getDescription() {
		return isset($this->description) ? $this->description : $this->shorten($this->getContent());
	}

	/**
	 * Return link to file
	 *
	 * @param string|null $src
	 * @return string
	 */
	public function getSlug($src = null) {
		return str_replace(
			realpath($src),
			'',
			$this->isDir() ? $this->getRealPath() : $this->getDir() . '/' . ($this->getName() !== 'index' ? $this->getName(
				) : null)
		);
	}

	/**
	 * Return file metadata value
	 *
	 * @param string $name
	 * @return null
	 */
	public function __get($name) {
		return array_key_exists($name, $this->meta) ? $this->meta[$name] : null;
	}

	/**
	 * Set meta value
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		$this->meta[$name] = $value;
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
		return $this->getBasename('.' . $this->getExtension());
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
		return preg_match('#md|html#i', $this->getExtension()) && !in_array($this->getName(), $skip);
	}

	/**
	 * Return file content
	 *
	 * @return string
	 */
	public function getContent() {
		if (isset($this->content)) return $this->content;

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
	 * @param $request
	 * @param array $meta
	 * @return static
	 */
	public static function fromRequest($request, array $meta = []) {
		if (
			is_file($file = $request . '.html') ||
			is_file($file = $request . '.md') ||
			// is_file($file = $request . '.php') || // TODO add raw PHP support
			is_dir($request) && is_file($file = $request . '/index.html') ||
			is_dir($request) && is_file($file = $request . '/index.md')
		) {
			return new static($file, $meta);
		}
	}
}

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
	 * @param $path
	 * @param array $skip
	 * @param callable $filter
	 * @return \vestibulum\Pages
	 */
	public static function from($path, array $skip = ['index', '404'], callable $filter = null) {
		$iterator = new \RecursiveDirectoryIterator(realpath($path), \RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator->setInfoClass('\\Vestibulum\\File');

		$filter = $filter ? : function (File $item, $key, \RecursiveIterator $iterator) use ($skip) {
			return $item->isValid($skip) ? $item : null;
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