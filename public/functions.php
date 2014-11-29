<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
namespace vestibulum {
	/** @var \vestibulum\Vestibulum $this */

	$this->pages = Pages::from(content(), ['404'])->toArraySorted();
}

namespace {
	/** @var \vestibulum\Vestibulum $this */

	// add your custom function here
	function yolo() {
		\vestibulum\redirect('http://www.omdesign.cz');
	}

	// and in template just write
	// {yolo()}
	// that's all
}

