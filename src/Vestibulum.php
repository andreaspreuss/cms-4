<?php
namespace vestibulum;

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/events.php';
require_once __DIR__ . '/Metadata.php';
require_once __DIR__ . '/Pages.php';
require_once __DIR__ . '/File.php';

// external library
require_once __DIR__ . '/../vendor/latte/latte/src/latte.php';
require_once __DIR__ . '/../vendor/erusev/parsedown/Parsedown.php';

use Latte\Engine;
use Latte\Macros\MacroSet;
use Latte\Runtime\Filters;

/**
 * Vestibulum: Really deathly simple CMS
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Vestibulum extends \stdClass {

	/** @var File */
	public $file;
	/** @var string */
	public $content;
	/** @var \stdClass */
	public $config;

	public function __construct() {
		$this->config = config();
		$this->requires();
		$this->file = $this->getFile((array)config()->meta);

		@include_once getcwd() . '/functions.php'; // include functions
	}

	/**
	 * Requires PHP
	 */
	public function requires() {
		// src index.php of request.php
		is_file($php = content(request() . '/index.php')) ? include_once $php : null ||
		is_file($php = content(request() . '.php')) ? include_once $php : null;

		// cwd index.php of request.php
		is_file($php = getcwd() . request() . '/index.php') ? include_once $php : null ||
		is_file($php = getcwd() . request() . '.php') ? include_once $php : null;
	}

	/**
	 * Return current file
	 *
	 * @param array $meta
	 * @return File
	 */
	public function getFile(array $meta = []) {
		$files = [
			content(request()),
			content(dirname(request()) . '/404'),
			content('/404')
		];

		foreach ($files as $path) {
			if ($file = File::fromPath($path, $meta)) return $file;
		}

		return new File(content(), array_merge($meta, ['status' => 404]), '<h1>404 Page not found</h1>'); // last chance
	}


	/**
	 * @return string
	 */
	public function render() {

		// HTTP status code
		if ($code = isset($this->file->status) ? $this->file->status : null) status($code);

		// PHTML file execute
		if ($this->file->getExtension() === 'phtml') {
			extract(get_object_vars($this), EXTR_SKIP);
			ob_start();
			require $this->file;
			return ob_get_clean();
		}

		// replace {url} with current server URL
		if ($this->file->getExtension() === 'md' || $this->file->getExtension() === 'html') {
			$this->content = preg_replace_callback(
				"/{url\s?['\"]?([^\"'}]*)['\"]?}/", function ($m) {
					return Filters::safeUrl(url(end($m)));
				},
				$this->file->getContent()
			);
		}

		// Read markdown from cache or recompile
		if ($this->file->getExtension() === 'md') {
			$cacheFile = tmp(md5($this->file) . '.html');
			$this->content = cache(
				$cacheFile, function () {
					return \Parsedown::instance()->text($this->content);
				},
				$this->file->getMTime() > @filemtime($cacheFile)
			);
		}

		$template = pathinfo($this->file->template, PATHINFO_EXTENSION);

		// phtml - for those who have an performance obsession :-)
		if ($template === 'phtml' || $template === 'php') {
			extract(get_object_vars($this), EXTR_SKIP);
			ob_start();
			require $this->file->template;
			return ob_get_clean();
		}

		// Latte
		if ($template === 'latte') {
			$latte = $this->getLatteEngine();
			if (isset($this->file->latte) || $this->file->getExtension() === 'latte') {
				$this->content = $latte->renderToString($this->file, get_object_vars($this));
			}

			return $latte->renderToString($this->file->template, get_object_vars($this));
		}

		return $this->content;
	}

	/**
	 * Return Latte engine
	 *
	 * @return Engine
	 */
	protected function getLatteEngine() {
		$latte = new Engine();
		$latte->setTempDirectory(tmp());

		$set = new MacroSet($latte->getCompiler());
		$set->addMacro('url', 'echo \vestibulum\url(%node.args);');

		return filter('latte', $latte);
	}

	/**
	 * Render string content
	 *
	 * @return string
	 */
	public function __toString() {
		try {
			return handle('render', [$this, 'render'], $this);
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
}