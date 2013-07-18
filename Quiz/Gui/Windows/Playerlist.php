<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Quiz\Gui\Controls\Playeritem;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Playerlist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    private $connection;
    private $storage;
    private $items = array();
    public static $mainPlugin;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX /2, $this->sizeY - 8);                
        $this->pager->setPosition(4, -4);
    }

    function onShow() {
        
    }

    /**
     * 
     * @param \ManiaLivePlugins\eXpansion\Quiz\Structures\QuizPlayer[] $players
     */
    function setData($players) {

        foreach ($this->items as $item)
            $item->destroy();
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $login = $this->getRecipient();
        $isadmin = AdminGroups::hasPermission($login,"quiz_admin");
        try {
            foreach ($players as $player) {
                $this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $login, $this->sizeX);
                $this->pager->addItem($this->items[$x]);                
            }
        } catch (\Exception $e) {
            echo $e->getFile() . ":" . $e->getLine();
        }
    }

    function addPoint($login, $target) {
        self::$mainPlugin->addPoint($login, $target);
        $this->setData(self::$mainPlugin->getPlayers());
        $this->RedrawAll();
    }

    function removePoint($login, $target) {
        self::$mainPlugin->removePoint($login, $target);
        $this->setData(self::$mainPlugin->getPlayers());
        $this->RedrawAll();
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
