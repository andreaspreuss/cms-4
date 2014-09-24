<?php
namespace vestibulum;

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
 * HTTP redirect
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
 * FIXME found better way
 *
 * @param null $url
 * @param null $src
 * @return string
 */
function url($url = null, $src = null) {
	if (is_string($url) || is_null($url)) {
		return Request::url($url);
	} elseif ($url instanceof File) {
		return $url->getSlug($src);
	}
}

/**
 * @param $value
 * @param int $code
 * @param int $options
 * @param int $depth
 */
function json($value, $code = 200, $options = 0, $depth = 512) {

	header('Cache-Control: no-cache, must-revalidate');

	header('Content-Type: application/json');
	header((isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.1") . " " . $code, true, $code);
	die(json_encode($value, $options, $depth));
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

	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $file);
	finfo_close($finfo);

	$filename = $filename ?: basename($file);
	header(sprintf('Content-Type: %s; name="%s"', $mime, $filename));
	header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
	header('ContentLength: ' . filesize($file));
	header('Connection: close');

	die(readfile($file));
}