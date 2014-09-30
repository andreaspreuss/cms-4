<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
namespace vestibulum {
	/** @var \vestibulum\Vestibulum $cms */
	$skip = ['404', src('/examples')];
	$cms->pages = Pages::from(src(), $skip)->toArraySorted();
}

namespace {
	/** @var \vestibulum\Vestibulum $cms */

	// add your custom function here
	function yolo() { \vestibulum\redirect('http://www.omdesign.cz'); }
}