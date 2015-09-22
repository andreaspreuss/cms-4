<?php
namespace cms {
	/** @var \cms\Sphido $this */

	// add Follow line to public/functions.php
	$this->pages = Pages::from(\dir\content('/example'))->toArraySorted();
}
