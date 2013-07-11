<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Widgets;

class RanksPanel extends \ManiaLive\Gui\Window {

    private $frame;
    private $items = array();
    private $bg;
    private $quad;
    private $lbl;

    protected function onConstruct() {
        parent::onConstruct();
        $this->bg = new \ManiaLib\Gui\Elements\Quad();
        $this->bg->setStyle("Bgs1InRace");
        $this->bg->setSubStyle("BgList");
        $this->bg->setId("MainWindow");
        $this->bg->setScriptEvents(true);
        $this->addComponent($this->bg);

        $this->lbl = new \ManiaLib\Gui\Elements\Label(30, 6);
        $this->lbl->setTextSize(1);
        $this->lbl->setStyle("TextStaticVerySmall");
        $this->lbl->setText('Rankings');        
        $this->lbl->setAlign("center", "center");
        $this->addComponent($this->lbl);

        $this->quad = new \ManiaLib\Gui\Elements\Quad(30, 8);
        $this->quad->setStyle("Bgs1InRace");
        $this->quad->setSubStyle("BgTitle3_3");
        $this->quad->setAlign("left", "center");
        $this->addComponent($this->quad);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(3, -4);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
        $this->addComponent($this->frame);
    }

    function onDraw() {
        
    }

    function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX + 16, $this->sizeY);
        $this->bg->setPosX(-16);
        $this->lbl->setPosX($this->sizeX / 2);
        $this->lbl->setPosY(1);
        $this->quad->setSizeX($this->sizeX + 22);
        $this->quad->setPosX(-16);
        $this->quad->setPosY(1);


        parent::onResize($oldX, $oldY);
    }

    function setData($ranks) {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = array();
        $this->frame->clearComponents();

        $x = 0;
        foreach ($ranks as $rank) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Controls\RankItem($x, $rank);
            $this->frame->addComponent($this->items[$x]);
            $x++;
            if ($x == 30)
                break;
        }
    }

    function destroy() {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = array();
        $this->clearComponents();

        parent::destroy();
    }

}

?>
