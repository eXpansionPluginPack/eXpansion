<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows;

use ManiaLivePlugins\eXpansion\Dedimania\Gui\Controls\RecItem;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

class Records extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

	private $frame;

	private $label_rank, $label_nick, $label_score;

	private $widths = array(1, 6, 6);

	private $pager;

	private $items = array();

	protected function onConstruct()
	{
		parent::onConstruct();
		$sizeX = 100;
		$scaledSizes = Gui::getScaledSize($this->widths, $sizeX);

		$this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
		$this->mainFrame->addComponent($this->pager);

		$this->frame = new \ManiaLive\Gui\Controls\Frame();
		$this->frame->setSize($sizeX, 6);
		$this->frame->setPosY(0);
		$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
		$this->mainFrame->addComponent($this->frame);

		$this->label_rank = new \ManiaLib\Gui\Elements\Label($scaledSizes[0], 6);
		$this->label_rank->setAlign('left', 'center');

		$this->frame->addComponent($this->label_rank);

		$this->label_nick = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 6);
		$this->label_nick->setAlign('left', 'center');

		$this->frame->addComponent($this->label_nick);
		$this->label_score = new \ManiaLib\Gui\Elements\Label($scaledSizes[2], 6);
		$this->label_score->setAlign('left', 'center');

		$this->frame->addComponent($this->label_score);

		$this->button_cps = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(30, 5);
		$this->button_cps->setText("Cp Times");
		$this->button_cps->setAction(\ManiaLivePlugins\eXpansion\Dedimania\DedimaniaAbstract::$actionOpenCps);
		$this->mainFrame->addComponent($this->button_cps);
	}

	public function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX()) - 5);

		$this->label_rank->setSizeX($scaledSizes[0]);
		$this->label_nick->setSizeX($scaledSizes[1]);
		$this->label_score->setSizeX($scaledSizes[2]);
		$this->button_cps->setPosition($this->getSizeX() - 30, -$this->getSizeY() + 6);
		
		$this->pager->setSize($this->getSizeX(), $this->getSizeY() - 12);
		foreach ($this->items as $item)
			$item->setSizeX($this->getSizeX());
	}

	public function onShow()
	{
		$this->label_rank->setText(__(LocalRecords::$txt_rank, $this->getRecipient()));
		$this->label_nick->setText(__(LocalRecords::$txt_nick, $this->getRecipient()));
		$this->label_score->setText(__(LocalRecords::$txt_score, $this->getRecipient()));
	}

	public function destroy()
	{
		foreach ($this->items as $item) {
			$item->erase();
		}

		$this->items = null;
		$this->pager->destroy();
		$this->clearComponents();
		parent::destroy();
	}

	public function populateList($recs)
	{
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
