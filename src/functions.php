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
}