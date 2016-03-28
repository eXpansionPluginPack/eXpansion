<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\SubTest\Gui\Menu;

/**
 * Description of Test
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class Test extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{
    protected $frame;
    protected $menu;
    public static $plugin;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->setTitle("Context Menu Test");

        $this->menu = new \ManiaLivePlugins\eXpansion\Gui\Elements\ContextMenu(array(self::$plugin, "menu1"));
        $this->menu->addItem('Ignore', "ignore");
        $this->menu->addItem('Kick', "kick");
        $this->menu->addItem('Ban', "ban");
        $this->menu->addItem('Black', "black");
        $this->addComponent($this->menu);


        $this->menu2 = new \ManiaLivePlugins\eXpansion\Gui\Elements\ContextMenu(array(self::$plugin, "menu2"));
        $this->menu2->addItem('Do stuff', "stuff1");
        $this->menu2->addItem('Do some other stuff', "stuff2");
        $this->addComponent($this->menu2);

        $this->setSize(90, 60);

        $storage = \ManiaLive\Data\Storage::getInstance();
        $x       = 0;
        foreach ($storage->players as $login => $player) {
            $label = new \ManiaLib\Gui\Elements\Label(60, 6);
            $label->setStyle("SliderSmall");
            $label->setText($player->nickName);
            $label->setId("contextMenu");
            $label->setAttribute("data-value", $login);
            $label->setAttribute("data-hash", $this->menu->hash);
            $label->setScriptEvents();
            $label->setPosition(0, -($x * 7));
            $this->addComponent($label);
            $x++;
        }


        $label = new \ManiaLib\Gui\Elements\Label(60, 6);
        $label->setStyle("SliderSmall");
        $label->setText("do stuff");
        $label->setId("contextMenu");
        $label->setAttribute("data-value", $login);
        $label->setAttribute("data-hash", $this->menu2->hash);
        $label->setScriptEvents();
        $label->setPosition(60, 0);
        $this->addComponent($label);
    }
}