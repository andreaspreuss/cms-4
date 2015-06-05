<?php
/** @author Roman Ozana <ozana@omdesign.cz> */

namespace http {

	/**
	 * Shortcut for http_response_code().
	 *
	 * @param $code
	 * @return int
	 */
	function status($code) {
		return http_response_code($code);
	}

	/**
	 * Shortcut for dumping a redirect header (no longer exits).
	 *
	 * @param $path
	 * @param int $code
	 * @param bool $halt
	 */
	function redirect($path, $code = 302, $halt = false) {
		header("Location: {$path}", true, $code);
		$halt && exit;
	}

	/**
	 * Prints out no-cache headers.
	 */
	function nocache() {
		header('Expires: Tue, 13 Mar 1979 18:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']) . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}

	/**
	 * Returns the value for an http request header, or sets an http
	 * response header (maps to php's header function)
	 *
	 * @param null $key
	 * @param $args
	 * @return array|null
	 */
	function headers($key = null, ...$args) {
		static $headers = null;

		if ($headers === null) {
			foreach ($_SERVER as $name => $data) {
				if (substr($name, 0, 5) === 'HTTP_') {
					$name = strtolower(substr($name, 5));
					$headers[$name] = $data;
				} else if ($name === 'CONTENT_LENGTH' || $name === 'CONTENT_MD5' || $name === 'CONTENT_TYPE') {
					$name = strtolower($name);
					$headers[$name] = $data;
				}
			}
		}

		if ($args) {
			header($key . ':' . implode($args)); // set headers by header('what', 'value');
			// $headers[] add to headers also...
		} else {
			return $key ? $headers[$key] : $headers; // return one header or all
		}
	}
}

namespace {

	/**
	 * Return GET parametters and check if exists.
	 *
	 * @param $key
	 * @return null|mixed
	 */
	function get($key) {
		return array_key_exists($key, $_GET) ? $_GET[$key] : null;
	}

	/**
	 * Return GET parametters and check if exists.
	 *
	 * @param $key
	 * @return null|mixed
	 */
	function post($key) {
		return array_key_exists($key, $_POST) ? $_POST[$key] : null;
	}


	/**
	 * Returns the best-guess remote address.
	 *
	 * @return string
	 */
	function ip() {
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		return $_SERVER['REMOTE_ADDR'];
	}


	/**
	 * Aaccessor for $_FILES, also consolidates array file uploads, but when using
	 * the files, be sure to use either is_uploaded_file() or move_uploaded_file()
	 * to ensure validity of file targets
	 *
	 * @param $name
	 * @return array|null
	 */
	function attachments($name) {
		static $cache = [];
		// return cached copy
		if (isset($cache[$name])) {
			return $cache[$name];
		}
		if (!isset($_FILES[$name])) {
			return null;
		}
		// single-file attachment (no need to cache)
		if (!is_array($_FILES[$name]['name'])) {
			return $_FILES[$name];
		}
		// attachment is an array
		$result = [];
		// consolidate file info
		foreach ($_FILES[$name] as $k1 => $v1)
			foreach ($v1 as $k2 => $v2)
				$result[$k2][$k1] = $v2;
		// cache and return array uploads
		return ($cache[$name] = $result);
	}

	/**
	 * Read request RAW body.
	 *
	 * @param bool $load
	 * @param string $pipe
	 * @return array
	 */
	function input($load = false, $pipe = 'php://input') {
		static $cache = null;
		# if called before, just return previous data
		if ($cache) {
			return $cache;
		}
		# do a best guess
		$content_type = (
		isset($_SERVER['HTTP_CONTENT_TYPE']) ?
			$_SERVER['HTTP_CONTENT_TYPE'] :
			$_SERVER['CONTENT_TYPE']
		);
		# try to load everything
		if ($load) {
			$content = file_get_contents($pipe);
			$content_type = preg_split('/ ?; ?/', $content_type);
			# type-content tuple
			return [$content_type, $content];
		}
		# create a temp file with the data
		$path = tempnam(sys_get_temp_dir(), 'disp-');
		$temp = fopen($path, 'w');
		$data = fopen($pipe, 'r');
		stream_copy_to_stream($data, $temp);
		fclose($temp);
		fclose($data);
		# type-path tuple
		return [$content_type, $path];
	}

}
