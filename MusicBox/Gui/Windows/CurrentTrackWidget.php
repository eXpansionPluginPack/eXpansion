<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows;

class CurrentTrackWidget extends \ManiaLive\Gui\Window {

    protected $frame;
    protected $quad;
    private $label;
    public static $musicBoxPlugin;
    protected $background;
    private $action = null;

    function onConstruct() {
        $this->action = $this->createAction(array(self::$musicBoxPlugin, "musicList"), $this->getRecipient());
        $this->setAlign("center");
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line(100, 8));
        $this->frame->setAlign("center", "top");

        $this->quad = new \ManiaLib\Gui\Elements\Quad();
        $this->quad->setStyle("Icons128x32_1");
        $this->quad->setSubStyle("Sound");
        $this->quad->setAlign("left", "center");
        $this->quad->setSize(8, 8);
        $this->frame->addComponent($this->quad);


        $this->label = new \ManiaLib\Gui\Elements\Label(80, 8);
        $this->label->setAlign("left", "center");
        $this->label->setPosY(1);
        $this->label->setTextColor('fff');
        $this->label->setStyle('TextCardMedium');
        $this->label->setAction($this->action);
        $this->label->setTextSize(1);
        $this->frame->addComponent($this->label);


        $this->addComponent($this->frame);
        //$this->label->enableAutonewline();
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