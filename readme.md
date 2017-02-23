# Sphido CMS

[![Build Status](https://travis-ci.org/sphido/cms.svg?branch=master)](https://travis-ci.org/sphido/cms) [![Latest Stable Version](https://poser.pugx.org/sphido/cms/v/stable.png)](https://packagist.org/packages/sphido/cms) [![Total Downloads](https://poser.pugx.org/sphido/cms/downloads.png)](https://packagist.org/packages/sphido/cms) [![Latest Unstable Version](https://poser.pugx.org/sphido/cms/v/unstable.png)](https://packagist.org/packages/sphido/cms) [![License](https://poser.pugx.org/sphido/cms/license.png)](https://packagist.org/packages/sphido/cms)

Sphido is deathly simple, ultra fast, flat file (Markdown, Latte, HTML, PHTML) CMS. Fully customisable.

See more information: https://www.sphido.org/

## How to install

Download latest version from Github and run `composer install`, or just run `composer create-project sphido/cms`.

## Try Sphido CMS

Clone source codes from [GitHub](https://github.com/sphido/cms):

```
git clone git@github.com:sphido/cms.git sphido.dev && cd sphido.dev
```

Install dependencies with [composer](https://getcomposer.org/):

```
composer install
```

### Running with Docker

Run Docker and open [http://localhost/](http://localhost/)

```
docker-compose up
```

## Running with PHP only

```
php -S localhost:8000 -t public
``