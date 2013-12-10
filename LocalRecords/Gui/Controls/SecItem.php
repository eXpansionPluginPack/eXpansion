<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls;

use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\Sector;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of RecItem
 *
 * @author oliverde8
 */
class SecItem extends \ManiaLive\Gui\Control {

    private $label_rank, $label_nick;
    private $bg;
    private $widths;

    function __construct($indexNumber, $login, $rank, $widths) {
        $this->widths = $widths;
        $this->sizeY = 4;
        $this->bg = new ListBackGround($indexNumber, 100, 4);
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
        foreach($rank as $sector){
            $this->label_nick[$i] = new \ManiaLib\Gui\Elements\Label(10., 4);
            $this->label_nick[$i]->setAlign('left', 'center');
            $this->label_nick[$i]->setScale(0.8);
            if($i <  Sector::$nbResult){
                $time = \ManiaLive\Utilities\Time::fromTM($sector['sectorTime']);
                $this->label_nick[$i]->setText('('.$time.')'. $sector['recordObj']->nickName.' ');
            }
            $this->frame->addComponent($this->label_nick[$i]);
            $i++;
            
        }
       
    }

    public function onResize($oldX, $oldY) {
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / .8) - 5);
        $this->bg->setSizeX($this->getSizeX() - 5);
        $this->label_rank->setSizeX($scaledSizes[0]);
        
        for($i = 0; $i<  Sector::$nbResult; $i++){
            if(isset($this->label_nick[$i]))
                $this->label_nick[$i]->setSizeX($scaledSizes[1]-2);
        }
        
    }

    // manialive 3.1 override to do nothing.
    function destroy() {
        
    }

    /*
     * custom function to remove contents.
     */

    function erase() {
        unset($this->label_nick);
        parent::destroy();
    }

}

?>
