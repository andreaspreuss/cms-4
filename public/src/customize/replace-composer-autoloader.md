<!--
title: Replace Composer Autoloader
-->

## Replace Composer Autoloader

And one more things, you can save 20ms when you avoid Composer Autoloader

    require_once __DIR__ .'/../src/Vestibulum.php';
    require_once __DIR__ .'/../vendor/erusev/parsedown/Parsedown.php';
    require_once __DIR__ .'/../vendor/twig/twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();
    echo new \vestibulum\Vestibulum; // deathly simple

<a href="/customize" class="btn btn-primary">Return to Customize</a>