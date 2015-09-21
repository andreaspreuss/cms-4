<?php
// Uncomment co enable Tracy
// @see https://github.com/nette/tracy
require_once __DIR__ . '/../vendor/tracy/tracy/src/tracy.php';
\Tracy\Debugger::enable(null, __DIR__ . '/../log');

require_once __DIR__ . '/../src/cms.php';

map($cms = new \cms\Sphido());

//
// Custom URL handler goes here....
//
// map('page', function () {}); // handle http://www.sphido.org/page
// map('page2', function () {}); // handle http://www.sphido.org/page
//
// @see https://github.com/sphido/routing for more information
//

dispatch($cms);