<!--
id: install
title: How to Install Vestibulum CMS
menu: Install
order : 2
-->

# How to Install Vestibulum

1. [Download Composer](http://getcomposer.org/download) `curl -s http://getcomposer.org/installer | php`
1. Run `php composer.phar create-project om/vestibulum`
2. Update content in `public/src` and `public/config.php`
3. Upload files to your server

### Requirements

- PHP 5.5 +
- Apache / NGINX

### Setup NGINX

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


### Setup Apache

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