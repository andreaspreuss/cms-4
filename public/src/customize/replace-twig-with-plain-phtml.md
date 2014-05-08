<!-- do not delete -->

### Replace Twig with plain phtml

Follow example is for those who have an performance obsession :-). First add `index.phtml` or `index.php` to your current working directory:

    <!DOCTYPE html>
    <html lang="en" ng-app="help">
    <head>
      <meta charset="utf-8">
      <title><?= $file->title ?> | <?= $config->title ?></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="<?= $meta->description ?>">
      <meta name="robots" content="all"/>
      <meta name="author" content="<?= $meta->author ?>"/>
      <link rel="shortcut icon" href="<?= $this->url('favicon.ico') ?>" type="image/x-icon"/>
    </head>

    <div class="container">
      <? menu(); /* call your functions */ ?>
      <?= $content ?>
    </div>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

    </body>
    </html>

Create markdown file, and change metadata:

    <!--
    template: index.phtml
    -->

This is it! Now can have request even **under 6 ms**

<a href="/customize" class="btn btn-primary">Return to Customize</a>