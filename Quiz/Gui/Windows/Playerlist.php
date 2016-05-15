<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Quiz\Gui\Controls\Playeritem;

class Playerlist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;

    protected $connection;

    protected $storage;

    protected $items = array();

    public static $mainPlugin;

    protected function onConstruct()
    {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->addComponent($this->pager);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setPosition(1);
        $this->pager->setSize($this->sizeX, $this->sizeY - 4);
    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\Quiz\Structures\QuizPlayer[] $players
     */

    public function setData($players)
    {

        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $login = $this->getRecipient();
        $isadmin = AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN);
        try {
            foreach ($players as $player) {
                $this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $login, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
            }
        } catch (\Exception $e) {
            Helper::log("[Quiz/PlayerList]Set data error : " . $e->getMessage());
        }
    }

    protected function onShow()
    {
        $this->setData(self::$mainPlugin->getPlayers());
        parent::onShow();
    }

    public function addPoint($login, $target)
    {
        self::$mainPlugin->addPoint($login, $target);
        $this->setData(self::$mainPlugin->getPlayers());
        $this->RedrawAll();
    }

    public function removePoint($login, $target)
    {
        self::$mainPlugin->removePoint($login, $target);
        $this->setData(self::$mainPlugin->getPlayers());
        $this->RedrawAll();
    }

    public function destroy()
    {
        $this->connection = null;
        $this->storage = null;
        foreach ($this->items as $item)
            $item->erase();

        $this->items = null;
        $this->pager->destroy();
        parent::destroy();
    }

}
