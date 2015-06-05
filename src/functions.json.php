<?php
/** @author Roman Ozana <ozana@omdesign.cz> */

namespace {
	/**
	 * Maps directly to json_encode(), but renders JSON headers as well.
	 *
	 * @param $value
	 * @param int $options
	 * @param int $depth
	 * @return bool
	 */
	function json($value, $options = 0, $depth = 512) {
		$json = call_user_func_array('json_encode', func_get_args());
		$err = json_last_error();
		if ($err !== JSON_ERROR_NONE) {
			throw new \RuntimeException(
				"JSON encoding failed [{$err}].",
				500
			);
		}
		if (!headers_sent()) header('Content-type: application/json');
		return print $json;
	}
}

namespace json {

	/**
	 * Create JSON ok response.
	 *
	 * @param string|null $message
	 * @param array $data
	 * @return bool|int
	 */
	function ok($message = null, $data = []) {
		return json(array_merge(['ok' => true, 'error' => false], ($message ? ['message' => $message] : []), $data));
	}


	/**
	 * Create JSON error response.
	 *
	 * @param $message
	 * @param int|null $code
	 * @param array $data
	 * @return bool|int
	 */
	function error($message = null, $code = 501, $data = []) {
		if (is_int($code)) http_response_code($code);
		return json(array_merge(['error' => true, 'ok' => false], ($message ? ['message' => $message] : []), $data));
	}
}
