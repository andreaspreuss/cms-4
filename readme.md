# Sphido CMS

[![Build Status](https://travis-ci.org/sphido/cms.svg?branch=master)](https://travis-ci.org/sphido/cms) [![Latest Stable Version](https://poser.pugx.org/sphido/cms/v/stable.png)](https://packagist.org/packages/sphido/cms) [![Total Downloads](https://poser.pugx.org/sphido/cms/downloads.png)](https://packagist.org/packages/sphido/cms) [![Latest Unstable Version](https://poser.pugx.org/sphido/cms/v/unstable.png)](https://packagist.org/packages/sphido/cms) [![License](https://poser.pugx.org/sphido/cms/license.png)](https://packagist.org/packages/sphido/cms)

Sphido is deathly simple, ultra fast, flat file (Markdown, Latte, HTML, PHTML) CMS. Fully customizable.

See more information: http://www.sphido.org/

# How to install

Download latest version from Github and run `composer install`, or just run `composer create-project sphido/cms`.

## Try Sphido CMS with PHP Built-in web server

Follow instructions [require PHP 5.4+](http://php.net/manual/en/features.commandline.webserver.php)

```bash
git clone git@github.com:sphido/cms.git && cd cms && mkdir cache
curl -sS https://getcomposer.org/installer | php
php composer.phar install

php -S localhost:8000 -t public/
```

Then open [http://localhost:8000/](http://localhost:8000/) in your browser

