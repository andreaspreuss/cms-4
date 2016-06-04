<!--
id: configuration
title: How to Configure Sphido CMS
syntax:double
template: ../../layout.docs.latte
-->

# Sphido Configuration

[Sphido CMS](/) using [fastest config ever](https://github.com/sphido/config) - pure PHP arrays/objects - see [config.php](https://github.com/sphido/cms/blob/master/public/config.php) in the public directory:

```php
{file_get_contents(dir\root('public/config.php'))|noescape}
```

### Accessing config values

All values are accessible via `/app/config()` function e.g. `{l}=/app/config()->title{r}` will be replaced by value of title: _{= \app\config()->title}_

### Change config values in runtime

You can overwrite something in config from [functions.php](https://github.com/sphido/cms/blob/master/public/functions.php) like follow:

```php
namespace {
	/app/config()->title = 'Sphido';
	/app/config()->myvariable = 'Speed is the core';
	/app/config()->example = 'example';
}
```

Values from `config.php` will be replaced with new one in runtime.
