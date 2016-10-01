<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class SimpleCheckbox extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $button;
    protected $active = false;
    protected $action;

    public function __construct($sizeX = 4, $sizeY = 4)
    {
        $this->action = $this->createAction(array($this, 'toggleActive'));
        $config = Config::getInstance();
        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('left', 'center2');
        $this->button->setStyle('Icons64x64_1');
        $this->button->setSubStyle('GenericButton');
        $this->button->setAction($this->action);
        $this->button->setScriptEvents(true);
        $this->addComponent($this->button);

        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->button->setSize($this->sizeX, $this->sizeY);
        $this->button->setPosition(0, -0.5);
    }

    protected function onDraw()
    {
        if ($this->active) {
            $this->button->setColorize("0f0");
        } else {
            $this->button->setColorize("f00");
        }
    }

    public function setStatus($boolean)
    {
        $this->active = $boolean;
    }

    public function getStatus()
    {
        return $this->active;
    }

    public function toggleActive($login)
    {
        $this->active = !$this->active;
        $this->redraw();
    }

    public function setAction($action)
    {
        $this->button->setAction($action);
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        parent::destroy();
    }
}
