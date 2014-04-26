<?php
namespace vestibulum;

/**
 * Extract metadata from single file
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Metadata {

	/**
	 * Extract main title from markdown
	 *
	 * @param string $content
	 * @return null|string
	 */
	public static function parseTitle($content) {
		$pattern = '/<h1[^>]*>([^<>]+)<\/h1>| *# *([^\n]+?) *#* *(?:\n+|$)/isU';
		if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
			$first = reset($matches);
			return trim(end($first));
		}
	}

	/**
	 * Shorten plain text content
	 *
	 * @see https://github.com/nette/utils/blob/master/src/Utils/Strings.php
	 *
	 * @param $content
	 * @param int $length
	 * @return mixed
	 */
	public static function shorten($content, $length = 128) {
		$s = static::text($content);

		if (strlen(utf8_decode($s)) > $length) {
			if (preg_match('#^.{1,' . $length . '}(?=[\s\x00-/:-@\[-`{-~])#us', trim($s), $matches)) {
				return reset($matches);
			}
			return (function_exists('mb_substr') ? mb_substr($s, 0, $length, 'UTF-8') : iconv_substr(
				$s, 0, $length, 'UTF-8'
			));
		}

		return $s;
	}

	/**
	 * Return plain text from markdown and HTML mix
	 *
	 * @see https://gist.github.com/jbroadway/2836900
	 *
	 * @param string $content
	 * @return mixed
	 */
	public static function text($content) {
		$rules = array(
			'/(#+) ?(.*)/' => '\2', // headers
			'/\[([^\[]+)\]\(([^\)]+)\)/' => '\1', // links
			'/(\*\*|__)(.*?)\1/' => '\2', // bold
			'/(\*|_)(.*?)\1/' => '\2', // emphasis
			'/\~\~(.*?)\~\~/' => '\1', // del
			'/\:\"(.*?)\"\:/' => '\1', // quote
			'/`(.*?)`/' => '\1', // inline code
			'/\s+/' => ' ' // strip spaces
		);

		return trim(preg_replace(array_keys($rules), array_values($rules), strip_tags($content)));
	}

	/**
	 * Parse content and getting metadata
	 *
	 * @param $content
	 * @return array
	 */
	public static function parseMeta($content) {
		preg_match('/<!--(.*)-->/sU', $content, $matches);
		if ($matches && $ini = end($matches)) {
			return parse_ini_string(str_replace(':', '=', $ini), false, INI_SCANNER_RAW);
		}
	}
}
