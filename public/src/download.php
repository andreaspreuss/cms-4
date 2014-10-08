<?php
namespace vestibulum {

	$tags = function () {
		return @file_get_contents(
			'https://api.github.com/repos/OzzyCzech/vestibulum/tags', false,
			stream_context_create(['http' => ['header' => "User-Agent: Vestibulum\r\n"]])
		);
	};

	$file = cache(tmp('github.tags.json'), $tags, 3600, 'json_decode');

	redirect(
		sprintf('https://github.com/OzzyCzech/vestibulum/archive/%s.zip', $file ? reset($file)->name : 'master')
	);
}