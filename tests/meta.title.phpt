<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
\Tester\Environment::setup();

class Metadata {
	use \vestibulum\Metadata;
}

$meta = new Metadata();


// Getting main title from content
Assert::same('Title', $meta->parseTitle('<h1>Title</h1>'));
Assert::same('Title', $meta->parseTitle('# Title'));

Assert::same('First', $meta->parseTitle('# First' . PHP_EOL . '<h1>Second</h1>'));
Assert::same('First', $meta->parseTitle('<h1>First</h1>' . PHP_EOL . '# Second'));
