<?php
namespace vestibulum;

use Latte\Runtime\Filters;

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/events.php';
require_once __DIR__ . '/Metadata.php';
require_once __DIR__ . '/Pages.php';
require_once __DIR__ . '/Render.php';
require_once __DIR__ . '/Page.php';

/**
 * Vestibulum: Really deathly simple CMS
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Vestibulum extends \stdClass {

	use Render;

	/** @var Vestibulum */
	public $cms;
	/** @var Page */
	public $page;
	/** @var string */
	public $content;
	/** @var \stdClass */
	public $config;

	public function __construct() {
		$this->cms = $cms = $this;
		$this->config = config();

		// content php files
		is_file($php = content(request() . '/index.php')) ? include_once $php : null ||
		is_file($php = content(request() . '.php')) ? include_once $php : null;

		// getcwd php files
		is_file($php = getcwd() . request() . '/index.php') ? include_once $php : null ||
		is_file($php = getcwd() . request() . '.php') ? include_once $php : null;

		// get current page
		$this->page = $this->getPage((array)config()->meta);

		// function.php
		is_file($php = content(request() . '/function.php')) ? include_once $php : null;
		is_file(getcwd() . '/functions.php') ? include_once getcwd() . '/functions.php' : null;
	}

	/**
	 * Return current file
	 *
	 * @param array $meta
	 * @return Page
	 */
	public function getPage(array $meta = []) {
		$files = [
			content(request()),
			content(dirname(request()) . '/404'),
			content('/404')
		];

		foreach ($files as $path) {
			if ($file = Page::fromPath($path, $meta)) return $file;
		}

		return new Page(content(), array_merge($meta, ['status' => 404]), '<h1>404 Page not found</h1>');
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