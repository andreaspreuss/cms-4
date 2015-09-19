<?php
namespace cms {

	isset($this) && $this instanceof Sphido or die('Sorry can be executed only from Sphido');

	class Zipper extends \ZipArchive {

		/**
		 * @param $path
		 */
		public function addDir($path, $parrent = '') {

			$this->addEmptyDir($parrent);
			if ($parrent !== '') {
				$this->addEmptyDir($parrent);
				$parrent .= '/';
			}


			$iterator = new \FilesystemIterator($path);

			foreach ($iterator as $node) {
				/** @var \SplFileInfo $node */
				if ($node->isDir()) {
					$this->addDir(strval($node), $parrent . $node->getBasename());
				} elseif ($node->isFile()) {
					$this->addFile(strval($node), $parrent . $node->getBasename());
				}
			}
		}
	}

	$file = __DIR__ . '/../Sphido.zip';

	// TODO expire of cache...

	if (!file_exists($file)) {
		$zip = new Zipper();
		$zip->open($file, \ZipArchive::OVERWRITE | \ZipArchive::CREATE);

		// Sphido
		$zip->addDir(\dir\root('/public'), '/public');
		$zip->addDir(\dir\root('/src'), '/src');


		// Dependencies
		$zip->addDir(\dir\root('/vendor/composer'), '/vendor/composer');
		$zip->addDir(\dir\root('/vendor/latte'), '/vendor/latte');
		$zip->addDir(\dir\root('/vendor/erusev'), '/vendor/erusev');
		$zip->addDir(\dir\root('/vendor/sphido'), '/vendor/sphido');

		// files
		$zip->addFile(\dir\root('/vendor/autoload.php'), '/vendor/autoload.php');
		$zip->addFile(\dir\root('/.htaccess'), '.htaccess');
		$zip->addFile(\dir\root('/composer.json'), 'composer.json');
		$zip->addFile(\dir\root('/readme.md'), 'readme.md');
		$zip->addFile(\dir\root('/LICENSE'), 'LICENSE');

		// empty dirs
		$zip->addEmptyDir('/cache');

		$zip->close();
	}

	if (file_exists($file)) {
		header("Location: " . url('Sphido.zip'), true, 302);
	} else {
		header("Location: https://github.com/sphido/cms/releases", true, 302);
	}

	exit; // don't execute any
}