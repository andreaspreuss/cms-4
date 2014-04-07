<!--
title: How to
order: 4
-->

# How to add custom functions

## Start with an example

[Vestibulum's homepage](%url%) it's an great example how to use **Vestibulum CMS**: [https://github.com/OzzyCzech/vestibulum/tree/master/public](https://github.com/OzzyCzech/vestibulum/tree/master/public)

## Basics
### Add custom Twig functions/filters

Open `functions.php` in main folder and add your custom functions:

    function myUrl() {
	    global $cms;
	    /** @var \vestibulum\Vestibulum $cms */
	    return $cms->url($_SERVER['REQUEST_URI']);
    }

In Twig template will be accessible `{{ myUrl() }}`

    function myFilter($string) {
	    return ' ::: ' . $string . '  ::: ';
    }

In Twig template will be accessible `{{ title|myFilter }}`


### Change something in config

You can change your `config.php` or overwrite something directly from `functions.php`

    /** @var \vestibulum\Vestibulum $cms */
    $cms->config()->title = 'Vestibulum';
    $cms->config()->example = 'example';

In Twig template will be accessible `{{config.example}}`

## Change response

### JSON response

It's easy to overwrite main response. Just add `ajax.php` to your **src directory**:

    <?php
    if (!$this instanceof \vestibulum\Vestibulum) die('Sorry can be executed only from Vestibulum'); // protection

    // first detect AJAX Request
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	    header('Content-Type: application/json');
	    exit(json_encode(['will' => 'This will be my message']));
    }

You cen see example response here [%url%ajax](%url%ajax).

### Submitting HTML form

Lets have `contact.php` and `contact.html` in your **src directory**. Whole requires will be process in chain.
PHP goes first after that it's prepare HTML content:

    <?php
    if (!$this instanceof \vestibulum\Vestibulum) die('Sorry can be executed only from Vestibulum'); // protection

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      @mail('info@example.com', 'My Subject', $_POST['message']); // send email...
      $this->flash = 'It's send well'; // can be used in Template file for example
    }

and HTML need contains

    <form method="post">
      <textarea name="message"></textarea>
      <button type="submit">Send</button>
    </form>

## Template hacking

### Generate multilevel HTML menu

Vestibulum contains class `Pages`, it's smart helper for iterate over src files:

    function menu() {
      global $cms;
      /** @var \vestibulum\Vestibulum $cms */
    	$pages = \vestibulum\Pages::from($cms->src(), ['index', '404'])->toArraySorted();
    	echo generateMenu($pages); // bellow
    }

Now you can recursively iterate over `Files array and create HTML structure:

    function generateMenu(array $array, $level = 0) {
    	global $cms;
    	/** @var \vestibulum\Vestibulum $cms */

    	$output = '';
    	foreach ($array as $current) {
    		/** @var \vestibulum\File $current */

    		$classes = array_filter(
    			[
    				$current->getRealPath() === $cms->file->getRealPath() ? 'active' : null,
    				$current->isDir() && $current->getRealPath() === dirname($cms->file) ? 'has-active-child' : null,
    				$current->isDir() ? 'root' : null,
    			]
    		);

    		$output .= '<li' . ($classes ? ' class="' . implode(' ', $classes) . '"' : null). '>';

    		$path = $current->isDir() ? $current->getRealPath() : dirname($current->file) . '/' . $current->name;
    		$path = str_replace(realpath($cms->src()), '', $path);

    		if (isset($current->children)) {
    			$output .= '<a href="' . $cms->url($path) . '">' . $current->title . '</a>';
    			$output .= generateMenu($current->children, $level + 1);
    		} else {
    			$output .= '<a href="' . $cms->url($path) . '">' . $current->title . '</a>';
    		}
    		$output .= '</li>' . PHP_EOL;
    	}

    	return '<ul>' . $output . '</ul>' . PHP_EOL;
    }




### Replace Twig with PHTML

Follow example is for those who have an performance obsession :-). First add `index.phtml` or `index.php` to your current working directory:

    <!DOCTYPE html>
    <html lang="en" ng-app="help">
    <head>
    	<meta charset="utf-8">
    	<title><?= $file->title ?> | <?= $config->title ?></title>
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta name="description" content="<?= $meta->description ?>">
    	<meta name="robots" content="all"/>
    	<meta name="author" content="<?= $meta->author ?>"/>
    	<link rel="shortcut icon" href="<?= $this->url('favicon.ico') ?>" type="image/x-icon"/>
    </head>

    <div class="container">
      <? menu(); /* call your functions */?>
    	<?= $content ?>
    </div>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

    </body>
    </html>


Create markdown file, and change metadata:

    <!--
    template: index.phtml
    -->

This is it! Now can have request even **under 6 ms**
