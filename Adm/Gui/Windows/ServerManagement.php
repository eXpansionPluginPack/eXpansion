<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

/**
 * Server Controlpanel Main window
 * 
 * @author Petri
 */
class ServerManagement extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $frame;
    private $closeButton;
    private $actions;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->actions = new \stdClass();
        $this->actions->close = ActionHandler::getInstance()->createAction(array($this, "close"));
        $this->actions->stopServer = ActionHandler::getInstance()->createAction(array($this, "stopServer"));
        $this->actions->stopManialive = ActionHandler::getInstance()->createAction(array($this, "stopManialive"));

        $elem = new myButton(16, 6);
        $elem->setText(__("Stop Server"));
        $elem->setAction($this->actions->stopServer);
        $elem->colorize("d00");
        $this->frame->addComponent($elem);

        $elem = new myButton(16, 6);
        $elem->setText(__("Stop Manialive"));
        $elem->setAction($this->actions->stopManialive);
        $elem->colorize("d00");
        $this->frame->addComponent($elem);


        $this->mainFrame->addComponent($this->frame);

        $this->closeButton = new myButton(16, 6);
        $this->closeButton->setText(__("Cancel"));
        $this->closeButton->setAction($this->actions->close);
        $this->mainFrame->addComponent($this->closeButton);
    }

    function stopServer($login) {
        $this->connection->stopServer();
    }

    function stopManialive($login) {
        die();
    }    

    function close() {
        $this->Erase($this->getRecipient());
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->frame->setPosition(8, -10);
        $this->closeButton->setPosition($this->sizeX - 18, -($this->sizeY - 6));
    }

    function destroy() {
        ActionHandler::getInstance()->deleteAction($this->actions->close);
        ActionHandler::getInstance()->deleteAction($this->actions->stopServer);
        ActionHandler::getInstance()->deleteAction($this->actions->stopManialive);        
        unset($this->actions);
        parent::destroy();
    }

}

?>
