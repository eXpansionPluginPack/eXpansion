<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Widgets;

class RanksPanel extends \ManiaLive\Gui\Window {

    private $frame;
    private $items = array();

    protected function onConstruct() {
        parent::onConstruct();
        $label = new \ManiaLib\Gui\Elements\Label(30, 6);
        $label->setText("Top 30 Ranks");
        $label->setTextSize(2);
        $label->setTextColor("fff");
        $this->addComponent($label);
        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, -6);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
        $this->addComponent($this->frame);
    }

    function onDraw() {
        
    }

    function setData($ranks) {
        foreach ($this->items as $item) {
            $itme->destroy();
        }
        $this->items = array();
        $this->frame->clearComponents();

        $x = 0;
        foreach ($ranks as $rank) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Controls\RankItem($x, $rank);
            $this->frame->addComponent($this->items[$x]);
            $x++;
            if ($x == 24)
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
