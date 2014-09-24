<?php
namespace vestibulum;

/**
 * @var \vestibulum\Vestibulum $cms
 * @author Roman Ozana <ozana@omdesign.cz>
 */

$skip = ['404', src('/examples')];
$cms->pages = Pages::from(src(), $skip)->toArraySorted();