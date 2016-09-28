<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\AutoUpdate\Gui\Windows;

/**
 * Description of newPHPClass
 *
 * @author Petri
 */
class UpdateProgress extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $updateScript;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->setTitle("eXpansion update");

        $this->gauge = new \ManiaLive\Gui\Elements\Xml();
        $this->gauge->setContent(
            '<gauge id="progressbar" style="EnergyBar" posn="0 -4" sizen="100 7" scriptevents="1" drawbg="1" drawblockbg="1" ratio="0" />'
        );
        $this->addComponent($this->gauge);

        $this->updateScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("AutoUpdate/Gui/Script");
        $this->registerScript($this->updateScript);

        $this->setSize(100, 30);
    }
}
