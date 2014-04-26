<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
\Tester\Environment::setup();

class Request {
	use \vestibulum\Request;
}

// empty request
$request = new Request();
Assert::null($request->getRequest());

// home
$_SERVER['REQUEST_URI'] = '/';
$request = new Request();
Assert::same('/', $request->getRequest());

// some path
$_SERVER['REQUEST_URI'] = '/a/b/c/d/e/f/g/';
$request = new Request();
Assert::same('/a/b/c/d/e/f/g/', $request->getRequest());

// request value caching
$_SERVER['REQUEST_URI'] = 'something';
$request = new Request();
Assert::same('something', $request->getRequest());
$_SERVER['REQUEST_URI'] = 'something else';
Assert::same('something', $request->getRequest());

// params strip
$_SERVER['REQUEST_URI'] = 'strip params?param=abc&param2=def';
$request = new Request();
Assert::same('strip params', $request->getRequest());

// /index.php strip
$_SERVER['REQUEST_URI'] = '/index.php/a/b/c/d';
$request = new Request();
Assert::same('/a/b/c/d', $request->getRequest());