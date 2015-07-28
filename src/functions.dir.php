<?php

namespace dir {

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
	 * Vendor
	 *
	 * @param null $path
	 * @return string
	 */
	function vendor($path = null) {
		return realpath(__DIR__ . '/../vendor/' . $path);
	}
}