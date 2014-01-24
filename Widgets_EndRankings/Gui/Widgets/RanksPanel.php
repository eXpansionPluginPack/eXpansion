<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Widgets;

class RanksPanel extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    private $frame;
    private $items = array();
    private $bg;
    private $quad;
    private $lbl;

    protected function onConstruct() {
        parent::onConstruct();
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(20,20);       
        $this->bg->setAlign("left", "top");
        $this->addComponent($this->bg);

        $this->lbl = new \ManiaLib\Gui\Elements\Label(30, 6);
        $this->lbl->setTextSize(1);
        $this->lbl->setStyle("TextStaticVerySmall");
        $this->lbl->setText(__('Server ranks'));        
        $this->lbl->setAlign("center", "center");
        $this->lbl->setTextColor('fff');
        $this->lbl->setTextEmboss();
        
        $this->addComponent($this->lbl);

        
        /* $this->quad = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(30, 4);       
        $this->quad->setAlign("left", "center");
        $this->addComponent($this->quad);  */

        $this->frame = new \ManiaLive\Gui\Controls\Frame(3, -4);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
        $this->addComponent($this->frame);
        
        $this->setName("Server Ranks");
    }
    

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->bg->setSize($this->sizeX-1, $this->sizeY+3);        
        $this->bg->setPosition(0,-44);
        $this->lbl->setPosX($this->sizeX / 2);
        $this->lbl->setPosY(3);
        /* $this->quad->setSizeX($this->sizeX);
        $this->quad->setPosX($this->sizeX / 2); 
        $this->quad->setPosY(1); */
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
