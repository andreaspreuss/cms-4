<!--
id: installing
title: How to Install Sphido CMS
template: ../../layout.docs.latte
-->

# How to install Sphido CMS

## Installing from zip file

1. Download and unzip [Sphido latest version](/download)
2. Change files in `public/pages` and `public/config.php`
3. Upload everything to your Apache or NGINX server.

See example server configuration for [Apache](https://github.com/sphido/cms/blob/master/.htaccess) or [NGINX](https://github.com/sphido/cms/blob/master/nginx)

## Installing with composer
 
<pre>
mkdir sphido && cd sphido
curl -sS https://getcomposer.org/installer | php
php composer.phar create-project sphido/cms

php -S localhost:8000 -t cms/public/
</pre>

Then open http://localhost:8000/ in your browser. Content files can be found in `public/pages` and configuration is in `public/config.php`.

## Installing from source code

<pre>
git clone git@github.com:sphido/cms.git sphido && cd sphido && mkdir cache
curl -sS https://getcomposer.org/installer | php
php composer.phar install
	
php -S localhost:8000 -t public/
</pre>
  
Then open http://localhost:8000/ in your browser. Content files can be found in `public/pages` and configuration is in `public/config.php`. 
