<?php
namespace cms {

	isset($this) && $this instanceof Sphido or die('Sorry can be executed only from Sphido');

	// check AJAX request
	isAjax() or status(500) and die(json(['message' => 'Not AJAX request, but nice try :-)']));

	// response all AJAX requests
	nocache(json(['message' => 'Well done! It\'s AJAX request']));
}