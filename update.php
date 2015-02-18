<?php
// TODO read from json
@unlink(__DIR__ . '/src/sphido.php');

if (!$sphido = fopen(__DIR__ . '/src/sphido.php', 'a')) die('UPS');
fwrite($sphido, '<?php' . PHP_EOL . '/** @author Roman Ozana <ozana@omdesign.cz> */' . PHP_EOL);

foreach (
	[
		'sphido/config/master/src/config.php',
		'sphido/routing/master/src/routing.php',
		'sphido/events/master/src/events.php',
		'sphido/url/master/src/url.php',
	] as $file) {
	$tokens = token_get_all(file_get_contents('https://raw.githubusercontent.com/' . $file));
	echo $file . PHP_EOL;
	while (list(, $token) = each($tokens)) {
		list($name, $token) = is_array($token) ? $token : [null, $token];
		if ($name === T_OPEN_TAG) continue;
		if (strpos($token, '@author') !== false) continue;
		if ($token === '<?php') continue;
		fwrite($sphido, $token);
	}
}

fclose($sphido);
