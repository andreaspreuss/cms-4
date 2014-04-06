<?php
/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */

function menu() {
	global $cms;
	$pages = $cms->getPages($cms->file->getDir());

	/** @var \vestibulum\Vestibulum $cms */
	if (!$pages) return;
	echo '<ul>';
	echo '<li' . ($cms->meta->id === 'home' ? ' class="active"' : null) . '><a href="' . \vestibulum\Vestibulum::url(
		) . '">Home</a></li>';
	foreach ($pages as $current => $page) {
		echo '<li' . ($cms->file->getRealPath() === $current ? ' class="active"' : null) . '>';
		echo '<a href="' . \vestibulum\Vestibulum::url($page->slug) . '">' . $page->title . '</a>';
		echo '</li>';
	}
	echo '</ul>';
}

// add custom functions/filters callable from twig

function myurl() {
	global $cms;
	/** @var \vestibulum\Vestibulum $cms */
	return $cms->url($_SERVER['REQUEST_URI']);
}

// Change whatever you need before render

/** @var \vestibulum\Vestibulum $cms */
$cms->config()->title = 'Vestibulum';