<?php
namespace cms;

use Latte\Engine;
use Latte\Macros\MacroSet;

require_once __DIR__ . '/../vendor/latte/latte/src/latte.php';

/**
 * @return Engine
 */
function latte() {
	$latte = new Engine();
	$latte->setLoader(filter(FileLoader::class, new FileLoader()));
	$latte->setTempDirectory(\dir\cache());
	$latte->addFilter('md', '\cms\md');
	trigger(MacroSet::class, new MacroSet($latte->getCompiler()));
	return filter(Engine::class, $latte);
}

/**
 * @param \Latte\Runtime\FilterInfo $info
 * @param $content
 * @return string
 */
function md(\Latte\Runtime\FilterInfo $info, $content) {
	require_once __DIR__ . '/../vendor/erusev/parsedown/Parsedown.php';
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
			if ($ext === 'html' && substr($content, 0, 15) === '<!doctype html>') return $content;
			if ($ext === 'md') $content = '{block|md}' . $content . '{/block}';
			if (strpos($content, '{block content') === false) $content = '{block content}' . $content . '{/block}';
			if (strpos($content, '{layout') === false) $content = "{layout '$file->template'}" . $content;
		}
		//echo '<pre>' . htmlentities($content);die(); // debug
		return $content;
	}
}