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
	 * Return src dirname
	 *
	 * @return string
	 */
	public function src() {
		return (isset($this->config()->src) ? realpath($this->config()->src) : $this->config()->src = getcwd() . '/src/');
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
					'template' => 'index.twig',
				]
			],
			@include(getcwd() . '/config.php') // intentionally @
		);
	}
}
