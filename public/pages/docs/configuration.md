<!--
id: configuration
title: How to Configure Sphido CMS
template: ../../layout.docs.latte
-->

# Sphido Configuration

To edit the configuration of [Sphido](/) you need to edit `config.php` in the root directory. It should contain config variables like:

You can change your `config.php` or overwrite something directly from `functions.php`

```php
namespace sphido {
	config()->title = 'Sphido';
	config()->myvariable = 'Speed is the core';
	config()->example = 'example';
}
```	

If you are editing the template you can add custom variables to config file and they will become availble in the 
template via the config object (e.g. `{config()->myvariable}`)


- [See config.php file example](https://github.com/sphido/cms/blob/master/public/config.php)