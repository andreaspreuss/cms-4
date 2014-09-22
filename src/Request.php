<?php
namespace vestibulum;

/**
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Request {

	/** @var string */
	private $request;

	/**
	 * Return requested URL path
	 *
	 * @return mixed
	 */
	public function getRequest() {
		if (isset($this->request) || !isset($_SERVER['REQUEST_URI'])) return $this->request;
		return $this->request = preg_replace(['#\?.*#', '#/?index.php#'], ['', ''], urldecode($_SERVER['REQUEST_URI']));
	}

	/**
	 * Return server URL
	 *
	 * @param null $url
	 * @return string
	 */
	public static function url($url = null) {
		return (isset($_SERVER['HTTPS']) && strcasecmp(
			$_SERVER['HTTPS'], 'off'
		) ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
			(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '')) .
		($_SERVER["SERVER_PORT"] == '80' ? null : ':' . $_SERVER["SERVER_PORT"]) . '/' . ltrim(
			parse_url($url, PHP_URL_PATH), '/'
		);
	}
}

/**
 * @return bool
 */
function isAjax() {
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(
		$_SERVER['HTTP_X_REQUESTED_WITH']
	) === 'xmlhttprequest';
}

/**
 * @return bool
 */
function isPost() {
	return strtolower($_SERVER['REQUEST_METHOD']) === 'post';
}