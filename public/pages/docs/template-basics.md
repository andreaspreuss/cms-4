<!--
id: template-basics
menu: Sphido Template Basics
template: ../../layout.docs.latte
-->

# Sphido Template Basics

### Chose page template <small>(optional)</small>

[Sphido](/) uses the [Latte template engine](http://latte.nette.org/en/) to power its themes. You can specify template directly in page metadata:

	<!--
	template: ../../my-template-file.latte 
	-->
	
	# Content of page

If you don't specify template in page file, Sphido will user default `layout.latte` from current working directory.

### Change default template file <small>(optional)</small>

You can easily change default template file directly from `config.php`:

	return [
		// ...
		'meta' => [
			'template' => getcwd() . '/my-custom-default-theme.latte'
		]
		// ...
	];


### Customize 404 page <small>(optional)</small>

This template shown when **no page is found** for the current URL. If page not exists, Sphido use one of follow files from 
pages directory (in that oreder):

- `pages/404.html`
- `pages/404.md`
- `pages/404.latte`
- `pages/404.phtml`

Do not forget set **correct status code**! You can use `status` metadata variable or [`http_response_code()`](http://php.net/manual/en/function.http-response-code.php) function.

	<!--
	title: Page not found
	class: page-not-found
	status: 404
	-->
	
	# Custom 404 page

		