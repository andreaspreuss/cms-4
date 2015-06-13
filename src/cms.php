<?php
namespace cms;

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/sphido.php';
require_once __DIR__ . '/Metadata.php';
require_once __DIR__ . '/Pages.php';
require_once __DIR__ . '/Render.php';
require_once __DIR__ . '/Page.php';

/**
 * Sphido:  A rocket fast flat file blog & CMS
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Sphido extends \stdClass {

	use Render;

	/** @var Sphido */
	public $cms;
	/** @var Page */
	public $page;
	/** @var string */
	public $content;
	/** @var \stdClass */
	public $config;

	public function __construct(array $config = []) {
		$this->config = config(
			[
				'title' => 'Sphido CMS',
				'cache' => false,
				'content' => getcwd() . '/pages/',
				'meta' => [
					'template' => getcwd() . '/layout.latte',
				]
			],
			$config,
			is_file(getcwd() . '/config.php') ? include_once(getcwd() . '/config.php') : []
		);

		map([404, 500], [$this, 'error']);
	}

	/**
	 * Page not found error.
	 *
	 * @param $error
	 * @param callable $method
	 * @param string $path
	 * @param Sphido $cms
	 */
	public function error($error, $method, $path, $cms) {
		foreach ([content($path . '/404'), content('/404')] as $path) {
			if ($this->page = Page::fromPath($path, (array)config()->meta)) {
				echo ensure('render.error', [$this, 'render'], $this);
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

		// inclide prepend PHP file first
		is_file($php = content($path . '/index.php')) ? include_once $php : null ||
		is_file($php = content($path . '.php')) ? include_once $php : null;

		$this->page = Page::fromPath(content($path), (array)config()->meta);

		// and functions.php
		is_file($php = content($path . '/function.php')) ? include_once $php : null;
		is_file(getcwd() . '/functions.php') ? include_once getcwd() . '/functions.php' : null;

		if ($this->page) {
			echo ensure('page.render', [$this, 'render'], $this);
		} else {
			error(404, $method, $path, $cms);
		}
	}
}