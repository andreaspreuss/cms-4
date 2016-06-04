<?php
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