<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls;

use ManiaLivePlugins\eXpansion\Widgets_Record\Config;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;
use ManiaLivePlugins\eXpansion\Helpers\Countries;

class Recorditem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record $record, $login) {
        $sizeX = 36;
        $sizeY = 3;


        /* if ($record->login == $login) {
          $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX + 6, $sizeY);
          $this->bg->setPosX(-2);
          $this->bg->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
          $this->bg->setAlign('left', 'center');
          $this->bg->setBgcolor('0c03');
          $this->addComponent($this->bg);
          } else {
          $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX + 4, $sizeY);
          $this->bg->setPosX(-2);
          $this->bg->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
          $this->bg->setAlign('left', 'center');
          //$this->bg->setBgcolor('0001');
          $this->addComponent($this->bg);
          } */

        //  $this->frame = new \ManiaLive\Gui\Controls\Frame();
        //   $this->frame->setSize($sizeX, $sizeY);
        //   $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->label = new \ManiaLib\Gui\Elements\Label(4, 1);
        $this->label->setAlign('center', 'center');
        $this->label->setPosition(0,0.5);
        $this->label->setScale(0.7);
        $bold = "";
        if ($index <= 3)
            $bold = '$o';
        $this->label->setText('$fff' . $bold . $index);
        $this->addComponent($this->label);

        $flag = new \ManiaLib\Gui\Elements\Quad(3, 3);
        $flag->setPosition(4,0);
        $flag->setAlign("left", "center");
        $flag->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $config = Config::getInstance();
        $path = $record->nation;

        if ($path !== null) {
            $path = explode("|", $path);
            if (sizeof($path) >= 2) {

                $image = "file://Skins/Avatars/Flags/" . Countries::mapCountry($path[2]) . ".dds";
                $flag->setStyle("");
                $flag->setImage($image, false);
            }
        }
        $this->addComponent($flag);
        /*
          $spacer = new \ManiaLib\Gui\Elements\Quad();
          $spacer->setSize(1, 4);
          $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
          $this->frame->addComponent($spacer);
         */
        $this->label = new \ManiaLib\Gui\Elements\Label(14, 4);
        $this->label->setPosition(9);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);
        $this->label->setText('$fff' . \ManiaLive\Utilities\Time::fromTM($record->time));
        if ($record->login == $login) {
            $this->label->setStyle("TextTitle2Blink");
            $this->label->setTextSize(1);
        }
        $this->addComponent($this->label);

        $this->nick = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->nick->setPosition(20,0);
        $this->nick->setAlign('left', 'center');
        $this->nick->setScale(0.7);

        if ($record->login == $login) {
            $this->nick->setStyle("TextTitle2Blink");
            $this->nick->setTextSize(1);
        }

        $nickname = \ManiaLib\Utils\Formatting::stripCodes($record->nickName, "wosnm");
        $this->nick->setText('$fff' . $nickname);
        $this->addComponent($this->nick);

        // $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
       // $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function onDraw() {
        
    }

    public function destroy() {
        // $this->frame->clearComponents();
        // $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

