<!--
title: How to
order: 4
-->

# How to add custom functions

## Add custom Twig functions/filters

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


## Change config

Or just change/add something to config

    /** @var \vestibulum\Vestibulum $cms */
    $cms->config()->title = 'Vestibulum';
    $cms->config()->example = 'example';

In Twig template will be accessible `{{config.example}}`

## Create HTML menu

    function generateMenu(array $array, $level = 0) {
    	global $cms;
    	/** @var \vestibulum\Vestibulum $cms */

    	$output = '';
    	foreach ($array as $current) {
    		/** @var \vestibulum\File $current */

    		$classes = array_filter(
    			[
    				$current->getRealPath() === $cms->file ? 'active' : null,
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

    function menu() {
      global $cms;
      /** @var \vestibulum\Vestibulum $cms */
    	$pages = \vestibulum\Menu::from($cms->src(), ['index', '404'])->toArraySorted();
    	echo generateMenu($pages);
    }