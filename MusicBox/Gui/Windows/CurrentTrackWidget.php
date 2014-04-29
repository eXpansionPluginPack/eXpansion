<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows;

class CurrentTrackWidget extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    protected $frame;
    protected $quad;
    private $label;
    public static $musicBoxPlugin;
    private $background;
    private $action = null;

    function onConstruct() {
	$this->setName("Music Widget");
	
        $this->action = $this->createAction(array(self::$musicBoxPlugin, "musicList"), $this->getRecipient());
        $this->setAlign("center");
	
	$this->background = new \ManiaLib\Gui\Elements\Quad(100,8);	
	$this->background->setAlign("center", "top");
	$this->background->setStyle("UiSMSpectatorScoreBig");
	$this->background->setSubStyle("PlayerSlotCenter");
	$this->background->setColorize('ff0');
	$this->background->setAction($this->action);
	$this->addComponent($this->background);
	
        $this->frame = new \ManiaLive\Gui\Controls\Frame(0,-3.5);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line(100, 8));
        $this->frame->setAlign("center", "top");
	$this->frame->setSizeX(90);

	
		
       /* $this->quad = new \ManiaLib\Gui\Elements\Quad();
        $this->quad->setStyle("Icons128x32_1");
        $this->quad->setSubStyle("Sound");
        $this->quad->setAlign("left", "center");
        $this->quad->setSize(8, 8);
        $this->frame->addComponent($this->quad); */
	$label = new \ManiaLib\Gui\Elements\Label(16, 8);
	$label->setAlign("left", "center");
	$label->setTextColor('fff');
	$label->setText("Now Playing: ");
	$label->setStyle('TextCardSmallScores2');        
        $label->setTextSize(1);
	$this->frame->addComponent($label);
	
        $this->label = new \ManiaLib\Gui\Elements\Label(80, 8);
        $this->label->setAlign("left", "center");        
        $this->label->setTextColor('fff');
        $this->label->setStyle('TextCardSmallScores2');        
        $this->label->setTextSize(1);
        $this->frame->addComponent($this->label);


        $this->addComponent($this->frame);
	
        //$this->label->enableAutonewline();
	parent::onConstruct();
    }

    function onResize($oldX, $oldY) {
     
    }

    function setSong(\ManiaLivePlugins\eXpansion\MusicBox\Structures\Song $song) {
        $this->label->setText($song->artist . " - " . $song->title);
    }

    function destroy() {
        parent::destroy();
    }

}

?>