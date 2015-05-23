<?php
/** @author Roman Ožana <ozana@omdesign.cz> */

namespace sphido {

	/**
	 * Simple update function download Github resources
	 *
	 * @param $resources
	 */
	function update($resources, callable $success = null) {
		foreach ($resources as $output => $parts) {
			@unlink($output); // delete output file
			if (!$output = fopen($output, 'a')) throw new \RuntimeException('File ' . $output . ' cant\'t be open');
			fwrite($output, '<?php' . PHP_EOL);
			foreach ($parts as $part) {

				if (preg_match('/\w+/', $part)) {
					$file = 'https://raw.githubusercontent.com/sphido/' . $part . '/master/src/' . $part . '.php';
				} elseif (strpos($part, 'raw.githubusercontent') !== false) {
					$file = $part;
				} else {
					$file = 'https://raw.githubusercontent.com/sphido/' . $part;
				}

				// combine all files to single one
				if ($content = file_get_contents($file)) {

					$tokens = token_get_all(trim($content) . PHP_EOL);
					if (is_callable($success)) $success($file, $content, $tokens);

					// write content to combined file
					while (list(, $token) = each($tokens)) {
						list($name, $token) = is_array($token) ? $token : [null, $token];
						if ($name === T_OPEN_TAG) continue; // skip <?php
						if ($token === '<?php') continue; // skip <?php
						fwrite($output, $token);
					}
				}
			}
			fclose($output);
		}
	}
}

namespace {
	\sphido\update(
		[
			__DIR__ . '/src/sphido.php' => ['config', 'routing', 'events', 'url'],
			__DIR__ . '/src/functions.json.php' => ['json'],
			__DIR__ . '/src/functions.http.php' => ['http'],
		],
		function ($file) {
			echo $file . PHP_EOL;
		}
	);
}