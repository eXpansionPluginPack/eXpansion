<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

/**
 * Server Control panel Main window
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
    private $btn1, $btn2, $btn3, $btn4, $btn5, $btn6, $btn7;
    private $btnDb;

    function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(120, 60));

        $this->actions = new \stdClass();
        $this->actions->close = $this->createAction(array($this, "close"));
        $this->actions->serverOptions = $this->createAction(array(self::$mainPlugin, "serverOptions"));
        $this->actions->gameOptions = $this->createAction(array(self::$mainPlugin, "gameOptions"));
        $this->actions->matchSettings = $this->createAction(array(self::$mainPlugin, "matchSettings"));
        $this->actions->serverManagement = $this->createAction(array(self::$mainPlugin, "serverManagement"));
        $this->actions->adminGroups = $this->createAction(array(self::$mainPlugin, "adminGroups"));
        $this->actions->scriptSettings = $this->createAction(array(self::$mainPlugin, "scriptSettings"));
        $this->actions->forceScores = $this->createAction(array(self::$mainPlugin, "forceScores"));
        $this->actions->roundPoints = $this->createAction(array(self::$mainPlugin, "roundPoints"));
        $this->actions->dbTools = $this->createAction(array(self::$mainPlugin, "dbTools"));


        $this->btn1 = new myButton(40, 6);
        $this->btn1->setText(__("Server management", $login));
        $this->btn1->setAction($this->actions->serverManagement);
        $this->btn1->colorize("f00");
        $this->btn1->setScale(0.5);
        $this->frame->addComponent($this->btn1);

        $this->btn2 = new myButton(40, 6);
        $this->btn2->setText(__("Server options", $login));
        $this->btn2->setAction($this->actions->serverOptions);
        $this->btn2->setScale(0.5);
        $this->frame->addComponent($this->btn2);

        $this->btn3 = new myButton(40, 6);
        $this->btn3->setText(__("Game options", $login));
        $this->btn3->setAction($this->actions->gameOptions);
        $this->btn3->setScale(0.5);
        $this->frame->addComponent($this->btn3);

        $this->btn4 = new myButton(40, 6);
        $this->btn4->setText(__("Admin Groups", $login));
        $this->btn4->setAction($this->actions->adminGroups);
        $this->btn4->colorize("0d0");
        $this->btn4->setScale(0.5);
        $this->frame->addComponent($this->btn4);

        $this->btn5 = new myButton(40, 6);
        $this->btn5->setText(__("Match settings", $login));
        $this->btn5->setAction($this->actions->matchSettings);
        $this->btn5->setScale(0.5);
        $this->frame->addComponent($this->btn5);

        $this->btn6 = new myButton(40, 6);
        $this->btn6->setText(__("ScriptMode settings", $login));
        $this->btn6->setAction($this->actions->scriptSettings);
        $this->btn6->setScale(0.5);
        $this->frame->addComponent($this->btn6);

        $this->btn7 = new myButton(40, 6);
        $this->btn7->setText(__("Force Scores", $login));
        $this->btn7->setAction($this->actions->forceScores);
        $this->btn7->setScale(0.5);
        $this->frame->addComponent($this->btn7);

        $this->btn8 = new myButton(40, 6);
        $this->btn8->setText(__("Round points", $login));
        $this->btn8->setAction($this->actions->roundPoints);
        $this->btn8->setScale(0.5);
        $this->frame->addComponent($this->btn8);

        $this->btnDb = new myButton(40, 6);
        $this->btnDb->setText(__("Database tools", $login));
        $this->btnDb->setAction($this->actions->dbTools);
        $this->btnDb->setScale(0.5);
        $this->frame->addComponent($this->btnDb);

        $this->mainFrame->addComponent($this->frame);

        $this->closeButton = new myButton(30, 5);
        $this->closeButton->setText(__("Close", $login));
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

    function scriptSettings($login) {
        self::$mainPlugin->scriptSettings($login);
    }

    function serverManagement($login) {
        self::$mainPlugin->serverManagement($login);
    }

    function adminGroups($login) {
        self::$mainPlugin->adminGroups($login);
    }

    function forceScores($login) {
        self::$mainPlugin->forceScores($login);
    }

    function dbTools($login) {
        self::$mainPlugin->dbTools($login);
    }

    function close() {
        $this->Erase($this->getRecipient());
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->frame->setPosition(0, -2);
        $this->closeButton->setPosition($this->sizeX - $this->closeButton->sizeX, -($this->sizeY - 6));
    }

    function destroy() {

        unset($this->actions);
        $this->btn1->destroy();
        $this->btn2->destroy();
        $this->btn3->destroy();
        $this->btn4->destroy();
        $this->btn5->destroy();
        $this->btn6->destroy();
        $this->btn7->destroy();
        $this->btn8->destroy();
        $this->btnDb->destroy();

        $this->frame->clearComponents();
        $this->connection = null;
        $this->storage = null;

        parent::destroy();
    }

}

?>
