<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

class Recorditem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record $record, $diffTime) {
        $sizeX = 30;
        $sizeY = 3;

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());



        $this->label = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);
        $bold = "";
        if ($index <= 3) $bold = '$o';
        $this->label->setText('$fff' . $bold. $index);
        $this->frame->addComponent($this->label);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(1, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(14, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);
        $this->label->setText('$fff' . \ManiaLive\Utilities\Time::fromTM($record->time));        
        $this->frame->addComponent($this->label);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(1, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->nick = new \ManiaLib\Gui\Elements\Label(34, 4);
        $this->nick->setAlign('left', 'center');
        $this->nick->setScale(0.7);
        $nickname = LocalRecords::$players[$record->login]->nickname;
        $nickname = \ManiaLib\Utils\Formatting::stripCodes($nickname, "wos");
        $nickname = \ManiaLib\Utils\Formatting::contrastColors($nickname, "777");
        $this->nick->setText('$fff' . $nickname );
        $this->frame->addComponent($this->nick);

        $this->label = new \ManiaLib\Gui\Elements\Label(15, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);
        if ($diffTime == 0)  {
          $this->label->setText('$0f0+' . \ManiaLive\Utilities\Time::fromTM($diffTime));
        }
        else {
           $this->label->setText('$ff0' . \ManiaLive\Utilities\Time::fromTM($diffTime, true));
        
        }
       // $this->frame->addComponent($this->label);
        

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function onDraw() {
        
    }

    function __destruct() {
        
    }

}
?>

