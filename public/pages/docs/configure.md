# Configure Sphido CMS

## Change values in

You can change your `config.php` or overwrite something directly from `functions.php`

	namespace sphido {
		config()->title = 'Sphido';
		config()->->example = 'example';
	}
	
In template will be accessible `{config()->title}`