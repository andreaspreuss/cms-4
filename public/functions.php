<?php

/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */

namespace cms {

	/** @var \cms\Sphido $cms */

	// Follow files are used in examples
	require_once __DIR__ . '/../vendor/sphido/json/src/json.php';
	require_once __DIR__ . '/../vendor/sphido/http/src/http.php';
	require_once __DIR__ . '/../vendor/sphido/download/src/download.php';

	// it's return menu items from all pages in content folder
	function menu() {
		return Pages::from(\dir\content(), ['404', \dir\content('example')])->toArraySorted();
	}

	// Custom default error handler... if 404.md missing in root
	on(
		MissingPage::class . '_default',
		function () {
			echo 'Page not found...';
		}
	);
}