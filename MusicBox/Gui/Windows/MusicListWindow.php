<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\MusicBox\Gui\Controls\Song;
use ManiaLivePlugins\eXpansion\MusicBox\MusicBox;

class MusicListWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    /**
     * @var MusicBox
     */
    public static $musicPlugin = null;
    private $items = array();

    /** @var \ManiaLive\Gui\Controls\Pager */
    private $pager;

    public function onConstruct()
    {
        parent::onConstruct();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\OptimizedPager();
        $this->mainFrame->addComponent($this->pager);
    }

    public function queueSong($login, $indexNumber)
    {
        self::$musicPlugin->mbox($login, $indexNumber);
        $this->Erase($this->getRecipient());
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setPosition(4, 0);
    }

    public function onShow()
    {
        parent::onShow();
        $login = $this->getRecipient();
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();


        $x = 0;
        foreach (self::$musicPlugin->getSongs() as $song) {
            $action = $this->createAction(array($this, "queueSong"), ($x+1));
            $this->pager->addSimpleItems(array(Gui::fixString($song->title) => -1,
                Gui::fixString($song->artist) => -1,
                Gui::fixString($song->genre) => -1,
                "queue" => $action
            ));
            $x++;
        }

        $this->pager->setContentLayout('\ManiaLivePlugins\eXpansion\MusicBox\Gui\Controls\Song');
        $this->pager->update($this->getRecipient());

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

}
