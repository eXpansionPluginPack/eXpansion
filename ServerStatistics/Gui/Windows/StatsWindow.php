<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Windows;

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
class StatsWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    /** @var \ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics */
    public static $mainPlugin;
    private $frame;
    private $closeButton;
    private $actions;
    private $btn1, $btn2, $btn3, $btn4, $btn5, $btn6, $btn7;
    private $btnDb;

    function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(120, 60));

        $this->actions = new \stdClass();
        $this->actions->close = $this->createAction(array($this, "close"));


        $this->btn1 = new myButton(40, 6);
        $this->btn1->setText(__("Players", $login));
        $this->btn1->setAction($this->createAction(array($this, "players")));
        $this->btn1->colorize("f00");
        $this->btn1->setScale(0.5);
        $this->frame->addComponent($this->btn1);

        $this->btn2 = new myButton(40, 6);
        $this->btn2->setText(__("Memory", $login));
        $this->btn2->setAction($this->createAction(array($this, "memory")));
        $this->btn2->colorize("f00");
        $this->btn2->setScale(0.5);
        $this->frame->addComponent($this->btn2);


        $this->btn3 = new myButton(40, 6);
        $this->btn3->setText(__("Cpu", $login));
        $this->btn3->setAction($this->createAction(array($this, "cpu")));
        $this->btn3->colorize("f00");
        $this->btn3->setScale(0.5);
        $this->frame->addComponent($this->btn3);

        $this->mainFrame->addComponent($this->frame);
        $this->closeButton = new myButton(30, 5);
        $this->closeButton->setText(__("Close", $login));
        $this->closeButton->setAction($this->actions->close);
        $this->mainFrame->addComponent($this->closeButton);
    }

    function players($login) {
        self::$mainPlugin->showPlayers($login);
    }

    function memory($login) {
        self::$mainPlugin->showMemory($login);
    }

    function cpu($login) {
        self::$mainPlugin->showCpu($login);
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
 
        $this->frame->clearComponents();
        $this->connection = null;
        $this->storage = null;

        parent::destroy();
    }

}

?>
