<?php

namespace ManiaLivePlugins\eXpansion\DonatePanel\Gui;

use ManiaLivePlugins\eXpansion\DonatePanel\DonatePanel;

class DonatePanelWindow extends \ManiaLive\Gui\Window {

    private $connection;
    private $container;
    public static $donatePlugin;

    protected function onConstruct() {
        // set a default size for the window.
        $this->setSize(100, 4);
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        // creating the panel which serves, as described,
        // as the window’s background

        $this->container = new \ManiaLive\Gui\Controls\Frame();
        $this->container->clearComponents();
        $this->container->setPosition(40, -85);
        $this->container->setLayout(new \ManiaLib\Gui\Layouts\Line(100, 4));



        $ui = new \ManiaLib\Gui\Elements\Label(12, 3);
        $ui->setHalign('right');
        $ui->setValign('top');
        //$ui->setScale();
        $ui->setText('$fff$sDonate:');
        $this->container->addComponent($ui);

        $donations = array(100, 500, 1000, 2000, 5000);
        foreach ($donations as $text) {
            $ui = new \ManiaLib\Gui\Elements\Label(14, 3);
            $ui->setHalign('center');
            $ui->setValign('top');
            $ui->setText('$fff$s' . $text);
            $ui->setStyle('TextCardMedium');
            $ui->setScale(0.8);
            $ui->setAction($this->createAction(array($this, "Donate"), $text));
            $this->container->addComponent($ui);
        }
        $this->addComponent($this->container);
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
        $this->container->destroy();
        $this->connection = null;
        parent::destroy();
    }

}

?>