<?php
namespace vestibulum {
	/** @var \vestibulum\Vestibulum $this */
	$this->pages = Pages::from(content('/examples'))->toArraySorted();
}
