<?php

namespace ManiaLivePlugins\eXpansion\DonatePanel\Gui;

use ManiaLivePlugins\eXpansion\DonatePanel\DonatePanel;

class DonatePanelWindow extends \ManiaLive\Gui\Window {

    private $connection;
    private $container;
    public static $donatePlugin;
    private $items = array();

    protected function onConstruct() {
        $this->setSize(80, 4);
        
        $bg = new \ManiaLib\Gui\Elements\Quad(77,5);
        $bg->setAlign("left", "center");
        $bg->setPosition(-13,1.5);
        $bg->setStyle("Bgs1InRace");
        $bg->setSubStyle("BgList");
        $this->addComponent($bg);
                
                
        $this->container = new \ManiaLive\Gui\Controls\Frame(3,0);        
        $this->container->setLayout(new \ManiaLib\Gui\Layouts\Line(100, 3));
        $this->addComponent($this->container);


        $ui = new \ManiaLib\Gui\Elements\Label(13, 2);
        $ui->setAlign('right','bottom');        
        //$ui->setScale();
        $ui->setText('Donate');
        $ui->setStyle('TextStaticVerySmall');
        $ui->setTextColor('fff');
        $this->addComponent($ui);

        $donations = array(50, 100, 500, 1000, 2000);
        $x = 0;
        foreach ($donations as $text) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(25, 6);
            $this->items[$x]->setText($text);
            $this->items[$x]->setScale(0.4);
            $this->items[$x]->setAlign('left','center');
            $this->items[$x]->setAction($this->createAction(array($this, "Donate"), $text));
            $this->container->addComponent($this->items[$x]);
        }
        
    }

    function Donate($login, $amount) {
        self::$donatePlugin->Donate($login, $amount);
    }

    protected function onShow() {
        $posx = 30;
        $posy = 50;
        $this->container->setSize($this->getSizeX(), $this->getSizeX());
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->destroy();

        $this->container->destroy();
        $this->connection = null;
        parent::destroy();
    }

}

?>