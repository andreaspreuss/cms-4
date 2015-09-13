<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/dir.php';

\Tester\Environment::setup();

{// tmp directory from cache
	config(['cache' => __DIR__]);
	Assert::same(\dir\cache('abc'), __DIR__ . '/abc'); // tmp directory
	Assert::same(\dir\cache('////abc'), __DIR__ . '/////abc'); // tmp directory
}

{ // content directory
	config(['content' => __DIR__]);
	Assert::same(__DIR__, \dir\content());
}

{// src directory
	Assert::false(\dir\src('not existing directory'));
	Assert::same(__DIR__, \dir\src('../tests'));
	Assert::same(__FILE__, \dir\src('../tests/' . basename(__FILE__)));
}

