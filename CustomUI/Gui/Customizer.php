<?php

namespace ManiaLivePlugins\eXpansion\CustomUI\Gui;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\CustomUI\Config;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;
use ManiaLivePlugins\eXpansion\Helpers\Maniascript;

class Customizer extends PlainWidget
{
	/** @var  Script */
	private $script;

	protected function onConstruct()
	{
		parent::onConstruct();
		$this->setName("Customizer");
		$this->script = new Script("CustomUI\\Gui\\Script");
		$this->registerScript($this->script);
	}

	/**
	 * @param Variable[] $variables
	 */
	public function update($variables)
	{
		$code = "";

		foreach ($variables as $variable) {
			$varName = 'ClientUI.'.ucfirst($variable->getName());
			if ($variable instanceof Boolean) {
				$code .= $varName . ' = ' . (($variable->getRawValue()) ? 'True' : 'False') . ";\n";
			} else if ($variable instanceof Int) {
				$code .= $varName . ' = ' . $variable->getRawValue() . ";\n";
			} else if ($variable instanceof BasicList) {
				$value = $variable->getRawValue();
				if (count($value) == 2) {
					$code .= $varName . ".X" . ' = ' . Maniascript::convertType($value[0]) . ";\n";
					$code .= $varName . ".Y" . ' = ' . Maniascript::convertType($value[1]) . ";\n";
				}
			}
		}

		$this->script->setParam('code', $code);
	}

	function destroy()
	{
		$this->destroyComponents();
		parent::destroy();
	}

}

?>
