<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\RecItem;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalBase;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

/**
 * Description of Records
 *
 * @author oliverde8
 */
class Records extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $frame;
    protected $label_rank, $label_nick, $label_score, $label_avgScore, $label_nbFinish;
    protected $widths = array(1, 5, 3, 3, 2, 4);

    /**
     * @var \ManiaLivePlugins\eXpansion\Gui\Elements\OptimizedPager
     */
    protected $pager;
    protected $items = array();
    protected $button_sectors, $button_cps;

    protected $recList;
    protected $limit;
    protected $currentMap;

    /** @var  LocalBase */
    protected $localBase;

    protected function onConstruct()
    {
        parent::onConstruct();
        $sizeX = 100;
        $scaledSizes = Gui::getScaledSize($this->widths, $sizeX / .8);

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\OptimizedPager();
        $this->pager->setPosX(0);
        $this->pager->setPosY(0);
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

        $this->label_nick = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 4);
        $this->label_nick->setAlign('left', 'center');
        $this->label_nick->setScale(0.8);
        $this->frame->addComponent($this->label_nick);

        $this->label_score = new \ManiaLib\Gui\Elements\Label($scaledSizes[2], 4);
        $this->label_score->setAlign('left', 'center');
        $this->label_score->setScale(0.8);
        $this->frame->addComponent($this->label_score);

        $this->label_avgScore = new \ManiaLib\Gui\Elements\Label($scaledSizes[3], 4);
        $this->label_avgScore->setAlign('left', 'center');
        $this->label_avgScore->setScale(0.8);
        $this->frame->addComponent($this->label_avgScore);

        $this->label_nbFinish = new \ManiaLib\Gui\Elements\Label($scaledSizes[3], 4);
        $this->label_nbFinish->setAlign('left', 'center');
        $this->label_nbFinish->setScale(0.8);
        $this->frame->addComponent($this->label_nbFinish);

        $this->button_sectors = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(30, 5);
        $this->button_sectors->setText("Sector Times");
        $this->button_sectors->setAction(LocalRecords::$openSectorsAction);
        $this->mainFrame->addComponent($this->button_sectors);

        $this->button_cps = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(30, 5);
        $this->button_cps->setText("Cp Times");
        $this->button_cps->setAction(LocalRecords::$openCpsAction);
        $this->mainFrame->addComponent($this->button_cps);

    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / 0.8) - 5);

        $this->label_rank->setSizeX($scaledSizes[0]);
        $this->label_nick->setSizeX($scaledSizes[1]);
        $this->label_score->setSizeX($scaledSizes[2]);
        $this->label_avgScore->setSizeX($scaledSizes[3]);
        $this->label_nbFinish->setSizeX($scaledSizes[4]);
        $this->pager->setSize($this->getSizeX() - 1, $this->getSizeY() - 12);
        $this->pager->setPosY(-7);
        foreach ($this->items as $item) {
            $item->setSizeX($this->getSizeX());
        }

        $this->button_sectors->setPosition($this->getSizeX() - 27, -$this->getSizeY() + 6);
        $this->button_cps->setPosition($this->getSizeX() - 53, -$this->getSizeY() + 6);
    }

    public function onShow()
    {
        $this->label_rank->setText(__(LocalRecords::$txt_rank, $this->getRecipient()));
        $this->label_nick->setText(__(LocalRecords::$txt_nick, $this->getRecipient()));
        $this->label_score->setText(__(LocalRecords::$txt_score, $this->getRecipient()));
        $this->label_avgScore->setText(__(LocalRecords::$txt_avgScore, $this->getRecipient()));
        $this->label_nbFinish->setText(__(LocalRecords::$txt_nbFinish, $this->getRecipient()));
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        $this->pager->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

    public function populateList($recs, $limit, $currentMap, LocalBase $localBase)
    {
        $this->recList = $recs;
        $this->limit = $limit;
        $this->currentMap = $currentMap;
        $this->localBase = $localBase;

        $this->pager->clearItems();
        $this->button_sectors->setVisibility($currentMap);
        $login = $this->getRecipient();
        $x = 0;

        RecItem::$widths = $this->widths;


        while ($x < $limit && $x < sizeof($recs)) {
            /** @var Record $record */
            $record = $recs[$x];

            $rank = $x + 1;

            $this->pager->addSimpleItems(array($rank => -1,
                Gui::fixString($record->nickName) => -1,
                $localBase->formatScore($record->time) . " " => -1,
                $localBase->formatScore($record->avgScore) . "" => -1,
                "#" . $record->nbFinish => -1,
                ($record->isDelete ? '$w$F00Cancel Delete' : '$F00$wDelete') => $this->createAction(
                    array($this, 'toogleDelete'),
                    $record
                ),
            ));
            $x++;
        }
        $this->pager->setContentLayout('\ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\RecItem');
        $this->pager->update($this->getRecipient());
    }


    public function toogleDelete($login, $record)
    {
        $record->isDelete = !$record->isDelete;

        $this->populateList($this->recList, $this->limit, $this->currentMap, $this->localBase);
        $this->redraw($login);
    }
}
