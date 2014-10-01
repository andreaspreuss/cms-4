<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
namespace vestibulum {
	/** @var \vestibulum\Vestibulum $cms */

	$cms->pages = Pages::from(src(), ['404'])->toArraySorted();
}

namespace {
	/** @var \vestibulum\Vestibulum $cms */

	// add your custom function here
	function yolo() { \vestibulum\redirect('http://www.omdesign.cz'); }
}