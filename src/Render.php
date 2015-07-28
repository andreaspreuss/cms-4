<?php
namespace cms;

use Latte\Engine;
use Latte\Macros\MacroSet;

require_once \dir\vendor('latte/latte/src/latte.php');

/**
 * @return Engine
 */
function latte() {

	$latte = new Engine();
	$latte->setLoader(filter('latte.loader', new FileLoader()));
	$latte->setTempDirectory(\dir\cache());
	$latte->addFilter('md', '\cms\md');
	trigger('latte.macroset', new MacroSet($latte->getCompiler()));
	return filter('latte', $latte);
}

/*
 function is_true($val, $return_null = false) {
	$bool = (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool)$val);
	return ($bool === null && !$return_null ? false : $bool);
}
*/

function md($content, $cache = '') {
	require_once \dir\vendor('erusev/parsedown/Parsedown.php');
	return \Parsedown::instance()->text($content);
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
			//if (isset($file->syntax)) $content = '<div n:syntax="' . $file->syntax .'" n:tag-if="false">' .$content . '</div>';
			if ($ext === 'md') $content = '{block|md}' . $content . '{/block}';
			if (strpos($content, '{block content') === false) $content = '{block content}' . $content . '{/block}';
			if (strpos($content, '{layout') === false) $content = "{layout '$file->template'}" . $content;
		}
		//echo '<pre>' . htmlentities($content);die(); // debug
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

/**
 * @param MacroSet $set
 */
function add_default_macros(MacroSet $set) {
	$set->addMacro('url', 'echo \cms\url(%node.args);');
}

on('latte.macroset', '\cms\add_default_macros');
