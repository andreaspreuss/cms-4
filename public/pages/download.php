<?php
namespace cms {

	isset($this) && $this instanceof Sphido or die('Sorry can be executed only from Sphido');

	if (file_exists(__DIR__ . '/../Sphido.zip')) {
		header("Location: " . url('Sphido.zip'), true, 302);
	} else {
		header("Location: https://github.com/sphido/cms/releases", true, 302);
	}

	exit; // don't execute anything else
}