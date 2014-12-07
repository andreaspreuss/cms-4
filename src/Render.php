<?php
namespace vestibulum;

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
	 * @param Vestibulum $cms
	 * @return mixed|null|string
	 * @throws \Exception
	 */
	public function render(Vestibulum $cms) {
		// HTTP status code
		if ($code = isset($cms->page->status) ? $cms->page->status : null) status($code);

		// PHTML file execute
		if ($cms->page->is('phtml')) {
			extract(get_object_vars($cms), EXTR_SKIP);
			ob_start();
			require $cms->page;
			return ob_get_clean();
		}

		$cms->content = filter('content', $cms->page->getContent(), $cms);

		// Read markdown from cache or recompile
		if ($cms->page->is('md')) {
			$cms->content = cache(
				$file = tmp($cms->page->getName() . '-' . md5($cms->page) . '.html'),
				function () use ($cms) {
					return \Parsedown::instance()->text($cms->content);
				},
				$cms->page->getMTime() > @filemtime($file)
			);
		}

		$template = pathinfo($cms->page->template, PATHINFO_EXTENSION);

		// phtml - for those who have an performance obsession :-)
		if ($template === 'phtml' || $template === 'php') {
			extract(get_object_vars($cms), EXTR_SKIP);
			ob_start();
			require $cms->page->template;
			return ob_get_clean();
		}

		// Latte - for lazy people :-)
		if ($template === 'latte') {
			$latte = $cms->getLatte();
			if (isset($cms->page->latte) || $cms->page->is('latte')) {
				$cms->content = $latte->renderToString($cms->page, get_object_vars($cms));
			}
			return $latte->renderToString($cms->page->template, get_object_vars($cms));
		}

		return $cms->content;
	}

	/**
	 * @return Engine
	 */
	public function getLatte() {
		$latte = new Engine();
		$latte->setTempDirectory(tmp());

		$set = new MacroSet($latte->getCompiler());
		$set->addMacro('url', 'echo \vestibulum\url(%node.args);');

		return filter('latte', $latte);
	}
}

/* default filters */

add_filter(
	'content', function ($content, Vestibulum $cms) {
		return 'aaa';

		// replace {url} with current server URL
		if ($cms->page->is('md') || $cms->page->is('html')) {
			return preg_replace_callback(
				"/{url\s?['\"]?([^\"'}]*)['\"]?}/", function ($m) {
					return Filters::safeUrl(url(end($m)));
				},
				$content
			);
		}

		return $content;
	}
);

add_filter(
	'content', function ($content, Vestibulum $cms) {

	}
);