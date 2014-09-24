<?php
namespace vestibulum;

/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;
use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';
Environment::setup();

// home
$_SERVER['REQUEST_URI'] = '/';
Assert::same('/', request());

$_SERVER['REQUEST_URI'] = '/a/b/c/d/e/f/g/';
Assert::same('/a/b/c/d/e/f/g/', request());

// request value caching
$_SERVER['REQUEST_URI'] = 'something';
Assert::same('something', request());

$_SERVER['REQUEST_URI'] = 'something else';
Assert::same('something', request());

// params strip
$_SERVER['REQUEST_URI'] = 'strip params?param=abc&param2=def';
Assert::same('strip params', request());

// /index.php strip
$_SERVER['REQUEST_URI'] = '/index.php/a/b/c/d';
Assert::same('/a/b/c/d', request());