<?php
namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use ManiaLib\Gui\Elements\Bgs1InRace;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Manialink;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Config;

class QuitWindow
{

    public function getXml()
    {
        Manialink::load();
        $storage = Storage::getInstance();


        $frame = new Frame();
        $frame->setAlign("center", "center");
        $logourl = Config::getInstance()->logo;
        $size = 120;
        $logo = new Quad($size, $size / 4);
        $logo->setImage($logourl, true);
        $logo->setUrl("http://ml-expansion.com");
        $logo->setAlign("center", "top");
        $logo->setPosition(0, 50, -50);
        $frame->addComponent($logo);

        $background = new Quad(160, 50);
        $background->setStyle("Bgs1InRace");
        $background->setPosition(0, 0);
        $background->setAlign("center", "top");
        $background->setSubStyle(Bgs1InRace::BgCard);
        $frame->addComponent($background);

        $server = new Label(160, 7);
        $server->setAlign("center", "top");
        $server->setPosition(0, 20);
        $server->setScale(1.5);
        $server->setText($storage->server->name);
        $server->setStyle("TextRaceMessageBig");
        $frame->addComponent($server);

        $frame->save();
        return Manialink::render(true);
    }
}
