<?php
namespace cms {

	function add_default_macros(\Latte\Macros\MacroSet $set) {
		$set->addMacro('url', 'echo \url(%node.args);');
	}

	\on('latte.macroset', '\cms\add_default_macros');
}