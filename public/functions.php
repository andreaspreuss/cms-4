<?php
use vestibulum\Pages;

/**
 * @var \vestibulum\Vestibulum $cms
 * @author Roman Ozana <ozana@omdesign.cz>
 */

$skip = ['404', $cms->src('/examples'), $cms->src('/customize')];
$cms->pages = Pages::from($cms->src(), $skip)->toArraySorted();