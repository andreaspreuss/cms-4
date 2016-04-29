<?php
/** @author Roman Ozana <ozana@omdesign.cz> */
return [
	'title' => 'Sphido CMS',
	'cache' => realpath(__DIR__ . '/../cache/'),
	'content' => realpath(__DIR__ . '/pages'),
	'meta' => [
		'author' => 'All: Roman Ozana; e-mail: ozana@omdesign.cz',
		'template' => realpath(__DIR__ . '/layout.latte')
	]
];