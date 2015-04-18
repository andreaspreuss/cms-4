<?php
namespace cms;

use Latte\Engine;
use Latte\Macros\MacroSet;
use Latte\Runtime\Filters;

require_once __DIR__ . '/../vendor/latte/latte/src/latte.php';

/**
 * Multiple pages loaders
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Render {

	/**
	 * @param Content $content
	 * @return mixed|null|string
	 * @throws \Exception
	 */
	public function render(Content $content) {

		// HTTP status code
		if ($code = isset($content->page->status) ? $content->page->status : null) status($code);

		// PHTML file execute
		if ($content->page->is('phtml')) {
			extract(get_object_vars($content), EXTR_SKIP);
			ob_start();
			require $content->page;
			return ob_get_clean();
		}

		return latte()->renderToString($content->page, get_object_vars($content));
	}
}


/**
 * @return Engine
 */
function latte() {
	$latte = new Engine();
	$latte->setLoader(new FileLoader);
	$latte->setTempDirectory(tmp());
	$set = new MacroSet($latte->getCompiler());
	$set->addMacro('url', 'echo \cms\url(%node.args);');
	return filter('latte', $latte);
}

class FileLoader extends \Latte\Loaders\FileLoader {
	public function getContent($file) {

		$content = parent::getContent($file);
		$ext = pathinfo(strval($file), PATHINFO_EXTENSION);

		// replace {url} with current server URL
		if ($ext === 'md' || $ext === 'html') {
			$content = preg_replace_callback(
				"/{url\s?['\"]?([^\"'}]*)['\"]?}/", function ($m) {
				return Filters::safeUrl(url(end($m)));
			},
				$content
			);
		}
		if ($file instanceof Page) {
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