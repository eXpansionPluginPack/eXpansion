<?php

namespace ManiaLivePlugins\eXpansion\RecordWidgets\Gui\Controls;

use ManiaLivePlugins\eXpansion\RecordWidgets\Config;

class Dediitem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, $record) {
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
               
        $this->label = new \ManiaLib\Gui\Elements\Label(14, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);
        $this->label->setText('$fff' . \ManiaLive\Utilities\Time::fromTM($record['Best']));
        $this->frame->addComponent($this->label);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(1, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->nick = new \ManiaLib\Gui\Elements\Label(34, 4);
        $this->nick->setAlign('left', 'center');
        $this->nick->setScale(0.7);
        $nickname = $record['NickName'];
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
        $this->clearComponents();
        parent::destroy();
    }   
}
?>

