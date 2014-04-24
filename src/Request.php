<?php
namespace vestibulum;

/**
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Request {

	/** @var string */
	private $request;

	/**
	 * Return requested URL
	 *
	 * @return mixed
	 */
	public function getRequest() {
		if (isset($this->request)) return $this->request;
		$this->request = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
		return $this->request = preg_replace(['#\?.*#', '#/?index.php#'], ['', ''], urldecode($this->request));
	}

	/**
	 * Return current URL
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
