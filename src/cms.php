<?php
namespace cms;

// All functions first
require_once __DIR__ . '/functions.dir.php';
require_once __DIR__ . '/functions.php';

// Sphido Framework core...
require_once __DIR__ . '/../vendor/sphido/config/src/config.php';
require_once __DIR__ . '/../vendor/sphido/routing/src/routing.php';
require_once __DIR__ . '/../vendor/sphido/events/src/events.php';
require_once __DIR__ . '/../vendor/sphido/url/src/url.php';

// CMS core
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
				'content' => realpath(getcwd() . '/pages/'),
				'meta' => [
					'template' => getcwd() . '/layout.latte',
				]
			],
			$config,
			is_file(getcwd() . '/config.php') ? include_once(getcwd() . '/config.php') : []
		);

		map([404, 500], [$this, 'error']); // add error handler
	}

	/**
	 * Page not found error.
	 *
	 * @param $error
	 * @param callable $method
	 * @param string $path
	 * @param Sphido $cms
	 * @return int|null
	 */
	public function error($error, $method, $path, $cms) {
		trigger('render.error', $error, $method, $path, $cms);

		if ($this->page = Page::fromPath(\dir\content() . '/404', (array)config()->meta)) {
			return print ensure('render.error', [$this, 'render'], $this);
		}

		/**
		 * @param int $error
		 * @param string $method
		 * @param string $path
		 * @param Sphido $cms
		 * @name render .default.error
		 */
		ensure('render.default.error', $error, $method, $path, $cms); // default error is on you
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
		is_file($php = \dir\content($path . '/index.php')) ? include_once $php : null ||
		is_file($php = \dir\content($path . '.php')) ? include_once $php : null;

		$this->page = Page::fromPath(\dir\content() . '/' . $path, (array)config()->meta);

		// and functions.php
		is_file($php = \dir\content($path . '/function.php')) ? include_once $php : null;
		is_file(getcwd() . '/functions.php') ? include_once getcwd() . '/functions.php' : null;

		if ($this->page) {
			echo ensure('render.page', [$this, 'render'], $this);
		} else {
			error(404, $method, $path, $this); // trigger router error
		}
	}
}