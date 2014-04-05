<?php
namespace vestibulum;

use Michelf\MarkdownExtra;

/**
 * Vestibulum: Really deadly simple CMS
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
	/** @var array */
	public $meta;
	/** @var array */
	public $pages;
	/** @var string */
	public $home;

	public function __construct() {
		$this->home = $this->url();
		$this->file = $this->getFile();
		$this->content = $this->file->getContent();
		$this->meta = $this->getMeta($this->file);
		$this->pages = $this->getPages($this->file->getDir());
		$this->functions();
	}

	/**
	 * Auto include functions.php
	 */
	public function functions() {
		global $cms;
		$cms = $this; // create link to $this
		@include_once getcwd() . '/functions.php'; // intentionally @
	}

	/**
	 * Return current file
	 *
	 * @return File
	 */
	public function getFile() {
		$file = File::fromRequest($this->src() . $this->getRequest());
		if ($file === null) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
			$file = File::fromRequest($this->src() . '/404', ['class' => 'page-not-found']);
		}
		return $file ? : new File($this->src(), ['class' => 'page-not-found'], '<h1>404 Page not found</h1>');
	}

	/**
	 * Skip selected files
	 *
	 * @param $file
	 * @return bool
	 */
	public function skip($file) {
		return in_array(basename($file, '.' . pathinfo($file, PATHINFO_EXTENSION)), $this->config->skip);
	}


	/**
	 * Return pages meta from selected path
	 *
	 * @param $path
	 * @return array
	 */
	public function getPages($path) {
		$files = (array)glob($path . '/*.{html,md}', GLOB_BRACE);

		$pages = [];
		foreach ($files as $id => $file) {
			if ($this->skip($file)) continue;
			$file = new File($file);
			$pages[realpath($file)] = (object)$file->getMeta(
				['slug' => str_replace($this->src(), '', $file->getDir() . '/' . $file->getName())]
			);
		}

		uasort(
			$pages, function ($a, $b) {
				if (is_numeric($a->order) && is_numeric($b->order)) {
					return $a->order > $b->order;
				}
				return strcmp($a->order, $b->order);
			}
		);

		return $pages;
	}


	/**
	 * @param File $file
	 * @return object
	 */
	public function getMeta(File $file) {
		return (object)$file->getMeta((array)$this->config()->meta);
	}


	protected function render() {
		$loader = new \Twig_Loader_Filesystem($this->config->templates);
		$twig = new \Twig_Environment($loader, $this->config->twig);
		$twig->addExtension(new \Twig_Extension_Debug());

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

		$this->content = str_replace('%url%', $this->url(), $this->content);

		// FIXME and find better way how to save to cache
		if (pathinfo($this->file, PATHINFO_EXTENSION) === 'md') {
			$cache = isset($this->config->markdown['cache']) && $this->config->markdown['cache'] ? realpath(
				$this->config->markdown['cache']
			) : false;
			if ($cache && is_dir($cache) && is_writable($cache)) {
				$cacheFile = $cache . '/' . md5($this->file);
				if (!is_file($cacheFile) || filemtime($this->file) > filemtime($cacheFile)) {
					$this->content = MarkdownExtra::defaultTransform($this->content);
					file_put_contents($cacheFile, $this->content);
				} else {
					$this->content = file_get_contents($cacheFile);
				}
			} else {
				$this->content = MarkdownExtra::defaultTransform($this->content);
			}

		}

		return $twig->render($this->meta->template, $this->toArray());
	}

	/**
	 * Return array of object Variables
	 *
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
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
			'/<(.|\n)*?>/' => '', // strip tags
			'/\s+/' => ' ' // strip spaces
		);

		return trim(preg_replace(array_keys($rules), array_values($rules), $content));
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
					'author' => null,
				],
				'skip' => ['404', 'index'],
			],
			@include(getcwd() . '/config.php') // intentionally @
		);
	}
}

/**
 * Vestibulum file with metadata
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
			'date' => $this->getCTime(),
			'created' => $this->getCTime(),
			'access' => $this->getATime(),
			'description' => $this->shorten($this->getContent()),
			'name' => $this->getName(),
			'basename' => $this->getFilename(),
			'dir' => $this->getDir(),
			'file' => $this->isFile() ? $this->getRealPath() : null,
		];

		$meta = array_merge($default, $meta, (array)$this->parseMeta($this->getContent()));
		return $meta;
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
	 * @return bool
	 */
	public function isValid() {
		return $this->isFile() ||
		$this->isDir() && (
			is_file($this->getRealPath() . '/index.html') || is_file($this->getRealPath() . '/index.md')
		);
	}

	/**
	 * Return file contentx
	 *
	 * @return string
	 */
	public function getContent() {
		if (isset($this->content)) return $this->content;

		if ($this->isDir()) {
			is_file($file = $this->getRealPath() . '/index.html') || is_file($file = $this->getRealPath() . '/index.md');
		} else {
			$file = $this->getRealPath();
		}

		return ($this->content) ? $this->content : $this->content = @file_get_contents($file);
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
			is_dir($request) && is_file($file = $request . '/index.html') ||
			is_dir($request) && is_file($file = $request . '/index.md')
		) {
			return new static($file, $meta);
		}
	}

}