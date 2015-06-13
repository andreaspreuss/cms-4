<?php

/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */

namespace cms {
	/** @var \cms\Sphido $cms */
	$cms->pages = Pages::from(content(), ['404'])->toArraySorted(); // get pages for menu
}

namespace {
	use Latte\Macros\MacroSet;

	/** @var \cms\Sphido $cms */

	// add your custom function here
	function yolo() {
		\cms\redirect('http://www.omdesign.cz');
	}

	// and in template just write
	// {yolo()}
	// that's all

	// add cutom filters
	add_filter(
		'url',
		function (Url $url) {
			return $url; // do nothing
			return $url->host('www.sphido.org'); // change anything
		}
	);

	add_filter(
		'latte.macroset', function (MacroSet $set) {
		$set->addMacro('custom', 'echo "This is custom macro";');
	}
	);
}
