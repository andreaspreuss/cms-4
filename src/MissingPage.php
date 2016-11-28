<?php
namespace cms {


	/**
	 * Sphido 404|500 Error Page
	 *
	 * @author Roman Ožana <ozana@omdesign.cz>
	 */
	class MissingPage {

		function __invoke($error, $method, $path, \cms\Sphido $cms) {
			trigger(MissingPage::class . '_before', $error, $method, $path, $cms);

			if ($cms->page = Page::fromPath(\dir\content() . '/404', (array)\app\config()->meta)) {
				return $cms->render();
			}

			trigger(MissingPage::class . '_default', $error, $method, $path, $cms);
		}
	}
}