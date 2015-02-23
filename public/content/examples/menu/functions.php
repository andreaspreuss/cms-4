<?php
namespace cms {
	/** @var \cms\Sphido $this */
	$this->pages = Pages::from(content('/examples'))->toArraySorted();
}
