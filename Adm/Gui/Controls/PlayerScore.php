<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

class PlayerScore extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $bg;
    private $label;
    private $inputbox;
    private $frame;

    /**
     *
     * @param int $indexNumber
     * @param \Maniaplanet\DedicatedServer\Structures\PlayerRanking $player
     * @param int $score
     * @param type $controller
     * @param int $sizeX
     */
    function __construct($indexNumber, $player, $sizeX)
    {
        $sizeY = 6;
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(4, 0);
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($player->nickName);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox($player->playerId, 20);
        $this->inputbox->setText($player->score);
        $this->frame->addComponent($this->inputbox);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

// manialive 3.1 override to do nothing.
    function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    function erase()
    {
        $this->inputbox->destroy();
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

}

?>

