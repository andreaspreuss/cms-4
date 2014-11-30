<?php
namespace vestibulum;

/**
 * Return config data.
 *
 * @property string $title
 * @return \stdClass
 */
function config() {
	static $config;

	return $config ? $config : $config = (object)array_replace_recursive(
		[
			'title' => 'Vestibulum',
			'cache' => false,
			'content' => getcwd() . '/content/',
			'meta' => [
				'template' => 'index.latte',
			]
		],
		@include(getcwd() . '/config.php') // intentionally @
	);
}

/**
 * Return content directory path.
 *
 * @param null $path
 * @return bool
 */
function content($path = null) {
	return realpath(isset(config()->content) ? config()->content : (config()->content = getcwd() . '/content/')) . $path;
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
 * Read content from cached file.
 *
 * @param string|callable $file
 * @param mixed|callable $data
 * @param null|int|bool|callable $expire
 * @param null|callable $filter
 * @return mixed|null|string
 */
function cache($file, $data, $expire = null, $filter = null) {

	// Getting cached file name
	$file = is_callable($file) ? call_user_func($file) : $file;

	// Expire cached file content?
	$expire = is_bool($expire) ? $expire : is_null($expire) ||
		(is_int($expire) && @filemtime($file) + $expire < time()) ||
		(is_callable($expire) && call_user_func($expire, $file));

	if ($expire || !is_file($file)) {
		$data = is_callable($data) ? call_user_func($data, $file) : $data;
		@file_put_contents($file, $data);
	} else {
		$data = @file_get_contents($file);
	}

	return is_callable($filter) ? call_user_func($filter, $data) : $data;
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

/**
 * @return bool
 */
function isAjax() {
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}

/**
 * @return bool
 */
function isPost() {
	return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * @return bool
 */
function isGet() {
	return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Make HTTP redirect
 *
 * @param string $path
 * @param int $code
 * @param bool $halt
 */
function redirect($path, $code = 302, $halt = true) {
	header("Location: {$path}", true, $code);
	$halt && exit;
}

/**
 * Return URL
 *
 * @param null $url
 * @param null $src
 * @return string
 */
function url($url = null, $src = null) {
	if (is_string($url) || is_null($url)) {

		return (isset($_SERVER['HTTPS']) && strcasecmp(
			$_SERVER['HTTPS'], 'off'
		) ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
			(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '')) .
		($_SERVER["SERVER_PORT"] == '80' ? null : ':' . $_SERVER["SERVER_PORT"]) . '/' . ltrim(
			parse_url($url, PHP_URL_PATH), '/'
		);

	} elseif ($url instanceof Page) {
		return $url->getSlug($src ? $src : content());
	}
}

/**
 * Prints out no-cache headers
 *
 * @param null $content
 * @return void
 */
function nocache($content = null) {
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']) . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	return $content and strlen($content) and (die($content));
}

/**
 * Send JSON data.
 *
 * @param $value
 * @param int $options
 * @param int $depth
 * @return bool
 */
function json($value, $options = 0, $depth = 512) {
	$json = json_encode($value, $options, $depth);
	$err = json_last_error();

	if ($err !== JSON_ERROR_NONE) {
		return trigger_error(
			__FUNCTION__ . ": JSON encoding failed [{$err}].",
			E_USER_ERROR
		);
	}

	header('Content-Type: application/json');
	return print $json;
}

/**
 * HTTP response status code.
 *
 * @param $code
 * @return int
 */
function status($code) {
	return http_response_code($code);
}

/**
 * Download selected file.
 *
 * @param $file
 * @param null $filename
 */
function download($file, $filename = null) {
	if (!is_file($file)) status(404) and die('File not found.'); // file not found
	header('Pragma: public');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . urlencode($filename ?: basename($file)));
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
	header('ETag: ' . md5(dirname($file)));
	header('ContentLength: ' . filesize($file));
	header('Connection: close');
	die(readfile($file));
}