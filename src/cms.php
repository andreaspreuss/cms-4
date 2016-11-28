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
require_once __DIR__ . '/../vendor/sphido/parsers/src/MetadataParser.php';
require_once __DIR__ . '/../vendor/sphido/parsers/src/TitleParser.php';
require_once __DIR__ . '/MissingPage.php';
require_once __DIR__ . '/Pages.php';
require_once __DIR__ . '/Render.php';
require_once __DIR__ . '/Page.php';


/**
 * Sphido:  A rocket fast flat file blog & CMS
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
class Sphido extends \stdClass {
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

		\route\map([404, 500], new MissingPage); // add error handler
		\route\map(filter(Sphido::class, $this)); // pages handler
	}

	function __invoke($method, $path, $cms) {
		$this->cms = $cms = $this;

		// include prepend PHP file first
		is_file($php = \dir\content($path . '/index.php')) ? include_once $php : null ||
		is_file($php = \dir\content($path . '.php')) ? include_once $php : null;

		// search page (html, md, latte, phtml)
		$this->page = Page::fromPath(\dir\content() . '/' . $path, (array)config()->meta);

		// include functions.php from $path and working directory
		is_file($php = \dir\content($path . '/functions.php')) ? include_once $php : null;
		is_file(getcwd() . '/functions.php') ? include_once getcwd() . '/functions.php' : null;

		print $this->page ? $this->render() : \route\error(404, $method, $path, $this);
	}

	/**
	 * @return mixed|null|string
	 * @throws \Exception
	 */
	public function render() {

		trigger(Sphido::class . '::' . __FUNCTION__, $this->page, $this);

		// HTTP status code
		if ($code = isset($this->page->status) ? $this->page->status : null) http_response_code($code);

		// PHTML file execute
		if ($this->page->is('phtml')) {
			extract(get_object_vars($this), EXTR_SKIP);
			ob_start();
			require $this->page;
			return ob_get_clean();
		}

		return latte()->renderToString($this->page, get_object_vars($this));
	}

}