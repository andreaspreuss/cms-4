<?php

namespace cms {

	echo '<pre>';

	class Zipper extends \ZipArchive {

		/**
		 * @param $path
		 */
		public function addDir($path, $parrent = '') {
			$this->addEmptyDir($path);
			if ($parrent !== '') {
				$this->addEmptyDir($parrent);
				$parrent .= '/';
			}

			$iterator = new \FilesystemIterator($path);

			//$nodes = glob($path . '/*');
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

	//if (!file_exists('Sphido.zip')) {
	$zip = new Zipper();
	$zip->open('Sphido.zip', \ZipArchive::OVERWRITE | \ZipArchive::CREATE);
	$zip->addDir(realpath(__DIR__ . '/..'), '/public');
	$zip->close();

	/*
	 *


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


	*/

	//}

	/*

	if (file_exists('Sphido.zip')) redirect(url('/Sphido.zip')); // download latest version
	redirect('https://github.com/sphido/cms/releases'); // redirect to releases
	*/

	die();
}