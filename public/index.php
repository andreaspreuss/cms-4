<?php
// Uncomment co enable Tracy
// @see https://github.com/nette/tracy
if (file_exists(__DIR__ . '/../vendor/tracy/tracy/src/tracy.php')) {
	require_once __DIR__ . '/../vendor/tracy/tracy/src/tracy.php';
	\Tracy\Debugger::enable(null, __DIR__ . '/../log');
}

require_once __DIR__ . '/../src/cms.php';

$cms = new \cms\Sphido();

//
// Custom URL handler goes here....
//
// /route/map('page', function () {}); // handle http://www.sphido.org/page
// /route/map('page2', function () {}); // handle http://www.sphido.org/page2
//
// @see https://github.com/sphido/routing for more information
//

\app\dispatch($cms);