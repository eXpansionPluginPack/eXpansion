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
class ServerControlMain extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    /** @var \ManiaLivePlugins\eXpansion\Adm\Adm */
    public static $mainPlugin;
    
    private $frame;
    private $closeButton;
    private $actions;

    function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->actions = new \stdClass();
        $this->actions->close = ActionHandler::getInstance()->createAction(array($this, "close"));
        $this->actions->serverOptions = ActionHandler::getInstance()->createAction(array($this, "serverOptions"));
        $this->actions->gameOptions = ActionHandler::getInstance()->createAction(array($this, "gameOptions"));
        $this->actions->matchSettings = ActionHandler::getInstance()->createAction(array($this, "matchSettings"));
        $this->actions->serverManagement = ActionHandler::getInstance()->createAction(array($this, "serverManagement"));
        $this->actions->adminGroups = ActionHandler::getInstance()->createAction(array($this, "adminGroups"));

        $elem = new myButton();
        $elem->setText(__("Server management"));
        $elem->setAction($this->actions->serverManagement);
        $elem->colorize("f00");
        $this->frame->addComponent($elem);

        $elem = new myButton();
        $elem->setText(__("Server options"));
        $elem->setAction($this->actions->serverOptions);
        $this->frame->addComponent($elem);

        $elem = new myButton();
        $elem->setText(__("Game options"));
        $elem->setAction($this->actions->gameOptions);
        $this->frame->addComponent($elem);

        $elem = new myButton();
        $elem->setText(__("Admin Groups"));
        $elem->setAction($this->actions->adminGroups);
        $elem->colorize("0d0");
        $this->frame->addComponent($elem);
        
        $elem = new myButton();
        $elem->setText(__("Match settings"));
        $elem->setAction($this->actions->matchSettings);
        $this->frame->addComponent($elem);

        $this->mainFrame->addComponent($this->frame);

        $this->closeButton = new myButton();
        $this->closeButton->setText(__("Close"));
        $this->closeButton->setAction($this->actions->close);
        $this->mainFrame->addComponent($this->closeButton);
    }

    function serverOptions($login) {
        self::$mainPlugin->serverOptions($login);
    }

    function gameOptions($login) {
        self::$mainPlugin->gameOptions($login);
    }

    function matchSettings($login) {
        self::$mainPlugin->matchSettings($login);
    }

    function serverManagement($login) {
        self::$mainPlugin->serverManagement($login);
    }

    function adminGroups($login) {
        self::$mainPlugin->adminGroups($login);
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
        ActionHandler::getInstance()->deleteAction($this->actions->serverOptions);
        ActionHandler::getInstance()->deleteAction($this->actions->gameOptions);
        ActionHandler::getInstance()->deleteAction($this->actions->matchSettings);
        ActionHandler::getInstance()->deleteAction($this->actions->serverManagement);
        unset($this->actions);
        parent::destroy();
    }

}

?>
