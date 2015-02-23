<?php
namespace cms;

use Latte\Engine;
use Latte\Macros\MacroSet;
use Latte\Runtime\Filters;

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

		// replace {url} with current server URL
		if ($content->page->is('md') || $content->page->is('html')) {
			$content->content = preg_replace_callback(
				"/{url\s?['\"]?([^\"'}]*)['\"]?}/", function ($m) {
					return Filters::safeUrl(url(end($m)));
				},
				$content->page->getContent()
			);
		}


		// Read markdown from cache or recompile
		if ($content->page->is('md')) {
			$content->content = cache(
				$file = tmp($content->page->getName() . '-' . md5($content->page) . '.html'),
				function () use ($content) {
					return \Parsedown::instance()->text($content->content);
				},
				$content->page->getMTime() > @filemtime($file)
			);
		}

		$template = pathinfo($content->page->template, PATHINFO_EXTENSION);

		// phtml - for those who have an performance obsession :-)
		if ($template === 'phtml' || $template === 'php') {
			extract(get_object_vars($content), EXTR_SKIP);
			ob_start();
			require $content->page->template;
			return ob_get_clean();
		}

		// Latte - for lazy people :-)
		if ($template === 'latte') {
			$latte = latte();
			if ($content->page->is('latte')) {
				$content->content = $latte->renderToString($content->page, get_object_vars($content));
			}
			return $latte->renderToString($content->page->template, get_object_vars($content));
		}

		return $content->content;
	}
}

/**
 * @return Engine
 */
function latte() {
	$latte = new Engine();
	$latte->setTempDirectory(tmp());
	$set = new MacroSet($latte->getCompiler());
	$set->addMacro('url', 'echo \cms\url(%node.args);');

	return filter('latte', $latte);
}