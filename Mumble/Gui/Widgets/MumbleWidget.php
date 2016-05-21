<?php

namespace ManiaLivePlugins\eXpansion\Mumble\Gui\Widgets;

class MumbleWidget extends \ManiaLive\Gui\Window
{

    private $frame;
    private $items = array();
    private $icon_mumble = "http://tmrankings.com/manialink/mv/icons/mumble.png";

    protected function onConstruct()
    {
        parent::onConstruct();
        $quad = new \ManiaLib\Gui\Elements\Quad(7, 7);
        $quad->setImage($this->icon_mumble);
        $quad->setId('MumbleLogo');
        $quad->setAction($quad->getId());
        $this->addComponent($quad);
    }

    protected function onDraw()
    {
        parent::onDraw();
        //echo "draw: " . $this->getRecipient() . "\n";
    }

    public function destroy()
    {
        parent::destroy();
    }
}
