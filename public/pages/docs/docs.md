<!--
id: docs
title: Sphido CMS Docs
menu: Docs
order: 4
-->

# Sphido CMS Docs

## Content files

You can user follow types of files:

1. HTML file `*.html` or
2. Markdown syntax file `*.md` or
3. Latte template file `*.latte` or
4. PHP file `*.phtml`

If any file cannot be found, the file `content/404.md` will be generate or subdirectory `404.md` file. Lets have look to follow examples:

| File                       | URL 
|----------------------------|-------
| `/content/index.html`      | `/`
| `/content/index.md`        | `/`
| `/content/index.latte`     | `/`
| `/content/index.phtml`     | `/`
| `/content/sub.md`          | `/sub`
| `/content/sub/index.html`  | `/sub`
| `/content/sub/index.md`    | `/sub`
| `/content/sub/index.latte` | `/sub`
| `/content/sub/index.phtml` | `/sub`
| `/content/som/url/xxx.md`  | `/some/url/xxx`


## File Metadata

Whole metadata are optional. Sphido CMS support HTML style block comments for metadata: 

    <!--
    title: Welcome
    description: Add some nice description...
    author: Roman Ožana
    order: 100
    date: 2013/01/01
    whatever: some custom content
    status: 404
    -->

All metadata are available `\stdClass $page` variable and can be accesible from template file e.g. as `{l}$page->title{r}` or `{l}$page->whatever{r}`.
The `status` will be used as HTTP status code.

If you don't setup title - first H1 content will be used. If you don't setup description - shorten text content will be generated automatically.

## Templating
| Variable       | Description
|----------------|-------------
| `$cms`         | Main Sphido cms object
| `$page`        | Current processed file metadata.
| `$config`      | Configuration as `\stdClass` variable.
| `$content`     | HTML content to be generated on current page.

- [Latte Templates](http://latte.nette.org/)


## Filters

Allow modify some key values if necessary. A filter function takes as input the unmodified data,
and returns modified data. If the data is not modified by your filter, then the original data must be returned.

### Modify generated URLs

Allow change all generated URL.

| Param   | Value
|---------|-------
| Keyword | `url`
| Input   | `string $url`, `string $slug`, `string $server`
| Output  | `string`

Example:

	add_filter('url', function($url, $slug, $server) {
		return 'http://customserver.com/' . $slug
	});
	
### Change Latte Engine object

Allow change main [Latte Engine object](https://github.com/nette/latte/blob/master/src/Latte/Engine.php). You can for 
example add custom [macroset](https://github.com/nette/latte/blob/master/src/Latte/Macros/MacroSet.php) or add Filters

| Param   | Value
|---------|-------
| Keyword | `latte`
| Input   | `\Latte\Engine $latte`
| Output  | `\Latte\Engine $latte` 

#### Add Curom Macro Example

Follow example show how to add custom Macro to Latte Engine. Copy follow code to `function.php` file:

	function title($title) {l}return sprintf('<h1>%s</h1>', $title);{r}
	
	add_filter(
		'latte', function (\Latte\Engine $latte) {
			$set = new \Latte\Macros\MacroSet($latte->getCompiler());
			$set->addMacro('title', 'echo \title(%node.args);');
			return $latte;
		}
	);

Macro can be executed with follow code `{l}title 'something to title'{r}` and will generate HTML `<h1>something to title</h1>`.

#### Add custom Filter Example

Follow example show how to add custom Filter to Latte Engine. Copy follow code to `function.php` file:

	add_filter('latte', function(\Latte\Engine $latte) {
		$latte->addFilter('md', function($content) {
			return \Parsedown::instance()->text($content);
		});
		return $latte;
	});

Filter like `{l}$var|md|noescape{r}` will be now accessible everywhere in Latte templates and parse input with markdown parser.

## Events