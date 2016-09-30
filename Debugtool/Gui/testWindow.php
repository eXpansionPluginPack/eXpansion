<?php

namespace ManiaLivePlugins\eXpansion\Debugtool\Gui;

/**
 * Description of testWindow
 *
 * @author Petri
 */
class testWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected function onConstruct()
    {
        parent::onConstruct();
        $quad = new \ManiaLib\Gui\Elements\Quad(50, 25);
        $quad->setImage("file://media/images/test.png", false);
        $this->addComponent($quad);

        $this->setTitle("testwindow");
    }
}
