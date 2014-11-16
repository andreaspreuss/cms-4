<!--
id: docs
title: Vestibulum Docs
menu: Docs
order: 4
-->

# Vestibulum docs

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

Whole metadata are optional. Vestibulum CMS support HTML style block comments for metadata: 

    <!--
    title: Welcome
    description: Add some nice description...
    author: Roman Ožana
    order: 100
    date: 2013/01/01
    whatever: some custom content
    status: 404
    -->

All metadata are available `\stdClass $file` variable and can be accesible from template file e.g. as `{$file->title}` or `{$file->whatever}`. The `status` will be used as HTTP status code.

If you don't setup title - first H1 content will be used. If you don't setup description - shorten text content will be generated automatically.

## Templating
| Variable       | Description
|----------------|-------------
| `$file`        | Current processed file metadata.
| `$config`      | Configuration as `\stdClass` variable.
| `$content`     | HTML content to be generated on current page.

- [Latte Templates](http://latte.nette.org/)

## Events

TODO