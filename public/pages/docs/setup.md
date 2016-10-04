<!--
id: setup
title: How to Install Sphido CMS
template: ../../layout.docs.latte
-->

# How to setup Sphido CMS

You have three options how to install Sphido CMS: 

## Setup from zip file

1. Download and unzip [Sphido latest version](/download)
2. Change website content:
 - delete or change files in `public/pages`, `public/css`, `public/js` and `public/img`
 - change layout file `public/layut.latte` and remove `ublic/layout.docs.latte`
 - modify settings in `pubic/config.php` nd change `public/functions.php` if needed 
 - change `public/favicon.ico`  
3. Upload everything to yur Aache or NGINX server. See example server configuration for [Apache](https://github.com/sphido/cms/blob/master/.htaccess) or [NGINX](https://github.com/sphido/cms/blob/master/nginx.conf)

## Setup with composer
 
```
mkdir sphido && cd sphido
curl -sS https://getcomposer.org/installer | php
php composer.phar create-project sphido/cms

php -S localhost:8000 -t cms/public/
```

Then open http://localhost:8000/ in your browser then modify files in `public` folder.

## Installing from source code

```
git clone git@github.com:sphido/cms.git sphido && cd sphido && mkdir cache
curl -sS https://getcomposer.org/installer | php
php composer.phar install
	
php -S localhost:8000 -t public/
```

Then open http://localhost:8000/ in your browser then modify files in `public` folder.