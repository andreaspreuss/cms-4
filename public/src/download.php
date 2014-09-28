<?php
namespace vestibulum;

$file = @json_decode(@file_get_contents('https://api.github.com/repos/OzzyCzech/vestibulum/tags', false,
	stream_context_create(['http' => ['header' => "User-Agent: Vestibulum\r\n"]])
));

redirect(sprintf('https://github.com/OzzyCzech/vestibulum/archive/%s.zip', $file ? reset($file)->name : 'master'));