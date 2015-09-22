<!--
id: create-content
title: Create content in Sphido CMS
template: ../../layout.docs.latte
-->

# How to create content?
 
Creating pages in Sphido is as simple as creating a Markdown file (`.md`) with you favourite text editor.
If you are new to [Markdown](http://daringfireball.net/projects/markdown/) please refer 
to the [syntax guide](http://daringfireball.net/projects/markdown/syntax) for more information. There are several 
important aspects to your files that you need to pay attention to when you create pages:

## File Name and URL

The name of the file defines what URL or "slug" is used to navigate to that page. For example if you create a 
file called `about.md` you would visit that page be navigation to `www.example.com/about`. 
Below is a table of example folder and file names and the URL's they would have in Sphido.

File Location | URL
------------- | -------------
`pages/hello.md` | `/hello`
`pages/an-example-page/index.md` | `/an-example-page`
`pages/my-example-page.md` | `/my-example-page`
`pages/hello/my-example-page.md` | `/hello/my-example-page`
`pages/some/sub/folder/page.md` | `/some/sub/folder/page`

## File Matadata <small>(optional)</small>

At the top of a page you can place a block comment to specify certain metadata of the page. 
For example you can specify the page **title** or a **description** to be used by search engines. 

	<!--
	id: 123456
	title: This is page title 
	description: Here can be description..
	-->

All page metadata are accessible in Template through `$page` variable like: `{l}$page->title}` or `{l}$page->id}`. 
Read more about [Latte templates syntax](/docs/latte-templates).
	
### Metadata default values

Metadata are optional. If you do not specify any, [Sphido](/) use default metadata values:
  
 Metadata | Value
----------|---------
**title** |  will be taken from `<h1>` 
**description** | will be shorten from content  
**status** | will be 200 OK  

### Changed HTTP Response code <small>(optional)</small>

Through the `status` metadata variable can be changed [HTTP response code](http://en.wikipedia.org/wiki/List_of_HTTP_status_codes) of current page.

### URL macro <small>(optional)</small>

You can also use <code>&#123;url [slug]}</code> macro in your Markdown or HTML pages which will be replaced with the current server URL:

 Code in HTML or Markdown | Results
--------------------------|---------
<code>{l}url{r}</code> | `{url}`  
<code>{l}url aaa{r}</code> | `{url aaa}`  
<code>{l}url /examples/send{r}</code> | `{url '/examples/send'}`  

## Need more then Markdown?
 
If you need more then [Markdown](http://daringfireball.net/projects/markdown/), use HTML, PHTML or [Latte syntax](http://latte.nette.org/) . 

File Location | URL
------------- | -------------
`pages/use-html-syntax.html` | `/use-html-syntax`
`pages/use-phtm-syntax.phtml` | `/use-phtml-syntax`
`pages/use-latte-syntax.latte` | `/use-latte-syntax`
