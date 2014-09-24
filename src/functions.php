<?php
namespace vestibulum;

/**
 * Return config data
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
			'src' => getcwd() . '/src/',
			'meta' => [
				'template' => 'index.latte',
			]
		],
		@include(getcwd() . '/config.php') // intentionally @
	);
}

/**
 * Return current request URL
 *
 * @return mixed
 */
function request() {
	static $request;

	if ($request) return $request;

	// base directory detection
	$base = rtrim(strtr(dirname($_SERVER['SCRIPT_NAME']), '\\', '/'), '/');

	# current requested URI
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

	return $request = preg_replace('@^' . preg_quote($base) . '@', '', $path);
}

/**
 * Return SRC directory path
 *
 * @param null $path
 * @return bool
 */
function src($path = null) {
	return realpath(isset(config()->src) ? config()->src : (config()->src = getcwd() . '/src/')) . $path;
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
 * Make HTTP redirect
 *
 * @param string $path
 * @param int $code
 * @param bool $condition
 */
function redirect($path, $code = 302, $condition = true) {
	if (!$condition) return;
	@header("Location: {$path}", true, $code);
	exit;
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

	} elseif ($url instanceof File) {
		return $url->getSlug($src ? $src : src());
	}
}

/**
 * Spit headers that force cache volatility.
 *
 * @return void
 */
function nocache() {
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
}

/**
 * Send JSON  formated data.
 *
 * @param $value
 * @param int $code
 * @param int $options
 * @param int $depth
 */
function json($value, $code = 200, $options = 0, $depth = 512) {
	nocache();
	header('Content-Type: application/json');
	header((isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.1") . " " . $code, true, $code);
	die(json_encode($value, $options, $depth));
}

/**
 * Download file
 *
 * @param $file
 * @param null $filename
 */
function download($file, $filename = null) {
	if (!is_file($file)) {
		header((isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.1") . " " . 404, true, 404);
		die('File not found.');
	}

	header('Pragma: public');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . urlencode($filename ?: basename($file)));
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
	header('ETag: ' . md5(dirname($file)));
	header('ContentLength: ' . filesize($file));
	header('Connection: close');
	die(readfile($file));
}