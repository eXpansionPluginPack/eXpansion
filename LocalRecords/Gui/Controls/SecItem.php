<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\Sector;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalBase;

/**
 * Description of RecItem
 *
 * @author oliverde8
 */
class SecItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $label_rank, $label_nick, $label_time, $frames;
    private $bg;
    private $widths;

    function __construct($indexNumber, $login, $rank, $widths, LocalBase $localBase)
    {
        $this->widths = $widths;
        $this->sizeY  = 8;
        $this->bg     = new ListBackGround($indexNumber, 100, 8);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize(100, 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label_rank = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_rank->setAlign('left', 'center');
        $this->label_rank->setScale(0.8);
        $this->label_rank->setText(($indexNumber + 1) . ".");
        $this->frame->addComponent($this->label_rank);

        $i = 0;
        foreach ($rank as $sector) {
            $frame = new \ManiaLive\Gui\Controls\Frame();
            $frame->setScale(0.8);
            $frame->setSize(10, 8);
            $frame->setPosY(3);

            $this->label_nick[$i] = new \ManiaLib\Gui\Elements\Label(10., 4);
            $this->label_nick[$i]->setAlign('center', 'top');
            //$this->label_nick[$i]->setScale(0.8);
            $this->label_nick[$i]->setText($sector['recordObj']->nickName);

            $this->label_time[$i] = new \ManiaLib\Gui\Elements\Label(10, 4);
            $this->label_time[$i]->setAlign('center', 'top');
            //$this->label_time[$i]->setScale(0.8);
            $this->label_time[$i]->setPosY(-4);

            if ($i < Sector::$nbResult) {
                $time = $localBase->formatScore($sector['sectorTime']);
                $this->label_time[$i]->setText($time);

                $frame->addComponent($this->label_nick[$i]);
                $frame->addComponent($this->label_time[$i]);
                $this->frames[$i] = $frame;
                $this->frame->addComponent($frame);
            }
            $i++;
        }

    }

    public function onResize($oldX, $oldY)
    {
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / .8) - 5);
        $this->bg->setSizeX($this->getSizeX() - 5);
        $this->label_rank->setSizeX($scaledSizes[0]);

        for ($i = 0; $i < Sector::$nbResult; $i++) {
            if (isset($this->label_nick[$i]) && isset($this->label_time[$i])) {
                $this->label_nick[$i]->setSizeX($scaledSizes[1] - 2);
                $this->label_time[$i]->setSizeX($scaledSizes[1] - 2);

                $this->label_nick[$i]->setPosX(($scaledSizes[1] - 2) / 2 + 1);
                $this->label_time[$i]->setPosX(($scaledSizes[1] - 2) / 2 + 1);

                $this->frames[$i]->setSizeX($scaledSizes[1]);
            }
        }

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
        unset($this->label_nick);
        parent::destroy();
    }

}

?>
