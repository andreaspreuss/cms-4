<?php
namespace vestibulum;

isset($this) && $this instanceof Vestibulum or die('Sorry can be executed only from Vestibulum');

// check AJAX request
isAjax() or status(500) and nocache(json(['message' => 'Not AJAX request, but nice try :-)']));

// response all AJAX requests
json(['message' => 'Well done! It\'s AJAX request']);