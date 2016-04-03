<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

/**
 * Description of PlainWindow
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class PlainWindow extends \ManiaLive\Gui\Window
{


    protected $xml;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->xml = new \ManiaLive\Gui\Elements\Xml();

    }


    private function detectElements($components)
    {
        $buttonScript = null;
        foreach ($components as $index => $component) {
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel) {
                $this->dicoMessages[$component->getTextid()] = $component->getMessages();
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
                $this->addScriptToMain($component->getScript());
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer) {
                $script = $component->getScript();

                $isset = !array_key_exists($script->getRelPath(), $this->calledScripts);

                if ($isset) {
                    $this->addScriptToLib($script->getlibScript($this, $component));
                }

                if ($isset || $script->multiply()) {
                    $this->calledScripts[$script->getRelPath()] = $script;

                    $dec = $script->getDeclarationScript($this, $component);
                    $this->addScriptToMain($dec);
                    $this->addScriptToWhile($script->getWhileLoopScript($this, $component));
                }
            }

            if ($component instanceof \ManiaLive\Gui\Container) {
                $this->detectElements($component->getComponents());
            }
        }
    }

    protected function onDraw()
    {
        parent::onDraw();

        $this->detectElements($this->getComponents());

        $dico = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dico($this->dicoMessages);
        \ManiaLive\Gui\Manialinks::appendXML($dico->getXml());
    }

    function destroy()
    {
        $this->destroyComponents();
        parent::destroy();

        // echo "window: #";
        foreach ($this as $index => $value) {
            if (\is_object($value)) {

                if ($value instanceof \ManiaLive\Gui\Containable || $value instanceof \ManiaLive\Gui\Container) {
                    //			echo "!";
                    $value->destroyComponents();
                    $value->destroy();
                    unset($this->$index);
                    continue;
                }
                if ($value instanceof \ManiaLive\Gui\Control) {
                    //				echo "*";
                    $value->destroy();
                    unset($this->$index);
                    continue;
                }

                unset($this->$index);
            } else {
                //			echo ".";
                unset($this->$index);
            }
        }
        //	echo "\n";
    }
}