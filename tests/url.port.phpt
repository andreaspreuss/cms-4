<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
\Tester\Environment::setup();

require_once __DIR__ . '/../src/functions.php';

$_SERVER['HTTPS'] = 'off';
$_SERVER['SERVER_NAME'] = 'test';
$_SERVER['SERVER_PORT'] = '80';

Assert::same('http://test/', strval(\cms\url()));
Assert::same('http://test/custom/url', strval(\cms\url('custom/url')));
Assert::same('http://test/custom.file.ext', strval(\cms\url('custom.file.ext')));
Assert::same('http://test/custom/url', strval(\cms\url('/////custom/url'))); // not path
Assert::same('http://test/custom/url////', strval(\cms\url('/custom/url////'))); // not path
