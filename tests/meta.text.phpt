<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/cms.php';
\Tester\Environment::setup();

class Metadata {
	use \cms\Metadata;
}

$meta = new Metadata();

// Transform content to plain text
Assert::same('', $meta->text('<html>'));
Assert::same('This will be plain text', $meta->text('This will be plain text'));
Assert::same('This will be plain text', $meta->text('This ## will **be** plain *text*'));
Assert::same('This will be plain text', $meta->text('This <h2>will</h2> <strong>be</strong> plain <em>text</em>'));

// Shorten HTML / markdown content
Assert::same('', $meta->shorten('<html>'));
Assert::same('This will be shorten', $meta->shorten('<p>This will be shorten</p>'));
Assert::same('T', $meta->shorten('<p>This will be shorten</p>', 1));
Assert::same('This', $meta->shorten('<p>This will be shorten</p>', 4));
Assert::same('This', $meta->shorten('<p>This will be shorten</p>', 5));
Assert::same('This', $meta->shorten('<p>This will be shorten</p>', 6));
Assert::same('This', $meta->shorten('<p>This will be shorten</p>', 7));
Assert::same('This will', $meta->shorten('<p>This will be shorten</p>', 9));

Assert::same('This will be shorten', $meta->shorten('#This will be shorten'));
Assert::same('This will be shorten', $meta->shorten('# This will be shorten'));

