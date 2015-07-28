<?php
//require_once __DIR__ . '/../vendor/tracy/tracy/src/tracy.php'; \Tracy\Debugger::enable(null, __DIR__ . '/../tmp');
require_once __DIR__ . '/../src/cms.php';

map($content = new \cms\Sphido());

// Custom URL handler goes here....
// map('page', function () {});

dispatch($content);