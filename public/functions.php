<?php
use vestibulum\Pages;

/**
 * @var \vestibulum\Vestibulum $cms
 * @author Roman Ozana <ozana@omdesign.cz>
 */

$skip = ['404', $cms->src('/customize'), $cms->src('/email')];
$cms->pages = Pages::from($cms->src(), $skip)->toArraySorted();