<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Scripts\PlayerFinish;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Scripts\PlayerFinish_Optimized;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide;

class LocalPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget {

    /** @var \ManiaLive\Gui\Controls\Frame */
    protected $frame;

    /**
     * @var Control[]
     */
    protected $items = array();

    /**
     * @var Quad
     */
    private $bgborder, $bg, $bgTitle, $bgFirst;


    /**
     * @var Button
     */
    private $layer;

    /**
     * @var Quad
     */
    protected $lbl_title;

    /**
     * @var Label
     */
    protected $bg_title;

    /** @var \ManiaLive\Data\Storage */
    public $storage;
    public $timeScript;
    protected $nbFields;

    protected function exp_onBeginConstruct() {
	$sizeX = 46;
	$sizeY = 95;
	$this->setScriptEvents();
	$this->storage = \ManiaLive\Data\Storage::getInstance();
	$this->setName("LocalRecords Panel");
	$this->registerScript($this->getScript());

	$this->storage = \ManiaLive\Data\Storage::getInstance();

	$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
	$this->_windowFrame->setAlign("left", "top");
	$this->_windowFrame->setId("Frame");
	$this->_windowFrame->setScriptEvents(true);
	$this->addComponent($this->_windowFrame);

	$this->bgborder = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
	$this->bgborder->setStyle("Bgs1InRace");
	$this->bgborder->setSubStyle("BgTitleShadow");
	//$this->_windowFrame->addComponent($this->bgborder);

	$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround($sizeX, $sizeY);
	$this->_windowFrame->addComponent($this->bg);

	$this->bgTitle = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY+2);
	$this->bgTitle->setStyle("UiSMSpectatorScoreBig");
	$this->bgTitle->setSubStyle("PlayerSlotCenter");
	$this->bgTitle->setColorize("3af");
	$this->_windowFrame->addComponent($this->bgTitle);

	$this->bgFirst = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
	$this->bgFirst->setBgcolor("aaa5");
	$this->bgFirst->setAlign("center", "top");
	$this->_windowFrame->addComponent($this->bgFirst);

	$this->lbl_title = new \ManiaLib\Gui\Elements\Label(30, 5);
	$this->lbl_title->setTextSize(1);
	$this->lbl_title->setTextColor("fff");
	$this->lbl_title->setStyle("TextCardScores2");

	$this->lbl_title->setAlign("center", "center");
	$this->_windowFrame->addComponent($this->lbl_title);

	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setAlign("left", "top");
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
	$this->_windowFrame->addComponent($this->frame);

	$this->layer = new myButton(5, 5);
	$this->layer->setIcon("UIConstruction_Buttons", "Down");
	$this->layer->setId("setLayer");
	$this->layer->setDescription("Switch from Race view to Score View(Visible on Tab)", 75);
	$this->addComponent($this->layer);

	parent::exp_onBeginConstruct();
    }

    protected function getScript() {
	$script = "";
	if($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT){
	    $script = new PlayerFinish_Optimized();
	}else{
	    $script = new PlayerFinish();
	}
	
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
	$this->timeScript->setParam('varName', 'LocalTime1');
	$this->timeScript->setParam('getCurrentTimes', Widgets_RecordSide::$secondMap ? "True" : "False");
	return $script;
    }

    protected function autoSetPositions() {
	parent::autoSetPositions();
	$nbFields = $this->getParameter('nbFields');
	$nbFieldsFirst = $this->getParameter('nbFirstFields');
	if($nbFields != null && $nbFieldsFirst != null){
	    $this->setNbFields($nbFields);
	    $this->setNbFirstFields($nbFieldsFirst);
	}
    }

    public function setNbFields($nb) {
	$this->timeScript->setParam("nbFields", $nb);
	$this->nbFields = $nb;
	$this->setSizeY(3 + $nb * 4);
    }

    public function setNbFirstFields($nb) {
	$this->timeScript->setParam("nbFirstFields", $nb);
	$this->bgFirst->setSize($this->sizeX / 1.5, 0.3);
	$this->bgFirst->setPosY((-4 * $nb) - 3);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->_windowFrame->setSize($this->sizeX, $this->sizeY);

	$this->bgborder->setSize($this->sizeX+20, $this->sizeY + 2.25);
	$this->bgborder->setPosition(0, 1.5);

	$this->bgFirst->setPosX(($this->sizeX / 2) + 1 );

	$this->bg->setSize($this->sizeX, $this->sizeY + 1);
	
	$this->bgTitle->setSize($this->sizeX, 4.2);
	$this->bgTitle->setPosition(0, 0.75);

	$this->frame->setPosition(($this->sizeX / 2) + 1, -5);
	$this->lbl_title->setPosition(($this->sizeX / 2), -1);
	$this->layer->setPosition($this->sizeX - 5, -1.5);
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
	    $recsData .= '"' . $this->fixDashes($record->login) . '"=>' . $record->time;
	    $nickData .= '"' . $this->fixDashes($record->login) . '"=>"' . $this->fixHyphens($record->nickName) . '"';
	    $index++;
	}

	/*for($i =0; $i < 100; $i++){
	    if ($index > 1) {
		$recsData .= ', ';
		$nickData .= ', ';
	    }
	    $recsData .= '"' . 'player'.$i . '"=>' . (10000+$i);
	    $nickData .= '"' . 'player'.$i . '"=>"' .'player'.$i . '"';
	}*/
	
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
    
    function fixDashes($string) {
	$out = str_replace('--', '––', $string);
	return $out;
    }
    
    protected function fixHyphens($string) {
	$out = str_replace('"', "'", $string);
	$out = str_replace('\\', '\\\\', $out);
	$out = str_replace('-', '–', $out);
	return $out;
    }

}

?>