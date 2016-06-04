<?php
namespace cms;

use function app\config as config;

require_once __DIR__ . '/dir.php'; // directory first

// Sphido core...
require_once __DIR__ . '/../vendor/sphido/config/src/config.php';
require_once __DIR__ . '/../vendor/sphido/routing/src/routing.php';
require_once __DIR__ . '/../vendor/sphido/events/src/events.php';
require_once __DIR__ . '/../vendor/sphido/url/src/url.php';

// main functions
require_once __DIR__ . '/functions.php';

// and CMS core
require_once __DIR__ . '/../vendor/sphido/metadata/src/Metadata.php';
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
		
		\route\map([404, 500], [$this, 'error']); // add error handler
		\route\map(filter('map.default', $this)); // pages handler
	}

	public function error($error, $method, $path, $cms) {
		trigger('render.error', $error, $method, $path, $cms);
		if ($this->page = Page::fromPath(\dir\content() . '/404', (array)config()->meta)) {
			return print ensure('render.error', [$this, 'render'], $this);
		}
		trigger('render.default.error', $error, $method, $path, $cms); // default error is on you
	}

	function __invoke($method, $path, $cms) {
		$this->cms = $cms = $this;

		// inclide prepend PHP file first
		is_file($php = \dir\content($path . '/index.php')) ? include_once $php : null ||
		is_file($php = \dir\content($path . '.php')) ? include_once $php : null;

		// search page (html, md, latte, phtml)
		$this->page = Page::fromPath(\dir\content() . '/' . $path, (array)config()->meta);

		// include functions.php from $path and working directory
		is_file($php = \dir\content($path . '/functions.php')) ? include_once $php : null;
		is_file(getcwd() . '/functions.php') ? include_once getcwd() . '/functions.php' : null;

		if ($this->page) {
			echo ensure('render.page', [$this, 'render'], $this); // render page
		} else {
			\route\error(404, $method, $path, $this); // trigger router error
		}
	}
}