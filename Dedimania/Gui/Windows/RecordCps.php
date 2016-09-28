<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows;

use ManiaLivePlugins\eXpansion\Dedimania\Gui\Controls\CpItem;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

class RecordCps extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{
    private $frame;
    private $label_rank, $label_nick, $label_score, $frameCP, $nextButton, $prevButton;
    private $widths = array(0.5, 3, 10);
    private $pager;
    private $items = array();
    private $offset = 0;
    private $itemsPerPage = 6;
    private $recs;

    protected function onConstruct()
    {
        parent::onConstruct();
        $sizeX = 170;
        $scaledSizes = Gui::getScaledSize($this->widths, $sizeX);

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
        $this->frame->addComponent($this->label_rank);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 4);
        $this->label_nick->setAlign('left', 'center');
        $this->frame->addComponent($this->label_nick);

        $this->frameCP = new \ManiaLive\Gui\Controls\Frame();
        $this->frameCP->setLayout(new \ManiaLib\Gui\Layouts\Line());

        for ($x = $this->offset; $x <= $this->offset + $this->itemsPerPage; $x++) {
            $label = new \ManiaLib\Gui\Elements\Label(15, 6);
            $label->setAlign("left", "center");
            $label->setText("Cp " . ($x + 1));
            $this->frameCP->addComponent($label);
        }

        $this->prevButton = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(6, 6);
        $this->prevButton->setIcon("Icons64x64_1", "ArrowPrev");
        $this->prevButton->setAction($this->createAction(array($this, "prevPage")));
        $this->frameCP->addComponent($this->prevButton);

        $this->nextButton = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(6, 6);
        $this->nextButton->setIcon("Icons64x64_1", "ArrowNext");
        $this->nextButton->setAction($this->createAction(array($this, "nextPage")));
        $this->frameCP->addComponent($this->nextButton);

        $this->frame->addComponent($this->frameCP);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / 0.8) - 5);

        $this->label_rank->setSizeX($scaledSizes[0]);
        $this->label_nick->setSizeX($scaledSizes[1]);

        $this->pager->setSize($this->getSizeX() - 4, $this->getSizeY() - 12);
        foreach ($this->items as $item) {
            $item->setSizeX($this->getSizeX());
        }
    }

    public function onShow()
    {
        $this->label_rank->setText(__(LocalRecords::$txt_rank, $this->getRecipient()));
        $this->label_nick->setText(__(LocalRecords::$txt_nick, $this->getRecipient()));
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->items = null;
        $this->recs = null;

        $this->pager->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

    public function populateList($recs)
    {
        $x = 0;
        $this->recs = $recs;

        $login = $this->getRecipient();
        foreach ($recs as $rec) {
            $this->items[$x] = new CpItem($x, $login, $rec, $this->widths, 0);
            $x++;
        }
        $this->updatePage(0);
    }

    public function nextPage($offset)
    {
        $this->offset += $this->itemsPerPage;
        if ($this->offset > count($this->recs)) {
            $this->offset = count($this->recs) - $this->itemsPerPage;
        }
        $this->updatePage($this->offset);
        $this->redraw($this->getRecipient());
    }

    public function prevPage($offset)
    {
        $this->offset -= $this->itemsPerPage;

        if ($this->offset < 0) {
            $this->offset = 0;
        }
        $this->updatePage($this->offset);
        $this->redraw($this->getRecipient());
    }

    public function updatePage($offset)
    {
        $this->pager->clearItems();

        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->items = array();

        $this->frameCP->destroyComponents();

        for ($x = $this->offset; $x <= $this->offset + $this->itemsPerPage; $x++) {
            $label = new \ManiaLib\Gui\Elements\Label(15, 6);
            $label->setAlign("left", "center");
            $label->setText("Cp " . ($x + 1));
            $this->frameCP->addComponent($label);
        }

        $this->prevButton = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(6, 6);
        $this->prevButton->setIcon("Icons64x64_1", "ArrowPrev");
        $this->prevButton->setAction($this->createAction(array($this, "prevPage")));
        $this->frameCP->addComponent($this->prevButton);

        $this->nextButton = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(6, 6);
        $this->nextButton->setIcon("Icons64x64_1", "ArrowNext");
        $this->nextButton->setAction($this->createAction(array($this, "nextPage")));
        $this->frameCP->addComponent($this->nextButton);

        $x = 0;
        foreach ($this->recs as $rec) {
            $this->items[$x] = new CpItem($x, $this->getRecipient(), $rec, $this->widths, $offset);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }
}
