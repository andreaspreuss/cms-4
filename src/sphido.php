<?php
/** @author Roman Ozana <ozana@omdesign.cz> */
/**
 * Basic config function.
 *
 * @return object
 */
function config() {
	static $config;
	if ($config) return $config;
	return $config = (object)call_user_func_array('\array_replace_recursive', func_get_args());
}

/**
 * @return object
 */
function routes() {
	static $routes;
	return (!$routes) ? $routes = (object)['any' => [], 'all' => [], 'error' => []] : $routes;
}

/**
 * Function for mapping actions to routes.
 */
function map() {
	$argv = func_get_args();

	// try to figure out how we were called
	switch (count($argv)) {
		// complete params (method, path, handler)
		case 3:
			foreach ((array)$argv[0] as $verb)
				routes()->{strtoupper($verb)}[] = ['/' . trim($argv[1], '/'), $argv[2]];
			break;
		// either (path, handler) or (code, handler)
		case 2:
			$argv[0] = (array)$argv[0];
			if (ctype_digit($argv[0][0])) {
				foreach ($argv[0] as $code)
					routes()->error[$code] = $argv[1];
			} else {
				foreach ($argv[0] as $path)
					routes()->any[] = ['/' . trim($path, '/'), $argv[1]];
			}
			break;
		// any method and any path (just one for this, replace ref)
		case 1:
			routes()->all = $argv[0];
			break;
		// everything else
		default:
			throw new \BadFunctionCallException(
				'Invalid number of arguments.',
				500
			);
	}
}

/**
 * Handling all errors.
 *
 * @return mixed
 * @throws \BadFunctionCallException
 */
function error() {
	$argc = func_num_args();
	$argv = func_get_args();
	if (!$argc) {
		throw new \BadFunctionCallException(
			'Invalid number of arguments.',
			500
		);
	}
	$code = $argv[0];
	$func = (
	isset(routes()->error[$code]) ?
		routes()->error[$code] :
		function ($code) {
			return http_response_code($code);
		}
	);
	http_response_code($code);
	return call_user_func_array($func, $argv);
}

/**
 * Return current request method.
 *
 * @return string
 */
function method() {
	$method = strtoupper($_SERVER['REQUEST_METHOD']);

	// override POST method
	if ($method === 'POST') {
		if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
			$method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
		} else {
			$method = isset($_POST['_method']) ? strtoupper($_POST['_method']) : $method;
		}
	}
	return $method;
}

/**
 * Dispatch current request.
 *
 * @return mixed
 */
function dispatch() {
	$argv = func_get_args();
	$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
	$method = method();

	$pattern = null;
	$func = null;
	$vals = null;

	// getting all maps
	$maps = (array)routes()->any;
	if (isset(routes()->{$method})) {
		$maps = array_merge((array)routes()->{$method}, $maps);
	}

	// iterate over all maps
	foreach ($maps as $temp) {
		list($pattern, $callback) = $temp;

		$pattern = trim($pattern, '/');
		$pattern = preg_replace(
			[
				'@<([^:]+)>@U', # <param> => <param>[^/]+
				'@<([^:]+)(:(.+))?>@U', # <param:...> => (?<param>...)
			],
			[
				'<$1:[^/]+>',
				'(?<$1>$3)',
			],
			$pattern
		);

		// match current path with any maps callback
		if (preg_match('@^' . $pattern . '$@', $path, $vals)) {
			$func = $callback;
			break;
		}
	}

	// valid handler, try to parse out route symbol values
	if ($func && is_callable($func)) {

		array_shift($vals); // remove top group from vals
		// extract route symbols and run the hook()s
		if ($vals) {
			// extract any route symbol values
			$toks = array_filter(array_keys($vals), 'is_string');
			$vals = array_map(
				'urldecode',
				array_intersect_key(
					$vals,
					array_flip($toks)
				)
			);

			array_unshift($argv, $vals);
		}
	} else {
		if (is_callable(routes()->all)) {
			$argv = array_merge(['method' => $method, 'path' => $path], $argv);
			return call_user_func_array(routes()->all, $argv);
		} else {
			$func = __NAMESPACE__ . '\error';
			array_unshift($argv, 404);
		}
	}

	return call_user_func_array($func, $argv);
}


/**
 * Return events object
 *
 * @return stdClass
 */
function events() {
	static $events;
	return $events ?: $events = new stdClass();
}

/**
 * Return listeners
 *
 * @param $event
 * @return mixed
 */
function listeners($event) {
	if (isset(events()->$event)) {
		ksort(events()->$event);
		return call_user_func_array('array_merge', events()->$event);
	}
}

/**
 * Add event listener
 *
 * @param $event
 * @param callable $listener
 * @param int $priority
 */
function on($event, callable $listener = null, $priority = 10) {
	events()->{$event}[$priority][] = $listener;
}

/**
 * Trigger only once.
 *
 * @param $event
 * @param callable $listener
 * @param int $priority
 */
function once($event, callable $listener, $priority = 10) {
	$onceListener = function () use (&$onceListener, $event, $listener) {
		off($event, $onceListener);
		call_user_func_array($listener, func_get_args());
	};

	on($event, $onceListener, $priority);
}

/**
 * Remove one or all listeners from event.
 *
 * @param $event
 * @param callable $listener
 * @return bool
 */
function off($event, callable $listener = null) {
	if (!isset(events()->$event)) return;

	if ($listener === null) {
		unset(events()->$event);
	} else {
		foreach (events()->$event as $priority => $listeners) {
			if (false !== ($index = array_search($listener, $listeners, true))) {
				unset(events()->{$event}[$priority][$index]);
			}
		}
	}

	return true;
}

/**
 * Trigger events
 *
 * @param $event
 * @return array
 */
function fire($event) {
	$args = func_get_args();
	$event = array_shift($args);

	$out = [];
	foreach ((array)listeners($event) as $listener) {
		if (($out[] = call_user_func_array($listener, $args)) === false) break; // return false ==> stop propagation
	}

	return $out;
}

/**
 * Care about something
 *
 * @param string $event
 * @param callable $listener
 * @return mixed
 */
function handle($event, callable $listener = null) {
	if ($listener) on($event, $listener, 0); // register default listener

	if ($listeners = listeners($event)) {
		return call_user_func_array(end($listeners), array_slice(func_get_args(), 2));
	}
}


/**
 * Pass variable with all filters.
 *
 * @param $event
 * @param null $value
 * @return mixed|null
 */
function filter($event, $value = null) {
	$args = func_get_args();
	$event = array_shift($args);

	foreach ((array)listeners($event) as $listener) {
		$args[0] = $value;
		$value = call_user_func_array($listener, $args);
	}

	return $value;
}

// ---------------------------------------------------- aliases ---------------------------------------------------- //

/**
 * Trigger an action.
 *
 * @param $event
 * @return mixed
 */
function action($event) {
	return call_user_func_array('\fire', func_get_args());
}

/**
 * Trigger an action.
 *
 * @param $event
 * @return mixed
 */
function trigger($event) {
	return call_user_func_array('\fire', func_get_args());
}

/**
 * @param $event
 * @param callable $listener
 * @param int $priority
 */
function add_action($event, callable $listener, $priority = 10) {
	on($event, $listener, $priority);
}

/**
 * @param $event
 * @param callable $listener
 * @param int $priority
 */
function add_listener($event, callable $listener, $priority = 10) {
	on($event, $listener, $priority);
}

/**
 * @param $event
 * @param callable $listener
 * @param int $priority
 */
function add_filter($event, callable $listener, $priority = 10) {
	on($event, $listener, $priority);
}

/**
 * Obfuscate email addresses and protect them against SPAM bots
 *
 * @param string $email
 * @param string $text
 * @param string $format
 * @return string
 */
function antispam($email, $text = null, $format = '<a href="mailto:%s" rel="nofollow">%s</a>') {
	return jsProtect(sprintf($format, $email, $text ?: $email)) .
	'<noscript><span style="unicode-bidi: bidi-override; direction: rtl;">' . strrev($email) . '</span></noscript>';
}

/**
 * Perform the rot13 transform on a string and then decode back with Javascript.
 *
 * @param string $string
 * @return string
 */
function jsProtect($string) {
	return '<script type="text/javascript">/* <![CDATA[ */document.write("' .
	addslashes(
		str_rot13($string)
	) . '".replace(/[a-zA-Z]/g,function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));/* ]]> */</script>';
}

/**
 * @method Url scheme(string $scheme)
 * @method Url host(string $host)
 * @method Url port(int $port)
 * @method Url user(string $user)
 * @method Url pass(string $pass)
 * @method Url query(array $query)
 * @method Url fragment(string $fragment)
 */
class Url {

	public $scheme = '';
	public $host = '';
	public $port = '';
	public $user = '';
	public $pass = '';
	public $path = '';
	public $query = [];
	public $fragment = '';
	public static $ports = [
		'http' => 80,
		'https' => 443,
		'ftp' => 21,
		'news' => 119,
		'nntp' => 119,
	];

	public function __construct($url = null) {
		if ($u = parse_url($url)) {
			$this->scheme = isset($u['scheme']) ? $u['scheme'] : '';
			$this->port = isset($u['port']) ? $u['port'] : (isset(self::$ports[$this->scheme]) ? self::$ports[$this->scheme] : null);
			$this->host = isset($u['host']) ? rawurldecode($u['host']) : '';
			$this->user = isset($u['user']) ? rawurldecode($u['user']) : '';
			$this->pass = isset($u['pass']) ? rawurldecode($u['pass']) : '';
			$this->fragment = isset($u['fragment']) ? rawurldecode($u['fragment']) : '';
			$this->path(isset($u['path']) ? $u['path'] : '');
			isset($u['query']) ? parse_str($u['query'], $this->query) : null;
		} elseif ($url instanceof self) {
			foreach ($url as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}

	public function path($path) {
		$this->path = ($this->scheme === 'https' || $this->scheme === 'http' ? '/' . ltrim($path, '/') : $path);
		return $this;
	}

	/** @return self */
	public static function current($uri = null) {
		$url = new Url($uri === null && isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $uri);
		$url->scheme = (isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off') ? 'https' : 'http');
		$url->host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : ''));
		$url->port = isset($_SERVER['SERVER_PORT']) ? intval($_SERVER['SERVER_PORT']) : null;
		return $url;
	}

	/** @return self */
	public function __call($name, $arguments) {
		$this->{$name} = reset($arguments);
		return $this;
	}

	function __toString() {
		return
			($this->scheme ? $this->scheme . ':' : '') . '//' .
			($this->user !== '' ? rawurlencode($this->user) . ($this->pass === '' ? '' : ':' . rawurlencode(
						$this->pass
					)) . '@' : '') .
			($this->host) .
			($this->port && (!isset(self::$ports[$this->scheme]) || $this->port !== self::$ports[$this->scheme]) ? ':' . $this->port : '') .
			($this->path) .
			(($q = http_build_query($this->query, '', '&', PHP_QUERY_RFC3986)) ? '?' . $q : '') .
			($this->fragment === '' ? '' : '#' . $this->fragment);
	}
}

/**
 * Return current URL with get query.
 *
 * @param  null|string $slug
 * @param array $query
 * @return string
 */
function url($slug = null, array $query = []) {
	static $url = null;
	if ($url === null) $url = strval(Url::current('/'));
	return $url . ltrim($slug, '/') . (($q = http_build_query($query, '', '&', PHP_QUERY_RFC3986)) ? '?' . $q : '');
}
