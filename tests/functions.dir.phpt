<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/functions.dir.php';

\Tester\Environment::setup();

// tmp directory from cache
config(['cache' => __DIR__]);
Assert::same(\dir\cache('abc'), __DIR__ . '/abc'); // tmp directory
Assert::same(\dir\cache('////abc'), __DIR__ . '/////abc'); // tmp directory

// content directory
config(['content' => __DIR__]);

var_dump(config()->content);
Assert::same(__DIR__, \dir\content());

// src directory
Assert::false(\dir\src('xxxxx'));
Assert::same(realpath(__DIR__ . '/../src'), \dir\src());
Assert::same(realpath(__DIR__ . '/../src/sphido.php'), \dir\src('sphido.php'));
