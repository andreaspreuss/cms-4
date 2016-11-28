<?php
namespace cms {

	use Latte\Macros\MacroSet;

	function default_macros(MacroSet $set) {
		$set->addMacro('url', 'echo \url(%node.args);');
	}

	on(MacroSet::class, '\cms\default_macros');
}