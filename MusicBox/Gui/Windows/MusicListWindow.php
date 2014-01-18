<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows;

use \ManiaLivePlugins\eXpansion\MusicBox\Gui\Controls\Song;

class MusicListWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    public static $musicPlugin = null;
    private $items = array();

    /** @var \ManiaLive\Gui\Controls\Pager */
    private $pager;

    public function onConstruct() {
        parent::onConstruct();
        $this->setTitle("Music available at server");
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    function queueSong($login, $indexNumber) {
        self::$musicPlugin->mbox($login, $indexNumber);
        $this->Erase($this->getRecipient());
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, 0);
    }

    public function onShow() {
        parent::onShow();
        $login = $this->getRecipient();
        foreach ($this->items as $item) {
           $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();


        $x = 0;
        $songs = self::$musicPlugin->getSongs();
        foreach ($songs as $song) {
            $this->items[$x] = new Song($x, $login, $song, $this, $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    function destroy() {
        foreach ($this->items as $item) {
           $item->erase();
        }
        $this->items = null;

        $this->pager->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
