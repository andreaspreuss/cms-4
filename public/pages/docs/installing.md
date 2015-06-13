<!--
id: installing
title: How to Install Sphido CMS
template: ../../layout.docs.latte
-->

# How to install Sphido CMS

1. Download and unzip [latest version](/download) or run `php composer.phar create-project sphido/cms`.
2. Change files in `public/content` and `public/config.php`
3. Upload everything to your server.

### Setup NGINX

Here is [NGINX](http://nginx.org/) configuration example:

	server {
		listen *:80;
		server_name sphido.org;
		root /Users/websites/Work/cms/public;

		# protect latte and markdown files against reading 
		location ~ (\.latte|\.md) {
			return 403;
		}
      
		# all other traffic going to index.php file 
		location / {
			try_files  $uri  $uri/  /index.php?$args;
			index index.php;
		}

		location ~ \.php$ {
			try_files  $uri  $uri/  /index.php?$args;
			index  index.html index.htm index.php;
			fastcgi_param PATH_INFO $fastcgi_path_info;
			fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
			fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

			fastcgi_pass 127.0.0.1:9000;
			fastcgi_index index.php;
			fastcgi_split_path_info ^(.+\.php)(/.+)$;
			fastcgi_intercept_errors on;
			include fastcgi_params;
		}
	}

- See [NGINX Beginner’s Guide](http://nginx.org/en/docs/beginners_guide.html) for more information.

### Requirements

- PHP 5.6+
- NGINX or Apache


## Installing from source codes

	git clone git@github.com:sphido/cms.git mywebsite
	cd mywebsite
	

