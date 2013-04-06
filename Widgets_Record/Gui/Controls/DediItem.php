<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls;

use ManiaLivePlugins\eXpansion\Widgets_Record\Config;

class DediItem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, $record, $login) {
        $sizeX = 36;
        $sizeY = 3;
        $config = Config::getInstance();
        if (!array_key_exists("Login", $record))
            return;

         if ($record['Login'] == $login) {
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
            //$this->bg->setBgcolor('0000');
            $this->addComponent($this->bg);
        }

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
        $nickname = \ManiaLib\Utils\Formatting::stripCodes($nickname, "wosnm");
        $this->nick->setText('$fff' . $nickname);
        $this->frame->addComponent($this->nick);
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
        try {
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        }
        catch (\Exception $e){

        }
        parent::destroy();
    }

}
?>

