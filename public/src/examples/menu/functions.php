<?php
namespace vestibulum {
	/** @var \vestibulum\Vestibulum $cms */
	$cms->pages = Pages::from(src('/examples'))->toArraySorted();
}
