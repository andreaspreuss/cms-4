<!--
title: Install
order : 2
-->

[![Latest Stable Version]()](https://packagist.org/packages/om/vestibulum) [![Total Downloads](https://poser.pugx.org/om/vestibulum/downloads.png)](https://packagist.org/packages/om/vestibulum) [![Latest Unstable Version](https://poser.pugx.org/om/vestibulum/v/unstable.png)](https://packagist.org/packages/om/vestibulum) [![License](https://poser.pugx.org/om/vestibulum/license.png)](https://packagist.org/packages/om/vestibulum)

# How to Install Vestibulum

## Installing with Composer

Download the [`composer.phar`](https://getcomposer.org/composer.phar) locally or install [Composer](https://getcomposer.org/) globally:

    curl -s https://getcomposer.org/installer | php

Run the following command for a local installation:

    php composer.phar require om/vestibulum:*

Or for a global installation, run the following command:

    composer require om/vestibulum:*

You can also add follow lines to your `composer.json` and run the `composer update` command:

    "require": {
      "om/vestibulum": "*"
    }

See https://getcomposer.org/ for more information and documentation.

## Requirements

- PHP 5.5 +
- Apache / NGINX


## Setup NGINX

Here is **nginx** configuration example:

    server {
	    listen                *:80;
	    server_name           vestibulum.dev;

	    root   /Users/roman/Work/vestibulum/public;

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


## Setup Apache

Here is **Apache** `.htaccess` configuration example:

    <IfModule mod_rewrite.c>
    	RewriteEngine On
    	# RewriteBase /

    	# prevents files starting with dot to be viewed by browser
    	RewriteRule /\.|^\. - [F]

    	# front controller
    	RewriteCond %{REQUEST_FILENAME} !-f
    	RewriteCond %{REQUEST_FILENAME} !-d
    	RewriteRule !\.(pdf|js|ico|gif|jpg|png|css|rar|zip|tar\.gz)$ index.php [L]
    </IfModule>

## Example

Check this page source on GitHub [https://github.com/OzzyCzech/vestibulum/tree/master/public](https://github.com/OzzyCzech/vestibulum/tree/master/public)