<?php
namespace cms {

	isset($this) && $this instanceof Sphido or die('Sorry can be executed only from Sphido');

	/**
	 * @author Roman Ožana <ozana@omdesign.cz>
	 */
	class SiteMap {

		public function __construct() {
			header("Content-type: text/xml");
		}

		public function addItem($pages) {
			$out = '';
			foreach ($pages as $page) {
				/** @var \cms\Page $page */
				$out .= sprintf(
					'<url><loc>%s</loc><lastmod>%s</lastmod></url>' . PHP_EOL,
					htmlspecialchars(url($page->getSlug(__DIR__))),
					htmlspecialchars(date('c', $page->date))
				);
				if ($page->children) $out .= $this->addItem($page->children);
			}
			return $out;
		}

		public function __toString() {
			return
				'<?xml version = "1.0" encoding = "UTF-8"?>' . PHP_EOL .
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL .
				$this->addItem(\cms\Pages::from(__DIR__, ['404'])->toArraySorted()) . '</urlset>';
		}
	}

	die(new SiteMap);
}