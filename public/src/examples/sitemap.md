<!--
title: How to create sitemap.xml
-->

# How to create sitemap

Create file `sitemap.xml.php` in your src directory and add follow code:


	<?php
	(isset($this) && $this instanceof \vestibulum\Vestibulum) or die('Sorry can be executed only from Vestibulum');

	class SiteMap {

		public function __construct() {
			header("Content-type: text/xml");
		}

	public function addItem($pages) {
			$out = '';
			foreach ($pages as $page) {
				/** @var \vestibulum\File $page */
				$out .= sprintf(
					'<url><loc>%s</loc><lastmod>%s</lastmod></url>' . PHP_EOL,
					htmlspecialchars(\vestibulum\Request::url($page->getSlug(__DIR__))),
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
				$this->addItem(\vestibulum\Pages::from(__DIR__, ['404'])->toArraySorted()) . '</urlset>';
		}
	}

	die(new SiteMap());
