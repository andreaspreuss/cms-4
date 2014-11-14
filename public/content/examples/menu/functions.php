<?php
namespace vestibulum {
	/** @var \vestibulum\Vestibulum $this */
	$this->pages = Pages::from(src('/examples'))->toArraySorted();
}
