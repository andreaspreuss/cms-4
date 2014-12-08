<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
\Tester\Environment::setup();

require_once __DIR__ . '/../src/events.php';
require_once __DIR__ . '/../src/functions.php';

$_SERVER['HTTPS'] = 'off';
$_SERVER['SERVER_NAME'] = 'test';
$_SERVER['SERVER_PORT'] = '80';

{ // basic URL test
	Assert::same('http://test/', \vestibulum\url());
	Assert::same('http://test/custom/url', \vestibulum\url('custom/url'));
	Assert::same('http://test/custom.file.ext', \vestibulum\url('custom.file.ext'));
}

{ // any others url
	Assert::same('http://test/', \vestibulum\url('/////custom/url')); // not path
	Assert::same('http://test/custom/url////', \vestibulum\url('/custom/url////')); // not path
}

{ // test HTTPS
	$_SERVER['HTTPS'] = 'on';
	Assert::same('https://test/', \vestibulum\url());
	Assert::same('https://test/custom/url', \vestibulum\url('custom/url'));
	Assert::same('https://test/custom.file.ext', \vestibulum\url('custom.file.ext'));
	$_SERVER['HTTPS'] = 'off';
}

{ // different port
	$_SERVER['SERVER_PORT'] = '8080';
	Assert::same('http://test:8080/', \vestibulum\url());
	Assert::same('http://test:8080/custom/url', \vestibulum\url('custom/url'));
	Assert::same('http://test:8080/custom.file.ext', \vestibulum\url('custom.file.ext'));
	$_SERVER['SERVER_PORT'] = '80';
}

{ // URL filter change
	add_filter(
		'url', function ($url, $slug, $server) {
			Assert::same('http://test/path/slug.file.ext', $url);
			Assert::same('/path/slug.file.ext', $slug);
			Assert::same('http://test', $server);

			return 'change everything';
		}
	);
	Assert::same('change everything', \vestibulum\url('/path/slug.file.ext'));
}