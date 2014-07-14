<?php
(isset($this) && $this instanceof \vestibulum\Vestibulum) or die('Sorry can be executed only from Vestibulum');

header('Content-Type: application/json');

// response to ajax request

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	exit(json_encode(['message' => 'Ajax Request well done!!!']));
}

// not ajax request? response with something else when

exit(json_encode(['message' => 'This will be my JSON message']));