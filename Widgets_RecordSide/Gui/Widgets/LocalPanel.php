<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide;

class LocalPanel extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    /** @var \ManiaLive\Gui\Controls\Frame */
    protected $frame;
    protected $items = array();
    protected $bg, $bgFirst, $layer;
    protected $lbl_title, $bg_title;
    protected $_windowFrame;

    /** @var \ManiaLive\Data\Storage */
    public $storage;
    public $timeScript;
    protected $nbFields;

    protected function onConstruct() {
        parent::onConstruct();
        $sizeX = 46;
        $sizeY = 95;
        $this->setName("LocalRecords Panel");
        $this->setScriptEvents();
        $this->storage = \ManiaLive\Data\Storage::getInstance();    
        
        $this->registerScript($this->getScript());

        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround($sizeX, $sizeY);
        $this->bg->setScriptEvents(1);
        $this->bgFirst = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround($sizeX, $sizeY);
        $this->_windowFrame->addComponent($this->bg);
        $this->_windowFrame->addComponent($this->bgFirst);

        $this->lbl_title = new \ManiaLib\Gui\Elements\Label(20, 5);
        $this->lbl_title->setTextSize(1);
        $this->lbl_title->setTextColor("fff");
        $this->lbl_title->setAlign("center", "center");
        $this->_windowFrame->addComponent($this->lbl_title);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "top");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
        $this->_windowFrame->addComponent($this->frame);

        $this->layer = new myButton(5, 5);
        $this->layer->setIcon("Icons128x32_1", "ManiaLinkSwitch");
        $this->layer->setId("setLayer");
        $this->layer->setDescription("Switch from Race view to Score View(Visible on Tab)", 75);
        $this->addComponent($this->layer);

    }
    
    protected function getScript(){
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_RecordSide/Gui/Scripts/PlayerFinish");
        $recCount = \ManiaLivePlugins\eXpansion\LocalRecords\Config::getInstance()->recordsCount;
        $this->timeScript = $script;
        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
        $this->timeScript->setParam("playerTimes", "[]");
        $this->timeScript->setParam("nbRecord", $recCount);
        $this->timeScript->setParam("acceptMaxServerRank", $recCount);
        $this->timeScript->setParam("acceptMaxPlayerRank", "Integer[Text]");
        $this->timeScript->setParam("useMaxPlayerRank", "False");
        $this->timeScript->setParam("acceptMinCp", 0);
        $this->timeScript->setParam("nbFields", 20);
        $this->timeScript->setParam("nbFirstFields", 5);
	$this->timeScript->setParam('varName','LocalTime1');
	$this->timeScript->setParam('getCurrentTimes',Widgets_RecordSide::$secondMap ? "True" : "False");
        return $script;
    }

    public function setNbFields($nb) {
        $this->timeScript->setParam("nbFields", $nb);       
        $this->nbFields = $nb;
    }

    public function setNbFirstFields($nb) {
        $this->timeScript->setParam("nbFirstFields", $nb);
        $this->bgFirst->setSizeY(2);
        $this->bgFirst->setPosY((-4 * $nb) - 4);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize($this->sizeX, $this->sizeY);
        $this->bg->setSize($this->sizeX, 6);
        $this->bgFirst->setSizeX($this->sizeX);
        $this->bg->setPosition(0, -1.5);
        $this->frame->setPosition(($this->sizeX / 2) + 1, -6);
        $this->lbl_title->setPosition(($this->sizeX / 2), -1);
        $this->layer->setPosition($this->sizeX - 4, -1);
        
    }

    function update() {        
        foreach ($this->items as $item)
            $item->destroy();
        $this->items = array();
        $this->frame->clearComponents();

        $index = 1;

        $this->lbl_title->setText('Local Records');


        $recsData = "";
        $nickData = "";

        for ($index = 1; $index <= $this->nbFields; $index++) {
            $this->items[$index - 1] = new Recorditem($index, false);
            $this->frame->addComponent($this->items[$index - 1]);
        }

        $index = 1;
        foreach (Widgets_RecordSide::$localrecords as $record) {
            if ($index > 1) {
                $recsData .= ', ';
                $nickData .= ', ';
            }
            $recsData .= '"' . $record->login . '"=>' . $record->time;
            $nickData .= '"' . $record->login . '"=>"' . $this->fixHyphens($record->nickName) . '"';
            $index++;
        }
        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);

        if (empty($recsData)) {
            $recsData = 'Integer[Text]';
            $nickData = 'Text[Text]';
        } else {
            $recsData = '[' . $recsData . ']';
            $nickData = '[' . $nickData . ']';
        }

        $this->timeScript->setParam("playerTimes", $recsData);
        $this->timeScript->setParam("playerNicks", $nickData);
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = array();

        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

    protected function fixHyphens($string) {
        $out = str_replace('"', "'", $string);
        $out = str_replace('\\', '\\\\', $out);
        $out = str_replace('-', '–', $out);	
        return $out;
    }

}

?>