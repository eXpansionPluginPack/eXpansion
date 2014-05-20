<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Gui;
class TimePanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget {

    const Mode_BestOfAll = 1;
    const Mode_PersonalBest = 2;
    const Mode_None = 3;
    const Mode_All = 4;

    protected $checkpoint;
    protected $time;
    protected $audio;
    protected $frame;
    protected $position;
    protected $top1;
    private $totalCp = 0;
    private $lapRace = false;
    private $nScript;
    private $target = "";

    /** @var \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record[] */
    public static $localrecords = array();
    public static $dedirecords = array();

    protected function onConstruct() {
	parent::onConstruct();
	$login = $this->getRecipient();

	$frame = new \ManiaLive\Gui\Controls\Frame();
	$frame->setPosition(20, -6);
	$this->addComponent($frame);

	$this->setAlign("center", "center");
	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setAlign("center", "center");
	$this->frame->setSize(40, 7);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line(40, 7));
	$frame->addComponent($this->frame);

	$this->checkpoint = new \ManiaLib\Gui\Elements\Label(22, 4);
	$this->checkpoint->setTextColor("fff");
	$this->checkpoint->setAlign("right", "center");
	$this->checkpoint->setText('');
	$this->checkpoint->setId("Cp");
	$this->checkpoint->setPosX(20);
	$this->checkpoint->setScriptEvents();
	$this->frame->addComponent($this->checkpoint);

	$this->time = new \ManiaLib\Gui\Elements\Label(20, 4);
	$this->time->setAlign("left", "center");
	$this->time->setStyle("TextRaceChrono");
	$this->time->setText('');
	$this->time->setId("Label");
	$this->time->setScriptEvents();
	$this->time->setTextSize(4);
	$this->frame->addComponent($this->time);

	$this->position = new \ManiaLib\Gui\Elements\Label(40, 4);
	$this->position->setAlign("left", "center");
	$this->position->setStyle("TextTitle2");
	$this->setPosX(-40);
	$frame->addComponent($this->position);

	$this->top1 = new \ManiaLib\Gui\Elements\Label(40, 4);
	$this->top1->setAlign("left", "center");
	$this->top1->setStyle("TextTitle2");
	$this->setPosX(-80);
	$frame->addComponent($this->top1);

	$this->audio = new \ManiaLib\Gui\Elements\Audio();
	$this->audio->setPosY(260);
	$frame->addComponent($this->audio);

	$this->nScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script('Widgets_Times/Gui/Scripts_Time');
	$this->registerScript($this->nScript);

	$this->setName("Player Time Panel");
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
    }

    function setTarget($login) {
	$this->target = Gui::fixHyphens($login);
    }

    function setMapInfo(\Maniaplanet\DedicatedServer\Structures\Map $map) {
	$this->totalCp = $map->nbCheckpoints;
	$this->lapRace = $map->lapRace;
    }

    function onDraw() {
	$record = \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::getObjbyPropValue(self::$localrecords, "login", $this->target);
	$checkpoints = "[ -1 ]";
	if ($record instanceof \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record && sizeof($record->ScoreCheckpoints) > $this->totalCp-1)  {
	    $checkpoints = "[" . implode(",", $record->ScoreCheckpoints) . ", $record->time]";	    
	}else{
	    $checkpoints = '[';
	    for($i = 0; $i < $this->totalCp+2; $i++){
		if($i > 0){
		    $checkpoints .= ', ';
		}
		$checkpoints .= -1;
	    }
	    $checkpoints .= ']';
	}
	
	$bool = "False";
	if ($this->lapRace)
	    $bool = "True";
	
	$this->nScript->setParam('checkpoints', $checkpoints);
	$this->nScript->setParam('totalCp', $this->totalCp);
	$this->nScript->setParam('target', Gui::fixHyphens($this->target));
	$this->nScript->setParam('lapRace', $bool);
	parent::onDraw();
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
