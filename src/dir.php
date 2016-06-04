<?php

namespace dir {

	use function app\config as config;

	/**
	 * Return cache directory path.
	 *
	 * @param null $path
	 * @return bool|string
	 */
	function cache($path = null) {
		return isset(config()->cache) && is_dir(config()->cache) ? config()->cache . '/' . $path : false;
	}

	/**
	 * Return pages directory.
	 *
	 * @param null|string $path
	 * @return string
	 */
	function content($path = null) {
		$content = isset(config()->content) ? config()->content : (config()->content = getcwd() . '/pages/');
		return realpath($content . '/' . $path);
	}

	/**
	 * Sphido CMS source code directory.
	 *
	 * @param null|string $path
	 * @return string
	 */
	function src($path = null) {
		return realpath(__DIR__ . '/' . $path);
	}

	/**
	 * Sphido CMS root directory.
	 *
	 * @param null|string $path
	 * @return string
	 */
	function root($path = null) {
		return realpath(dirname(__DIR__) . '/' . $path);
	}

}