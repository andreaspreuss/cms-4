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
| `/content/sub/index.html`  | `/sub` (same as above)
| `/content/sub/index.md`    | `/sub` (same as above)
| `/content/sub/index.latte` | `/sub` (same as above)
| `/content/sub/index.phtml` | `/sub` (same as above)
| `/content/som/url/xxx.md`  | `/some/url/xxx`


## Metadata

    <!--
    title: Welcome
    description: Add some nice description...
    author: Roman Ožana
    order: 100
    date: 2013/01/01
    status: 404
    -->

## Templates

## Events

TODO