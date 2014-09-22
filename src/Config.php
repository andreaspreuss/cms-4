<?php
namespace vestibulum;

/**
 * Simple singleton config object
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Config {

	/** @var \stdClass */
	private $config;

	/**
	 * Return src directory path
	 *
	 * @param string|null $path
	 * @return string
	 */
	public function src($path = null) {
		return
			realpath(
				(isset($this->config()->src) ? $this->config()->src : $this->config()->src = getcwd() . '/src')
			) . $path;
	}

	/**
	 * Return configuration
	 *
	 * @return \stdClass
	 */
	public function config() {
		return $this->config ? $this->config : $this->config = (object)array_replace_recursive(
			[
				'title' => 'Vestibulum',
				'twig' => [
					'cache' => false,
					'autoescape' => false,
					'debug' => false,
				],
				'markdown' => [
					'cache' => false,
				],
				'src' => getcwd() . '/src/',
				'templates' => getcwd(),
				'meta' => [
					'template' => 'index.latte',
				]
			],
			@include(getcwd() . '/config.php') // intentionally @
		);
	}
}
