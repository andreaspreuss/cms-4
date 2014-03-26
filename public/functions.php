<?php

/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */


function menu($file, $meta, $pages) {
	if (!$pages) return;
	echo '<ul>';
	echo '<li' . ($meta->id === 'home' ? ' class="active"' : null) . '><a href="' . \om\Vestibulum::url() . '">Home</a></li>';
	foreach ($pages as $current => $page) {
		echo '<li' . ($file === $current ? ' class="active"' : null) . '>';
		echo '<a href="' . \om\Vestibulum::url($page->slug) . '">' . $page->title . '</a>';
		echo '</li>';
	}
	echo '</ul>';
}

function myurl() {
	\om\Vestibulum::url($_SERVER['REQUEST_URI']);
}
