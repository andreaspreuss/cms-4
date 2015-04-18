<?php
namespace cms {

	class Zipper extends \ZipArchive {

		/**
		 * @param $path
		 */
		public function addDir($path) {
			$this->addEmptyDir($path);
			$nodes = glob($path . '/*');
			foreach ($nodes as $node) {
				if (is_dir($node)) {
					$this->addDir($node);
				} else if (is_file($node)) {
					$this->addFile($node);
				}
			}
		}
	}

	if (!file_exists('Sphido.zip')) {
		$zip = new Zipper();
		$zip->open('Sphido.zip', \ZipArchive::CREATE);

		// content
		$zip->addDir(__DIR__ . '/../public');
		$zip->addDir(__DIR__ . '/../src');

		// dependencies
		$zip->addDir(__DIR__ . '/../vendor/erusev');
		$zip->addDir(__DIR__ . '/../vendor/latte');

		// tmp file
		$zip->addEmptyDir('/tmp');

		// from root dir
		$zip->addFile(__DIR__ . '/../.htaccess', '.htaccess');
		$zip->addFile(__DIR__ . '/../composer.json', 'composer.json');
		$zip->addFile(__DIR__ . '/../update.php', 'update.php');
		$zip->addFile(__DIR__ . '/../readme.md', 'readme.md');
		$zip->addFile(__DIR__ . '/../LICENSE', 'LICENSE');

		$zip->close();
	}


	if (file_exists('Sphido.zip')) redirect(url('/Sphido.zip')); // download latest version
	redirect('https://github.com/sphido/cms/releases'); // redirect to releases
}