<!--
id: how-to
title: Example how to hack/customize Vestibulum CMS
menu: Examples
order: 5
-->

<div class="alert alert-info">
	<a href="{url}">Current site</a> it's an great example
	how to use <strong>Vestibulum CMS</strong>! Visit source on
	<a href="https://github.com/OzzyCzech/vestibulum/tree/master/public" target="_blank">GitHub</a>.
</div>

## Add custom function

Open `functions.php` in main folder and add your custom functions:

	namespace {
		/** @var \vestibulum\Vestibulum $this */
		function currentUrl() {
	     global $this; /** @var \vestibulum\Vestibulum $this */
	     return $this->url($_SERVER['REQUEST_URI']);
	  }
	}

Your function now will be accesible in Latte template `{currentUrl()}`.

### Add custom Latte parameters

You can add more parameters from `function.php

    $this->get = $_GET;
    $this->post = $_POST;
    $this->xxx = 'some value';

Will be accessible in template like `{$get->something}` or `{$post.something}` or `{$xxx}`.

### Change something in config

You can change your `config.php` or overwrite something directly from `functions.php`

    /** @var \vestibulum\Vestibulum $this */
    $this->config()->title = 'Vestibulum';
    $this->config()->example = 'example';

In template will be accessible `{config()->title}`

## Hacking response

### JSON response

It's easy to overwrite main response. Just add `ajax.php` to your **src or current directory**:

	<?php
	namespace vestibulum {
		isset($this) && $this instanceof Vestibulum or die('Sorry can be executed only from Vestibulum');
	
		// check AJAX request
		isAjax() or json(['message' => 'Not AJAX request, but nice try :-)']);
	
		// response all AJAX requests
		json(['message' => 'Well done! It\'s AJAX request']);
	}

You cen see example response here [{url examples/email}]({url examples/email}).

### Submitting HTML form

Lets have `contact.php` and `contact.html` in your **src directory**. Whole requires will be process in chain.
PHP goes first after that it's prepare HTML content:

	<?php
	namespace vestibulum {
		isset($this) && $this instanceof Vestibulum or die('Sorry can be executed only from Vestibulum');
	
		if (isPost()) {
		  @mail('info@example.com', 'My Subject', $_POST['message']); // send email...
		  $this->flash = 'It's send well'; // can be used in Template file for example
		}
	}

and HTML need contains

    <form method="post">
      <textarea name="message"></textarea>
      <button type="submit">Send</button>
    </form>

### Absolute image URL

Add follow code to your `functions.php` and all markdown images URL will be replaces with absolute URL:

    $this->file->setContent(
    	preg_replace_callback(
    		'{(!\[.+\]\s?\()(\S*)([ \n]*(?:[\'"].*?[ \n]*[\'"])?\))}xsU', function ($matches) use ($this) {
    			$path = $this->file->isDir() ? $this->file->getRealPath() : dirname($this->file);
    			$path = str_replace(dirname($this->src()), '', $path . '/');
    			return $matches[1] . $this->url($path . $matches[2]) . $matches[3];
    		}, $this->file->getContent()
    	)
    );


## Advanced hacks

- [Multi Language Content](/examples/multi-language)
- [Replace Composer Autoloader](/examples/replace-composer-autoloader)
- [Create sitemap](/examples/sitemap)
- [Ajax contact form](/examples/email)
- [Raw PHTML support](/examples/phtml)
- [Generate nested HTML menu](/examples/menu)

