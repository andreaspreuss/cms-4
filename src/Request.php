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
		if ($this->request) return $this->request;

		# current requested URI
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		// base directory detection
		$base = rtrim(strtr(dirname($_SERVER['SCRIPT_NAME']), '\\', '/'), '/');
		$path = preg_replace('@^' . preg_quote($base) . '@', '', $path);

		return $this->request = $path;
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

