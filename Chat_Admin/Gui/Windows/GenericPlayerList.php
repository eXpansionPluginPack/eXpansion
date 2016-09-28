<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;

class GenericPlayerList extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;
    protected $inputbox;
    protected $button;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->inputbox = new Inputbox("login", 50);
        $this->inputbox->setPosition(0, -6);
        $this->inputbox->setLabel("Login to add");
        $this->addComponent($this->inputbox);

        $this->button = new Button();
        $this->button->setPosition(55, -6);
        $this->button->setText(eXpGetMessage("add"));
        $this->addComponent($this->button);


        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->addComponent($this->pager);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 5, $this->sizeY - 20);
        $this->pager->setPosition(2, -12);
    }

    /**
     *
     * @param type $items ArrayOfObject
     */
    public function populateList($items)
    {
        $this->pager->clearItems();

        foreach ($items as $item) {
            $this->pager->addItem($item);
        }
    }

    public function setAction($action)
    {
        $this->button->setAction($action);
    }
}
