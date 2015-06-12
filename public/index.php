<?php
//require_once __DIR__ . '/../vendor/tracy/tracy/src/tracy.php'; \Tracy\Debugger::enable(null, __DIR__ . '/../tmp');
require_once __DIR__ . '/../src/cms.php';

map($content = new \cms\Content());

// custom URL handler goes here....

dispatch($content);