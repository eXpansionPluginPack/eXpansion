<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class RankItem_old extends \ManiaLive\Gui\Control {

    private $bg;
    private $nbrec;
    private $nickname;
    private $frame;

    function __construct($indexNumber, $nickname, $nbrec) {
        $sizeX = 120;
        $sizeY = 6;


        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        if ($indexNumber % 2 == 0) {
            $this->bg->setBgcolor('fff4');
        } else {
            $this->bg->setBgcolor('7774');
        }
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $lbl = new \ManiaLib\Gui\Elements\Label(6);
        $lbl->setText('$000' . $indexNumber);
        $lbl->setAlign('left', 'center');
        $this->frame->addComponent($lbl);

        $this->nickname = new \ManiaLib\Gui\Elements\Label(80, 4);
        $this->nickname->setAlign('left', 'center');
        $this->nickname->setScale(0.8);
        $this->nickname->setText($nickname);
        $this->frame->addComponent($this->nickname);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $lbl = new \ManiaLib\Gui\Elements\Label(6);
        $lbl->setText('$000' . $nbrec['count']);
        $lbl->setAlign('left', 'center');
        $this->frame->addComponent($lbl);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $lbl = new \ManiaLib\Gui\Elements\Label(6);
        $lbl->setText('$000' . $nbrec['1']);
        $lbl->setAlign('left', 'center');
        $this->frame->addComponent($lbl);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $lbl = new \ManiaLib\Gui\Elements\Label(6);
        $lbl->setText('$000' . $nbrec['2']);
        $lbl->setAlign('left', 'center');
        $this->frame->addComponent($lbl);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $lbl = new \ManiaLib\Gui\Elements\Label(6);
        $lbl->setText('$000' . $nbrec['3']);
        $lbl->setAlign('left', 'center');
        $this->frame->addComponent($lbl);
        
        $this->addComponent($this->frame);
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        
    }

    function onDraw() {
        
    }

    function destroy() {
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();

        parent::destroy();
    }

}
?>

