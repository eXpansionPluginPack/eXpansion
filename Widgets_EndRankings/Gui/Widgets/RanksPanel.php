<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Widgets;

class RanksPanel extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    private $frame;
    private $items = array();
    private $bg;
    private $quad;
    private $lbl;

    protected function onConstruct() {
	
	$sizeX = 38;
	$sizeY = 95;

	$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(38, 95);
	$this->addComponent($this->bg);

	$this->bgTitle = new \ManiaLib\Gui\Elements\Quad();
	$this->bgTitle->setStyle("UiSMSpectatorScoreBig");
	$this->bgTitle->setSubStyle("PlayerSlotCenter");
	$this->bgTitle->setColorize("3af");
	$this->addComponent($this->bgTitle);

	$this->lbl_title = new \ManiaLib\Gui\Elements\Label(30, 5);
	$this->lbl_title->setTextSize(1);
	$this->lbl_title->setTextColor("fff");
	$this->lbl_title->setStyle("TextCardScores2");
	$this->lbl_title->setAlign("center", "center");
	$this->lbl_title->setText("Server Ranks");
	$this->addComponent($this->lbl_title);

	$this->frame = new \ManiaLive\Gui\Controls\Frame(4, -5);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
	$this->addComponent($this->frame);

	$this->setName("Server Ranks");
	parent::onConstruct();
    }

    function onResize($oldX, $oldY) {

	$this->bg->setSize($this->sizeX, $this->sizeY );
	$this->bg->setPosition(0, -($this->bg->getSizeY() / 2));

	$this->bgTitle->setSize($this->sizeX+2, 4.2);
	$this->bgTitle->setPosition(0, 0.75);
	
	$this->lbl_title->setPosition(($this->sizeX / 2), -1);
	parent::onResize($oldX, $oldY);
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
