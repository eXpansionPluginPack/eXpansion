<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows;

use ManiaLivePlugins\eXpansion\Dedimania\Gui\Controls\RecItem;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

class Records extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $frame;
    private $label_rank, $label_nick, $label_score;
    private $widths = array(1, 2, 8);
    private $pager;
    private $items = array();

    protected function onConstruct() {
	parent::onConstruct();
	$sizeX = 100;
	$scaledSizes = Gui::getScaledSize($this->widths, $sizeX / .8);

	$this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
	$this->pager->setPosX(0);
	$this->pager->setPosY(-4);
	$this->mainFrame->addComponent($this->pager);

	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setSize($sizeX, 4);
	$this->frame->setPosY(0);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->mainFrame->addComponent($this->frame);

	$this->label_rank = new \ManiaLib\Gui\Elements\Label($scaledSizes[0], 4);
	$this->label_rank->setAlign('left', 'center');
	$this->label_rank->setScale(0.8);
	$this->frame->addComponent($this->label_rank);

	$this->label_score = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 4);
	$this->label_score->setAlign('left', 'center');
	$this->label_score->setScale(0.8);
	$this->frame->addComponent($this->label_score);

	$this->label_nick = new \ManiaLib\Gui\Elements\Label($scaledSizes[2], 4);
	$this->label_nick->setAlign('left', 'center');
	$this->label_nick->setScale(0.8);
	$this->frame->addComponent($this->label_nick);
    }

    public function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / 0.8) - 5);

	$this->label_rank->setSizeX($scaledSizes[0]);
	$this->label_score->setSizeX($scaledSizes[1]);
	$this->label_nick->setSizeX($scaledSizes[2]);

	$this->pager->setSize($this->getSizeX() - 4, $this->getSizeY() - 12);
	foreach ($this->items as $item)
	    $item->setSizeX($this->getSizeX());
    }

    public function onShow() {
	$this->label_rank->setText(__(LocalRecords::$txt_rank, $this->getRecipient()));
	$this->label_nick->setText(__(LocalRecords::$txt_nick, $this->getRecipient()));
	$this->label_score->setText(__(LocalRecords::$txt_score, $this->getRecipient()));
    }

    public function destroy() {
	foreach ($this->items as $item) {
	    $item->erase();
	}

	$this->items = null;
	$this->pager->destroy();
	$this->clearComponents();
	parent::destroy();
    }

    public function populateList($recs) {
	$x = 0;
	$login = $this->getRecipient();
	foreach ($recs as $rec) {
	    $this->items[$x] = new RecItem($x, $login, $rec, $this->widths);
	    $this->pager->addItem($this->items[$x]);
	    $x++;
	}
    }

}

?>
