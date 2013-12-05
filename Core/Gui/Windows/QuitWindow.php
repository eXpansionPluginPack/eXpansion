<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use ManiaLib\Gui\Manialink;

class QuitWindow {

    protected $xml;
    protected $entry;
    protected $label;
    /** @var \ManiaLive\Data\Storage */
    private $storage;
    public function getXml() {
        Manialink::load();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
       

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setPosX(0, 0);
        $frame->setAlign("center", "center");

        $background = new \ManiaLib\Gui\Elements\Quad(160, 50);
        $background->setStyle("Bgs1InRace");
        $background->setPosition(0,0);
        $background->setAlign("center", "top");
        $background->setSubStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgCard);
        $frame->addComponent($background);
        
        $server = new \ManiaLib\Gui\Elements\Label(160,7);
        $server->setAlign("center", "top");
        $server->setPosition(0,10);
        $server->setText($this->storage->server->name);
        $server->setStyle("TextRaceMessageBig");
        $server->save();
        
        $frame->save();

       
        return Manialink::render(true);
    }

}

?>
