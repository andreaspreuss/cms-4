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


{ // empty meta
	Assert::null($meta->parseMeta(''));
	Assert::same([], $meta->parseMeta('<!-- -->'));
}

{ // some values
	Assert::same(['t' => '5'], $meta->parseMeta('<!-- t: 5 -->')); // int
	Assert::same(['t' => 'some string with spaces'], $meta->parseMeta('<!-- t: some string with spaces -->'));
}

{ // special content
	Assert::same(['a key' => 'a value'], $meta->parseMeta('<!-- a key : a value -->'));
	Assert::same(['t' => '12:00:00'], $meta->parseMeta('<!--t: 12:00:00-->'));
}

{ // multiple values test
	Assert::same(['a' => 'b', 'b' => 'c'], $meta->parseMeta('<!-- a : b ' . PHP_EOL . ' b  :  c	-->'));
}


