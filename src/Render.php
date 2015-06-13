<?php
namespace cms;

use Latte\Engine;
use Latte\Macros\MacroSet;
use Latte\Runtime\Filters;

require_once __DIR__ . '/../vendor/latte/latte/src/latte.php';

/**
 * @return Engine
 */
function latte() {
	$latte = new Engine();
	$latte->setLoader(filter('latte.loader', new FileLoader));
	$latte->setTempDirectory(tmp());
	trigger('latte.macroset', new MacroSet($latte->getCompiler()));
	return filter('latte', $latte);
}

/**
 * Solve (html, md, latte) file loading
 *
 * @package cms
 */
class FileLoader extends \Latte\Loaders\FileLoader {
	/**
	 * @param $file
	 * @return mixed|string
	 */
	public function getContent($file) {

		$ext = pathinfo(strval($file), PATHINFO_EXTENSION);
		$content = filter('content', parent::getContent($file), $file, $ext);

		// Try render page
		if ($file instanceof Page) {
			/** @var Page $file */
			switch ($ext) {
				case 'html':
					return "{layout '$file->template'}{block content}{syntax off}" . $content;
				case 'md':
					require_once __DIR__ . '/../vendor/erusev/parsedown/Parsedown.php';
					return "{layout '$file->template'}{block content}{syntax off}" . \Parsedown::instance()->text($content);
					break;
				case 'latte':
					if (strpos($content, '{block') === false) $content = '{block content}' . $content;
					if (strpos($content, '{layout') === false) $content = "{layout '$file->template'}" . $content;
					return $content;
			}
		}
		return $content;
	}
}


/**
 * Multiple pages loaders
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Render {

	/**
	 * @param Sphido $cms
	 * @return mixed|null|string
	 * @throws \Exception
	 */
	public function render(Sphido $cms) {

		// HTTP status code
		if ($code = isset($cms->page->status) ? $cms->page->status : null) http_response_code($code);

		// PHTML file execute
		if ($cms->page->is('phtml')) {
			extract(get_object_vars($cms), EXTR_SKIP);
			ob_start();
			require $cms->page;
			return ob_get_clean();
		}

		return latte()->renderToString($cms->page, get_object_vars($cms));
	}
}

// TODO namespaces for functions and filters

/**
 * @param $content
 * @param $file
 * @param $ext
 * @return mixed
 */
function replace_url($content, $file, $ext) {
// replace {url} with current server URL
	if ($ext === 'md' || $ext === 'html') {
		$content = preg_replace_callback(
			"/{url\s?['\"]?([^\"'}]*)['\"]?}/", function ($m) {
			return Filters::safeUrl(url(end($m)));
		},
			$content
		);
	}

	return $content;
}

add_filter('content', '\cms\replace_url');

/**
 * @param MacroSet $set
 */
function add_default_macros(MacroSet $set) {
	$set->addMacro('url', 'echo \cms\url(%node.args);');
}

on('latte.macroset', '\cms\add_default_macros');
