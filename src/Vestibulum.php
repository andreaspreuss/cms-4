<?php
namespace vestibulum;

require_once __DIR__ . '/functions.php';

use Latte\Engine;
use Latte\Macros\MacroSet;
use Latte\Runtime\Filters;

/**
 * Vestibulum: Really deathly simple CMS
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Vestibulum extends \stdClass {

	/** @var \stdClass */
	public $config;
	/** @var File */
	public $file;
	/** @var string */
	public $content;

	public function __construct() {
		$this->config = config();
		$this->requires();
		$this->file = $this->getFile((array)$this->config->meta);
		$this->functions();
	}

	/**
	 * Requires PHP
	 */
	public function requires() {
		// src index.php of request.php
		is_file($php = src(request() . '/index.php')) ? include_once $php : null ||
		is_file($php = src(request() . '.php')) ? include_once $php : null;

		// cwd index.php of request.php
		is_file($php = getcwd() . request() . '/index.php') ? include_once $php : null ||
		is_file($php = getcwd() . request() . '.php') ? include_once $php : null;
	}

	/**
	 * Requires functions.php
	 */
	public function functions() {
		global $cms;
		$cms = $this; // create link to $this
		@include_once getcwd() . '/functions.php'; //
	}

	/**
	 * Return current file
	 *
	 * @param array $meta
	 * @return File
	 */
	public function getFile(array $meta = []) {
		$files = [
			src(request()),
			src(dirname(request()) . '/404'),
			src('/404')
		];

		foreach ($files as $path) {
			if ($file = File::fromPath($path, $meta)) return $file;
		}

		return new File(src(), array_merge($meta, ['status' => 404]), '<h1>404 Page not found</h1>'); // last chance
	}


	/**
	 * TODO caching
	 *
	 * @return string
	 */
	protected function render() {

		// HTTP response status code
		if ($code = isset($this->file->status) ? $this->file->status : null) {
			header(
				(isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.1") . " " . $code, true, $code
			);
		}

		if ($this->file->getExtension() === 'phtml') {
			ob_start();
			extract(get_object_vars($this));
			require($this->file);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}


		if ($this->file->getExtension() === 'md' || $this->file->getExtension() === 'html') {
			$this->content = preg_replace_callback(
				"/{url\s?['\"]?([^\"'}]*)['\"]?}/", function ($m) {
					return Filters::safeUrl(url(end($m)));
				},
				$this->file->getContent()
			);
		}

		// FIXME and find better way how to save to cache
		if ($this->file->getExtension() === 'md') {
			$cache = isset($this->config->cache) && $this->config->cache ? realpath($this->config->cache) : false;
			if ($cache && is_dir($cache) && is_writable($cache)) {
				$cacheFile = $cache . '/' . md5($this->file) . '.html';
				if (!is_file($cacheFile) || $this->file->getMTime() > filemtime($cacheFile)) {
					$this->content = \Parsedown::instance()->text($this->content);
					file_put_contents($cacheFile, $this->content);
				} else {
					$this->content = file_get_contents($cacheFile);
				}
			} else {
				$this->content = \Parsedown::instance()->text($this->content);
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

		// Latte
		if ($ext === 'latte') {
			$latte = new Engine();
			$latte->setTempDirectory($this->config->cache);

			$set = new MacroSet($latte->getCompiler());
			$set->addMacro('url', 'echo \vestibulum\url(%node.args);');

			if ($this->file->latte || $this->file->getExtension() === 'latte') {
				$this->content = $latte->renderToString($this->file, get_object_vars($this));
			}

			return $latte->renderToString($this->file->template, get_object_vars($this));
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

