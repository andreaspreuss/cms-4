<?php
namespace vestibulum;
use Latte\Engine;

/**
 * Multiple pages loaders
 *
 * @author Roman Ožana <ozana@omdesign.cz>
 */
trait Render {

	/**
	 * @return Engine
	 */
	public function getLatte() {
		$latte = new \Latte\Engine();
		$latte->setTempDirectory(tmp());

		$set = new \Latte\Macros\MacroSet($latte->getCompiler());
		$set->addMacro('url', 'echo \vestibulum\url(%node.args);');

		return filter('latte', $latte);
	}

	/**
	 * Render string content
	 *
	 * @return string
	 */
	public function __toString() {
		try {
			return handle('render', [$this, 'render'], $this);
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
}
