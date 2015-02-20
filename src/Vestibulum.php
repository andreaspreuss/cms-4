<?php
namespace vestibulum;

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/sphido.php';
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
		$this->config = config(
			[
				'title' => 'Vestibulum',
				'cache' => false,
				'content' => getcwd() . '/content/',
				'meta' => [
					'template' => getcwd() . '/index.latte',
				]
			],
			is_file(getcwd() . '/config.php') ? include_once(getcwd() . '/config.php') : []
		);

		map([404, 500], [$this, 'pageNotFound']);
	}

	/**
	 * Page not found.
	 */
	public function pageNotFound($error, $method, $path, $cms) {
		foreach ([content($path . '/404'), content('/404')] as $path) {
			if ($this->page = Page::fromPath($path, (array)config()->meta)) {
				echo handle('render.error', [$this, 'render'], $this);
			}
		}
	}

	/**
	 * @param $method
	 * @param $path
	 * @param $cms
	 * @return mixed
	 */
	function __invoke($method, $path, $cms) {
		$this->cms = $cms = $this;

		// content php files
		is_file($php = content($path . '/index.php')) ? include_once $php : null ||
		is_file($php = content($path . '.php')) ? include_once $php : null;

		// getcwd php files
		is_file($php = getcwd() . $path . '/index.php') ? include_once $php : null ||
		is_file($php = getcwd() . $path . '.php') ? include_once $php : null;

		// get page or return error
		$this->page = Page::fromPath(content($path), (array)config()->meta);

		// function.php
		is_file($php = content($path . '/function.php')) ? include_once $php : null;
		is_file(getcwd() . '/functions.php') ? include_once getcwd() . '/functions.php' : null;

		if ($this->page) {
			echo handle('render', [$this, 'render'], $this);
		} else {
			error(404, $method, $path, $cms);
		}
	}
}