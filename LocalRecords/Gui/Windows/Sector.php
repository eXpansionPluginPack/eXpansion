<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\SecItem;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalBase;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

/**
 * Description of Cps
 *
 * @author De Cramer Oliver
 */
class Sector extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    public static $nbResult = 5;
    protected $label_sector, $label_pos;
    protected $widths = array(3, 7, 7, 7, 7, 7);
    protected $pager;
    protected $items = array();

    protected function onConstruct()
    {
        parent::onConstruct();
        $sizeX = 100;
        $scaledSizes = Gui::getScaledSize($this->widths, $sizeX / .8);

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->pager->setPosX(0);
        $this->pager->setPosY(2);
        $this->mainFrame->addComponent($this->pager);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->mainFrame->addComponent($this->frame);

        $this->label_sector = new \ManiaLib\Gui\Elements\Label($scaledSizes[0], 4);
        $this->label_sector->setAlign('left', 'center');
        $this->label_sector->setScale(0.8);
        $this->frame->addComponent($this->label_sector);

        for ($i = 0; $i < self::$nbResult; $i++) {
            $this->label_pos[$i] = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 4);
            $this->label_pos[$i]->setAlign('center', 'center');
            $this->label_pos[$i]->setScale(0.8);
            $this->frame->addComponent($this->label_pos[$i]);
        }
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / 0.8) - 5);

        $this->label_sector->setSizeX($scaledSizes[0]);

        for ($i = 0; $i < self::$nbResult; $i++) {
            $this->label_pos[$i]->setSizeX($scaledSizes[1]);
            $this->label_pos[$i]->setPosX(($scaledSizes[1] / 2) * .8);
        }

        $this->pager->setSize($this->getSizeX() - 4, $this->getSizeY() - 7);
        foreach ($this->items as $item)
            $item->setSizeX($this->getSizeX());
    }

    public function onShow()
    {
        $this->label_sector->setText(__(LocalRecords::$txt_sector, $this->getRecipient()));
        for ($i = 0; $i < self::$nbResult; $i++) {
            $this->label_pos[$i]->setText(__(LocalRecords::$txt_ptime) . "#" . ($i + 1));
        }
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        unset($this->label_pos);
        $this->items = null;
        $this->pager->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

    public function populateList($recs, $limit, LocalBase $localBase)
    {
        $x = 0;
        $login = $this->getRecipient();

        foreach ($recs as $rec) {
            $this->items[$x] = new SecItem($x, $login, $rec, $this->widths, $localBase);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }
}
