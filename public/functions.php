<?php
use vestibulum\Pages;

/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */

$cms->pages = Pages::from($cms->src(), ['404'])->toArraySorted();