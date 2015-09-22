<!--
id: create-menu
title: How to create menu
template: ../../layout.docs.latte
-->

# How to create nested menu

First modify your `function.php` file in public folder:

<pre><code>{file_get_contents(\dir\content('/example/menu/functions.php'))}</code></pre>

Then add follow structure to your Latte template:

<pre><code>{file_get_contents(\dir\content('/example/menu/index.latte'))}</code></pre>

- [See live menu example here](/example/menu)
- [Get example source codes](https://github.com/sphido/cms/tree/master/public/pages/example/menu)
