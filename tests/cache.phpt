<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/sphido.php';
require __DIR__ . '/../src/functions.php';

\Tester\Environment::setup();

// Callback data
{
	\vestibulum\cache(
		__DIR__ . '/.cache', function () {
			return 'example data callback';
		}
	);

	Assert::true(file_exists(__DIR__ . '/.cache'));
	Assert::same('example data callback', file_get_contents(__DIR__ . '/.cache'));
	unlink(__DIR__ . '/.cache');
}

// String data cache test
{
	\vestibulum\cache(
		__DIR__ . '/.cache', 'example data string'
	);

	Assert::true(file_exists(__DIR__ . '/.cache'));
	Assert::same('example data string', file_get_contents(__DIR__ . '/.cache'));
	unlink(__DIR__ . '/.cache');
}

{
	\vestibulum\cache(
		function () {
			return __DIR__ . '/.cache';
		}, 'example data'
	);

	Assert::true(file_exists(__DIR__ . '/.cache'));
	Assert::same('example data', file_get_contents(__DIR__ . '/.cache'));
	unlink(__DIR__ . '/.cache');
}


{
	\vestibulum\cache(
		__DIR__ . '/.cache', 'example data'
	);

	Assert::true(file_exists(__DIR__ . '/.cache'));
	Assert::same('example data', file_get_contents(__DIR__ . '/.cache'));
	unlink(__DIR__ . '/.cache');
}