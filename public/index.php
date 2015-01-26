<?php
require_once __DIR__ . '/../src/Vestibulum.php';

// external library
require_once __DIR__ . '/../vendor/latte/latte/src/latte.php';
require_once __DIR__ . '/../vendor/erusev/parsedown/Parsedown.php';

map(new \vestibulum\Vestibulum()); // deathly simple