<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls;

use ManiaLivePlugins\eXpansion\Widgets_Record\Config;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

class Recorditem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record $record) {
        $sizeX = 30;
        $sizeY = 3;
        $config = Config::getInstance();
        
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());



        $this->label = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);
        $bold = "";
        if ($index <= 3)
            $bold = '$o';
        $this->label->setText('$fff' . $bold . $index);
        $this->frame->addComponent($this->label);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(1, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);
       
        $flag = new \ManiaLib\Gui\Elements\Quad(3,3);
        $flag->setAlign("left", "center");
        $flag->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        
        $path = $record->nation;
        echo "path : ".$path."\n";
        if ($path !== null) {
            $path = explode("|", $path);
            $image = $config->flags.rawurlencode($path[2]).".dds";
            $flag->setStyle("");
            $flag->setImage($image);                        
        }
        $this->frame->addComponent($flag);
        
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
        $nickname = $record->nickName;
        $nickname = \ManiaLib\Utils\Formatting::stripCodes($nickname, "wos");
        $nickname = \ManiaLib\Utils\Formatting::contrastColors($nickname, "777");
        $this->nick->setText('$fff' . $nickname);
        $this->frame->addComponent($this->nick);

        $this->label = new \ManiaLib\Gui\Elements\Label(15, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);

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

    public function destroy() {   
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }   
}
?>

