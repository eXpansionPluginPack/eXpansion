<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLive\Gui\ActionHandler;

class SimpleCheckbox extends \ManiaLivePlugins\eXpansion\Gui\Control {

    protected $button;
    protected $active = false;
    protected $action;

    function __construct($sizeX = 4, $sizeY = 4) {
        $this->action = $this->createAction(array($this, 'toggleActive'));
        $config = Config::getInstance();
        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('left', 'center2');
        $this->button->setImage($config->checkbox, true);
        $this->button->setAction($this->action);
        $this->button->setScriptEvents(true);
        $this->addComponent($this->button);

        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->button->setSize($this->sizeX, $this->sizeY);
        $this->button->setPosition(0, -0.5);
    }

    function onDraw() {
        $config = Config::getInstance();

        if ($this->active) {
            $this->button->setImage($config->checkboxActive, true);
        } else {
            $this->button->setImage($config->checkbox, true);
        }
    }

    function setStatus($boolean) {
        $this->active = $boolean;
    }

    function getStatus() {
        return $this->active;
    }

    function toggleActive($login) {
        $this->active = !$this->active;
        $this->redraw();
    }

    function setAction($action) {
        $this->button->setAction($action);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        parent::destroy();
    }

}
