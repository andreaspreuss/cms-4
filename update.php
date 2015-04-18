<?php
/** @author Roman Ožana <ozana@omdesign.cz> */


namespace sphido {

	/**
	 * Simple update function download Github resources
	 *
	 * @param $output
	 * @param $resources
	 */
	function update($output, $resources, callable $success = null) {
		@unlink($output); // delete output file
		if (!$output = fopen($output, 'a')) throw new \RuntimeException('File ' . $output . ' cant\'t be open');
		fwrite($output, '<?php' . PHP_EOL . '/**@author Roman Ožana <ozana@omdesign.cz>  * /' . PHP_EOL);

		foreach ($resources as $resource) {

			if (preg_match('/\w+/', $resource)) {
				$file = 'https://raw.githubusercontent.com/sphido/' . $resource . '/master/src/' . $resource . '.php';
			} elseif (strpos($resource, 'raw.githubusercontent') !== false) {
				$file = $resource;
			} else {
				$file = 'https://raw.githubusercontent.com/sphido/' . $resource;
			}

			// combine all files to single one
			if ($content = file_get_contents($file)) {
				$tokens = token_get_all($content);
				if (is_callable($success)) $success($file, $content, $tokens);

				// write content to combined file
				while (list(, $token) = each($tokens)) {
					list($name, $token) = is_array($token) ? $token : [null, $token];
					if ($name === T_OPEN_TAG) continue;
					if (strpos($token, '@author') !== false) continue; // skip author
					if ($token === '<?php') continue;
					fwrite($output, $token);

				}
			}
		}
		fclose($output);
	}
}

namespace {
	\sphido\update(
		__DIR__ . '/src/sphido.php',
		[
			'config', 'routing', 'events', 'antispam', 'url'
		],

		function ($file) {
			echo $file . PHP_EOL;
		}
	);
}