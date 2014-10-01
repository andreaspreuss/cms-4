<!--
id: how-to
title: Example how to hack/customize Vestibulum CMS
menu: Examples
order: 4
-->

<div class="alert alert-info">
	<a href="%url%">Current site</a> it's an great example
	how to use <strong>Vestibulum CMS</strong>! Visit source on
	<a href="https://github.com/OzzyCzech/vestibulum/tree/master/public" target="_blank">GitHub</a>.
</div>

## Add custom function

Open `functions.php` in main folder and add your custom functions:

	namespace {
		/** @var \vestibulum\Vestibulum $cms */
		function myUrl() {
	     global $cms; /** @var \vestibulum\Vestibulum $cms */
	     return $cms->url($_SERVER['REQUEST_URI']);
	  }
	}

Your function now will be accesible in Latte template `{myUrl()}`.

### Add custom Twig parameters

You can add more parameters from `function.php

    $cms->get = $_GET;
    $cms->post = $_POST;
    $cms->xxx = 'some value';

Will be accessible in template like `{$get->something}` or `{$post.something}` or `{$xxx}`.

### Change something in config

You can change your `config.php` or overwrite something directly from `functions.php`

    /** @var \vestibulum\Vestibulum $cms */
    $cms->config()->title = 'Vestibulum';
    $cms->config()->example = 'example';

In Twig template will be accessible `{{config.example}}`

## Hacking response

### JSON response

It's easy to overwrite main response. Just add `ajax.php` to your **src or current directory**:

	<?php
	namespace vestibulum;
	isset($this) && $this instanceof Vestibulum or die('Sorry can be executed only from Vestibulum');

	// check AJAX request
	isAjax() or json(['message' => 'Not AJAX request, but nice try :-)']);

	// response all AJAX requests
	json(['message' => 'Well done! It\'s AJAX request']);

You cen see example response here [%url%ajax](%url%ajax).

### Submitting HTML form

Lets have `contact.php` and `contact.html` in your **src directory**. Whole requires will be process in chain.
PHP goes first after that it's prepare HTML content:

	<?php
	namespace vestibulum;
	isset($this) && $this instanceof Vestibulum or die('Sorry can be executed only from Vestibulum');

	if (isPost()) {
	  @mail('info@example.com', 'My Subject', $_POST['message']); // send email...
	  $this->flash = 'It's send well'; // can be used in Template file for example
	}

and HTML need contains

    <form method="post">
      <textarea name="message"></textarea>
      <button type="submit">Send</button>
    </form>

### Absolute image URL

Add follow code to your `functions.php` and all markdown images URL will be replaces with absolute URL:

    $cms->file->setContent(
    	preg_replace_callback(
    		'{(!\[.+\]\s?\()(\S*)([ \n]*(?:[\'"].*?[ \n]*[\'"])?\))}xsU', function ($matches) use ($cms) {
    			$path = $cms->file->isDir() ? $cms->file->getRealPath() : dirname($cms->file);
    			$path = str_replace(dirname($cms->src()), '', $path . '/');
    			return $matches[1] . $cms->url($path . $matches[2]) . $matches[3];
    		}, $cms->file->getContent()
    	)
    );


### Generate nested HTML menu

Vestibulum contains class `Pages`, it's smart helper for iterate over src files:

	<ul n:block="menu">
		{foreach $pages as $item}
			<li n:class="$file->id === $item->id ? active" id="{$item->id}">
				<a href="{url $item}">{$item->menu ? $item->menu : $item->title}</a>
				{if $item->children}{include menu, pages => $item->children}{/if}
			</li>
		{/foreach}
	</ul>

And you also will need add follow code to your `functions.php`

    namespace vestibulum {
    	$cms->pages = Pages::from(src())->toArraySorted();
    }

## Advanced hacks

- [Multi Language Content](/examples/multi-language)
- [Replace Composer Autoloader](/examples/replace-composer-autoloader)
- [Create sitemap](/examples/sitemap)
- [Ajax contact form](/examples/email)
- [Raw PHTML support](/examples/phtml)

