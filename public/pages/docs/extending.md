# Extending Sphido CMS

## Custom URL handlers

	map('hola', function () {
	  echo 'hey';
	});


## Add custom functions to `functions.php`
 
Open `functions.php` in main folder and add your custom functions:

	namespace {
		function currentUrl() {
			return \sphido\url($_SERVER['REQUEST_URI']); // current URL
	  }
	}

Your function now will be accesible in Latte template `{l}currentUrl(){r}`.

## Add custom Latte parameters

You can add more parameters in `function.php`, for example `$_GET` or `$_POST` variables:

	$this->get = $_GET;
	$this->post = $_POST;
	     
Or you can add whatever you need:

	$this->xxx = 'some value';
	
All parametters will be accessible from [Latte Template](http://latte.nette.org/en/) like this
 `{l}$get->something{r}` or `{l}$post->something{r}` or `{l}$xxx{r}`.

### Resources

- https://github.com/sphido/cms/blob/master/public/functions.php
- [Sphido/routing](https://github.com/sphido/routing) - events library 