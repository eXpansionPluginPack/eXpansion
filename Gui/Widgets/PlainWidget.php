<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

/**
 * Description of EmptyWidget
 *
 * @author De Cramer Oliver
 */
class PlainWidget extends \ManiaLive\Gui\Window
{

	private $dDeclares = "";

	private $scriptLib = "";

	private $wLoop = "";

	private $_script;

	private $_scripts = array();

	private $dicoMessages = array();

	protected function onConstruct()
	{
		parent::onConstruct();

		$this->setPosZ(-30);

		$this->_script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\PlainWidgetScript");

		$this->xml = new \ManiaLive\Gui\Elements\Xml();
	}

	private $calledScripts = array();

	private function detectElements($components)
	{
		foreach ($components as $index => $component) {
			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel) {
				$this->dicoMessages[$component->getTextid()] = $component->getMessages();
			}

			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
				$this->addScriptToMain($component->getScript());
			}

			if ($component instanceof \ManiaLive\Gui\Container) {
				$this->detectElements($component->getComponents(), $component);
			}

			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer) {
				$script = $component->getScript();
				$this->applyScript($script, $component);
			}

			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\MultipleScriptedContainer) {
				$scripts = $component->getScripts();
				if (!empty($scripts)) {
					foreach ($scripts as $script)
						$this->applyScript($script, $component);
				}
			}
		}
	}

	/**
	 * Will apply all script to the current window
	 */
	private function applyAllScripts()
	{
		$this->scriptLib = "";
		$this->dDeclares = "";
		$this->wLoop = "";

		$this->calledScripts = array();

		foreach ($this->_scripts as $script) {
			$this->applyScript($script, $this);
		}


		$this->detectElements($this->getComponents());

		foreach ($this->calledScripts as $script) {
			$this->addScriptToMain($script->getEndScript($this));
			$script->reset();
		}

		$this->calledScripts = array();
	}

	/**
	 * Will apply a single script
	 *
	 * @param Script $script The script to apply if possible to the current window
	 * @param mixed $component the component that adds this script
	 */
	private function applyScript(Script $script, $component)
	{
		$isset = !isset($this->calledScripts[$script->getRelPath()]);

		if ($isset || $script->multiply()) {

			$libs = $script->getLibraries();
			if (!empty($libs)) {
				foreach ($libs as $libSCript) {
					$this->applyScript($libSCript, $component);
				}
			}

			$this->calledScripts[$script->getRelPath()] = $script;

			$this->addScriptToMain($script->getDeclarationScript($this, $component));
			$this->addScriptToWhile($script->getWhileLoopScript($this, $component));
		}

		if ($isset) {
			$this->addScriptToLib($script->getlibScript($this, $component));
		}
	}

	protected function onDraw()
	{
		parent::onDraw();

		//Applying all scripts to the current widget
		$this->applyAllScripts();

		$this->_script->setParam("dDeclares", $this->dDeclares);
		$this->_script->setParam("scriptLib", $this->scriptLib);
		$this->_script->setParam("wLoop", $this->wLoop);

		$this->removeComponent($this->xml);
		$this->xml->setContent($this->_script->getDeclarationScript($this, $this));

		$this->addComponent($this->xml);

		$dico = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dico($this->dicoMessages);
		\ManiaLive\Gui\Manialinks::appendXML($dico->getXml());
	}

	function closeWindow()
	{
		$this->erase($this->getRecipient());
	}

	function addScriptToMain($script)
	{
		$this->dDeclares .= $script;
	}

	function addScriptToWhile($script)
	{
		$this->wLoop .= $script;
	}

	function addScriptToLib($script)
	{
		$this->scriptLib .= $script;
	}

	function destroy()
	{
		$this->_scripts = array();
		$this->destroyComponents();
		parent::destroy();
	}

	/**
	 * Registers a script to widget instance
	 *
	 * @param \ManiaLivePlugins\eXpansion\Gui\Structures\Script $script
	 */
	public function registerScript(\ManiaLivePlugins\eXpansion\Gui\Structures\Script $script)
	{
		$this->_scripts[] = $script;
	}

	/**
	 * formats number for maniascript
	 * @param numeric $number
	 * @return string
	 */
	public function getNumber($number)
	{
		return number_format((float) $number, 2, '.', '');
	}

	public function getBoolean($boolean)
	{
		if ($boolean)
			return "True";
		return "False";
	}

	public function isDestroyed(){
		return !isset($this->_scripts);
	}

}

?>
