<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Controls\InfoLine;

/**
 * Server Control panel Main window
 * 
 * @author Petri
 */
class StatsWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    /** @var \ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics */
    public static $mainPlugin;
    private $frame;
    private $contentFrame;
    private $closeButton;
    private $actions;
    private $btn1, $btn2, $btn3;
    private $btnDb;

    function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        
        $this->actions = new \stdClass();
        $this->actions->close = $this->createAction(array($this, "close"));


        $this->btn1 = new myButton(6, 6);
        $this->btn1->setDescription(__("Number of Players statistics", $login), 30);
        $this->btn1->setAction(\ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics::$serverPlayerAction);
        $this->btn1->colorize("f00");
        $this->btn1->setScale(0.8);
        $this->btn1->setIcon("Icons128x128_1",'Rankings');
        $this->frame->addComponent($this->btn1);

        $this->btn2 = new myButton(6, 6);
        $this->btn2->setDescription(__("Memory usage statistics", $login),30);
        $this->btn2->setAction(\ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics::$serverMemAction);
        $this->btn2->colorize("f00");
        $this->btn2->setScale(0.8);
		$this->btn2->setIcon("http://files.oliver-decramer.com/data/maniaplanet/images/eXpansion/ramStat.png",null);
        $this->frame->addComponent($this->btn2);


        $this->btn3 = new myButton(6, 6);
        $this->btn3->setDescription(__("Cpu  usage statistic", $login), 30);
        $this->btn3->setAction(\ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics::$serverCpuAction);
        $this->btn3->colorize("f00");
        $this->btn3->setScale(0.8);
		$this->btn3->setIcon("http://files.oliver-decramer.com/data/maniaplanet/images/eXpansion/cpuStat.png",null);
        $this->frame->addComponent($this->btn3);

        $this->mainFrame->addComponent($this->frame);
        $this->closeButton = new myButton(30, 5);
        $this->closeButton->setText(__("Close", $login));
        $this->closeButton->setAction($this->actions->close);
        $this->mainFrame->addComponent($this->closeButton);
    }
    
    public function setData($data, \ManiaLive\Data\Storage $storage){
        $this->contentFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->contentFrame->setLayout(new \ManiaLib\Gui\Layouts\Column(80, 100));
        $this->contentFrame->setScale(.8);
        $this->mainFrame->addComponent($this->contentFrame);
        
        $this->contentFrame->addComponent(new InfoLine(25, 'Comment', $storage->server->comment,0));
        $this->contentFrame->addComponent(new InfoLine(5, 'Up Time', $data['upTime'],0));
        $this->contentFrame->addComponent(new InfoLine(5, 'Map Count', sizeof($storage->maps),0));
        
        $this->contentFrame->addComponent(new InfoLine(5, 'Max Players', $storage->server->currentMaxPlayers,0));
        $this->contentFrame->addComponent(new InfoLine(5, 'Average Players', $data['avgPlayer'],0));
        $this->contentFrame->addComponent(new InfoLine(5, 'Max Spectators', $storage->server->currentMaxPlayers,0));
        $this->contentFrame->addComponent(new InfoLine(5, 'Average Spectators', $data['avgSpec'],0));
        $this->contentFrame->addComponent(new InfoLine(5, 'Ladder Limit', $storage->server->ladderServerLimitMin.' - '.$storage->server->ladderServerLimitMax,0));
    
        $label = new \ManiaLib\Gui\Elements\Label(70, 12);
        $label->setText("Visited by ".$data['nbPlayer'].' players from '.$data['nbNation'].' Nations');
        $this->contentFrame->addComponent($label);
        
    }

    function close() {
        $this->Erase($this->getRecipient());
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->frame->setPosition(2, -($this->sizeY - 6));
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
