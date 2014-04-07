<?php
use vestibulum\Vestibulum;
use vestibulum\Menu;
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
function slug(\vestibulum\File $file) {
	global $cms;
	/** @var Vestibulum $cms */

	return $cms->url(str_replace($cms->src(), '', $file->getDir() . '/' . $file->getName()));
}

function menu() {
	global $cms;
	/** @var Vestibulum $cms */

	$pages = Menu::from($cms->file->getDir(), ['index', '404', 'how-to'])->toArraySorted();

	if (!$pages) return;
	echo '<ul>';
	echo '<li' . ($cms->meta->id === 'home' ? ' class="active"' : null) . '><a href="' . \vestibulum\Vestibulum::url(
		) . '">Home</a></li>';
	foreach ($pages as $current => $file) {
		echo '<li' . ($cms->file->getRealPath() === $file->getRealPath() ? ' class="active"' : null) . '>';
		echo '<a href="' . slug($file) . '">' . $file->title . '</a>';
		echo '</li>';
	}
	echo '</ul>';
}