<?php
use vestibulum\Pages;

/**
 * @var \vestibulum\Vestibulum $cms
 * @author Roman Ozana <ozana@omdesign.cz>
 */

$cms->pages = Pages::from($cms->src(), ['404'])->toArraySorted();