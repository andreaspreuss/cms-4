<?php
namespace cms {

	/**
	 * Return current URL path.
	 *
	 * @param null|string $slug
	 * @return \Url
	 */
	function url($slug = null) {
		static $url = null;
		if ($url === null) $url = filter('url.base', \Url::current('/'));
		return filter('url', clone $url->path($slug));
	}

	/**
	 * Return content directory path.
	 *
	 * @param null $path
	 * @return bool
	 */
	function content($path = null) {
		$content = isset(config()->content) ? config()->content : (config()->content = getcwd() . '/content/');
		return realpath($content) . '/' . $path;
	}

	/**
	 * Return tmp directory path.
	 *
	 * @param null $path
	 * @return bool|string
	 */
	function tmp($path = null) {
		return isset(config()->cache) && config()->cache ? realpath(config()->cache) . '/' . $path : false;
	}

	/**
	 * Return current request URL segment
	 *
	 * @return mixed
	 */
	function request() {
		static $request;

		if ($request !== null) return $request;

		// base directory detection
		$base = rtrim(strtr(dirname($_SERVER['SCRIPT_NAME']), '\\', '/'), '/');

		# current requested URI
		$path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

		return $request = preg_replace('@^' . preg_quote($base) . '@', '', $path);
	}
}