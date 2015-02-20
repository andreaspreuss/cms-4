<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
\Tester\Environment::setup();

require_once __DIR__ . '/../src/sphido.php';
require_once __DIR__ . '/../src/functions.php';

$_SERVER['HTTPS'] = 'off';
$_SERVER['SERVER_NAME'] = 'test';
$_SERVER['SERVER_PORT'] = '80';

{ // basic URL test
	Assert::same('http://test/', strval(\vestibulum\url()));
	Assert::same('http://test/custom/url', strval(\vestibulum\url('custom/url')));
	Assert::same('http://test/custom.file.ext', strval(\vestibulum\url('custom.file.ext')));
}

{ // any others url
	Assert::same('http://test/custom/url', strval(\vestibulum\url('/////custom/url'))); // not path
	Assert::same('http://test/custom/url////', strval(\vestibulum\url('/custom/url////'))); // not path
}