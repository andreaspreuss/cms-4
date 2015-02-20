<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
\Tester\Environment::setup();

require_once __DIR__ . '/../src/sphido.php';
require_once __DIR__ . '/../src/functions.php';

$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_NAME'] = 'test';
$_SERVER['SERVER_PORT'] = '123456';


// url.base filter

add_filter(
	'url.base', function (Url $url) {
	Assert::same($url->port, 123456);
	$url->port = 80;
	Assert::same($url->path, '/');
	Assert::same($url->scheme, 'https');
	$url->scheme = 'http';

	return $url;
}
);

Assert::same('http://test/some/path/index.html', \vestibulum\url('/some/path/index.html')->__toString());
Assert::same('http://test/a/b?a=b', '' . \vestibulum\url('/a/b')->query(['a' => 'b']));

// url filter

add_filter(
	'url', function (Url $url) {
	$url->path('/prefix/' . ltrim($url->path, '/'));
	return $url;
}
);
Assert::same('http://test/prefix/a/b/c/d', '' . \vestibulum\url('/a/b/c/d'));