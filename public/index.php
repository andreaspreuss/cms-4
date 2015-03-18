<?php
//require_once __DIR__ . '/../vendor/tracy/tracy/src/tracy.php'; \Tracy\Debugger::enable(null, __DIR__ . '/../tmp');
require_once __DIR__ . '/../src/cms.php';

// external library
require_once __DIR__ . '/../vendor/erusev/parsedown/Parsedown.php';

map($content = new \cms\Content()); // deathly simple
dispatch($content);