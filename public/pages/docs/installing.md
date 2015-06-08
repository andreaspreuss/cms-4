<!--
id: installing
title: How to Install Sphido CMS
template: ../../layout.docs.latte
-->

# How to install Sphido CMS

1. Download and unzip [latest version](/download) or run `php composer.phar create-project om/sphido`.
2. Change files in `public/content` and `public/config.php`
3. Upload everything to your server.

### Requirements

- PHP 5.5+
- Apache / NGINX

### Setup NGINX

Here is **nginx** configuration example:

    server {
	    listen                *:80;
	    server_name           sphido.dev;

	    root   /Users/roman/Work/sphido/public;

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
