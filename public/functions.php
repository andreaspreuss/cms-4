<?php
use vestibulum\Vestibulum;
use vestibulum\Pages;

/**
 * @author Roman Ozana <ozana@omdesign.cz>
 */
function slug(\vestibulum\File $file) {
	global $cms;
	/** @var Vestibulum $cms */

	return $cms->url(str_replace($cms->src(), '', $file->getDir() . '/' . $file->getName()));
}

function menu() {
	global $cms;
	/** @var Vestibulum $cms */

	$pages = Pages::from($cms->src(), ['404'])->toArraySorted();

	$generate = function (array $array, $level = 0) use (&$generate, $cms) {
		$output = '';
		foreach ($array as $current) {
			/** @var \vestibulum\File $current */

			$classes = array_filter(
				[
					$current->id === $cms->meta->id ? 'active' : null,
					$current->isDir() && $current->getRealPath() === dirname($cms->file) ? 'has-active-child' : null,
					$current->isDir() ? 'root' : null,
				]
			);

			$output .= '<li' . ($classes ? ' class="' . implode(' ', $classes) . '"' : null) . '>';

			$path = $current->isDir() ? $current->getRealPath() : dirname($current->file) . '/' . $current->name;
			$path = str_replace(realpath($cms->src()), '', $path);

			if (isset($current->children)) {
				$output .= '<a href="' . $cms->url($path) . '">' . $current->title . '</a>';
				$output .= $generate($current->children, $level + 1);
			} else {
				$output .= '<a href="' . $cms->url($path) . '">' . $current->title . '</a>';
			}
			$output .= '</li>' . PHP_EOL;
		}

		return '<ul>' . $output . '</ul>' . PHP_EOL;
	};

	return $generate($pages);
}