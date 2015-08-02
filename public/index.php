<?php
require_once __DIR__ . '/../vendor/tracy/tracy/src/tracy.php'; \Tracy\Debugger::enable(null, __DIR__ . '/../cache');
require_once __DIR__ . '/../src/cms.php';

map($cms = new \cms\Sphido());

// Custom URL handler goes here....
// map('page', function () {});

dispatch($cms);