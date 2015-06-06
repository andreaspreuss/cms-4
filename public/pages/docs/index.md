<!--
id: how-to
title: Example how to hack/customize Sphido CMS
menu: Docs
order: 3
-->


## [Using PHTM](/docs/phtml)
## [Configure Sphido CMS](/docs/configure)
## [Extending Sphido CMS](/docs/extending)
Sphido CMS can be easily extends by `functions.php` file, you can add custom functions or parametters.


## Hacking response

### JSON response

It's easy to overwrite main response. Just add `ajax.php` to your **src or current directory**:

	<?php
	namespace sphido {
		require_once __DIR__ . "/function.json.php"
		isset($this) && $this instanceof Sphido or die('Sorry can be executed only from Sphido');
	
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
	namespace sphido {
		isset($this) && $this instanceof Sphido or die('Sorry can be executed only from Sphido');
	
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

	$cms->page->setContent(
		preg_replace_callback(
			'{(!\[.+\]\s?\()(\S*)([ \n]*(?:[\'"].*?[ \n]*[\'"])?\))}xsU', function ($matches) use ($cms) {
				$path = $cms->page->isDir() ? $cms->page->getRealPath() : $cms->page->getDir();
				$path = str_replace(dirname(\sphido\content()), '', $path) . '/';
				return $matches[1] . url($path . $matches[2]) . $matches[3];
			}, $cms->page->getContent()
		)
	);

## Advanced hacks

- [Multi Language Content](/examples/multi-language)
- [Direct Latte input](/examples/latte)
- [Redirect](/examples/redirect)
- [Links](/examples/links)
- [Download file example](/examples/download)
- [Create sitemap](/examples/sitemap)
- [Ajax contact form](/examples/email)
- [Raw PHTML support](/examples/phtml)
- [Generate nested HTML menu](/examples/menu)

