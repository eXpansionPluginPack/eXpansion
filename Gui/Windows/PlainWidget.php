<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * Description of EmptyWidget
 *
 * @author De Cramer Oliver
 */
class PlainWidget extends \ManiaLive\Gui\Window {
	
	private $dDeclares = "";
    private $scriptLib = "";
    private $wLoop = "";
    private $_script;
    private $_scripts = array();

    protected function onConstruct() {
        parent::onConstruct();

        $this->_script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\PlainWidgetScript");

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    private $calledScripts = array();

    private function detectElements($components) {
        $buttonScript = null;
        foreach ($components as $index => $component) {
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
                $this->addScriptToMain($component->getScript());
            }

            if ($component instanceof \ManiaLive\Gui\Container) {
                $this->detectElements($component->getComponents());
            }

           if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer) {
                $script = $component->getScript();

                $isset = !isset($this->calledScripts[$script->getRelPath()]);

                if($isset){
                    $this->addScriptToLib($script->getlibScript($this, $component));
                }
                
                if ($isset || $script->multiply()) {
                    $this->calledScripts[$script->getRelPath()] = $script;

                    $dec = $script->getDeclarationScript($this, $component);
                    $this->addScriptToMain($dec);
                    $this->addScriptToWhile($script->getWhileLoopScript($this, $component));
                }
            }
        }
    }

    private function getNumber($number) {
        return number_format((float) $number, 2, '.', '');
    }

    protected function onDraw() {
        $this->nbButton = 0;
        $this->dIndex = 0;
        $this->scriptLib = "";
        $this->dDeclares = "";
        $this->wLoop = "";

        $this->calledScripts = array();

        foreach ($this->_scripts as $script) {
            $dec = $script->getDeclarationScript($this, $this);
            $this->addScriptToMain($dec);
            $this->addScriptToLib($script->getlibScript($this, $this));
            $this->addScriptToWhile($script->getWhileLoopScript($this, $this));
            $this->addScriptToMain($script->getEndScript($this));
        }

        $this->detectElements($this->getComponents());
       foreach ($this->calledScripts as $script) {
            $this->addScriptToMain($script->getEndScript($this));
            $script->reset();
        }
        $this->calledScripts = array();


        $this->_script->setParam("dDeclares", $this->dDeclares);
        $this->_script->setParam("scriptLib", $this->scriptLib);
        $this->_script->setParam("wLoop", $this->wLoop);
		
        $this->removeComponent($this->xml);
        $this->xml->setContent($this->_script->getDeclarationScript($this, $this));

        $this->addComponent($this->xml);
        parent::onDraw();
    }

    function closeWindow() {
        $this->erase($this->getRecipient());
    }

    function addScriptToMain($script) {
        $this->dDeclares .= $script;
    }

    function addScriptToWhile($script) {
        $this->wLoop .= $script;
    }

    function addScriptToLib($script) {
        $this->scriptLib .= $script;
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

    public function registerScript($script) {
        $this->_scripts[] = $script;
    }

}

?>
