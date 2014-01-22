<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
class Widget extends \ManiaLive\Gui\Window {

    private $dDeclares = "";
    private $scriptLib = "";
    private $wLoop = "";
    private $_name = "widget";
    private $move;
    private $axisDisabled = "";
    private $script;
    private $scripts = array();

    protected function onConstruct() {
        parent::onConstruct();

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WidgetScript");
        $this->move = new \ManiaLib\Gui\Elements\Quad(45, 7);
        $this->move->setAlign("left", "center");
        $this->move->setStyle("Icons128x128_Blink");
        $this->move->setSubStyle("ShareBlink");
        $this->move->setScriptEvents();
        $this->move->setId("enableMove");
        $this->addComponent($this->move);

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->move->setSize($this->sizeX, $this->sizeY);
        $this->move->setPosZ(20);
    }

    private $calledScripts = array();

    private function detectElements($components) {
        $buttonScript = null;
        foreach ($components as $index => $component) {
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
                $this->addScriptToMain($component->getScript());
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\Pager) {
                $this->addScriptToMain($component->getScriptDeclares());
                $this->addScriptToWhile($component->getScriptMainLoop());
            }


            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown) {
                $this->addScriptToMain($component->getScriptDeclares($this->dIndex));
                $this->addScriptToLoop($component->getScriptMainLoop($this->dIndex));
                $this->dIndex++;
            }

            if ($component instanceof \ManiaLive\Gui\Container) {
                $this->detectElements($component->getComponents());
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer) {
                $script = $component->getScript();

                $isset = !isset($this->calledScripts[$script->getRelPath()]);

                if ($isset || $script->multiply()) {
                    $this->calledScripts[$script->getRelPath()] = $script;

                    $dec = $script->getDeclarationScript($this, $component);
                    $this->addScriptToMain($dec);
                    $this->addScriptToLib($script->getlibScript($this, $component));
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
        $this->dLoop = "";

        $this->calledScripts = array();
        
        foreach($this->scripts as $script){
            $dec = $script->getDeclarationScript($this, $this);
            $this->addScriptToMain($dec);
            $this->addScriptToLib($script->getlibScript($this, $this));
            $this->addScriptToWhile($script->getWhileLoopScript($this, $this));
            $this->addScriptToMain($script->getEndScript($this));
        }
        
        $this->detectElements($this->getComponents());
        foreach ($this->calledScripts as $script) {
            $this->addScriptToMain($script->getEndScript($this));
        }
        $this->calledScripts = array();


        $this->script->setParam("name", $this->_name);
        $this->script->setParam("axisDisabled", $this->axisDisabled);
        $this->script->setParam("dDeclares", $this->dDeclares);
        $this->script->setParam("scriptLib", $this->scriptLib);
        $this->script->setParam("wLoop", $this->wLoop);

        $this->removeComponent($this->xml);

        $this->xml->setContent($this->script->getDeclarationScript($this, $this));
            
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    function setName($text, $parameter = "") {
        $this->_name = $text;
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

    function setDisableAxis($axis) {
        $this->axisDisabled = $axis;
    }
    
    public function registerScript($script){
        $this->scripts[] = $script;
    }

}

?>
