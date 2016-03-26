<!--
title: Multi Language Content
-->

## Multi Language Content

Lets have follow source files structure:

    /en/index.md
    /en/content.md
    /en/contact.md

    /cs/index.md
    /cs/obsah.md
    /cs/kontakt.md

Add follow lines to your `functions.php` file:

    // detect browser language
    $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
    $cms->lang = in_array($lang, ['en', 'cs']) ? $lang : 'en';

    // redirect users to selected language
    if ($cms->getRequest() === '/') header('Location: ' . $cms->url($cms->lang));

Then you can add follow code if you are using Pages class for menu generation:

    $cms->pages = Pages::from(\dir\content('/' . LANGUAGE))->toArraySorted();

Add language switch somewhere to your template file:

    <a href="{url 'en'}">English</a> | <a href="{url 'cs'}">Česky</a>


<a href="/examples" class="btn btn-primary">Return to Examples</a>