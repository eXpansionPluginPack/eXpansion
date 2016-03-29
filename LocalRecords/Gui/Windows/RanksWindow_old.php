<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;

use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\RankItem;

use ManiaLive\Gui\ActionHandler;

class RanksWindow_old extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    private $connection;
    private $storage;
    private $items = array();
    public static $ranks;
    public static $nbrec;
    public static $top3;
            
    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -5);
    }

    function onShow() {
        $this->populateList();
    }

    function populateList() {

        foreach ($this->items as $item)
            $item->destroy();
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $login = $this->getRecipient();        
       
        arsort(self::$ranks);
        
        foreach (self::$ranks as $login => $rank) {
            $this->items[$x] = new RankItem($x++, \ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords::$players[$login]->nickname, self::$nbrec[$login]);
            $this->pager->addItem($this->items[$x]);
        }
    
    }

    function destroy() {
        $this->connection = null;
        $this->storage = null;
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = null;
        $this->pager->destroy();

        parent::destroy();
    }

}

?>
