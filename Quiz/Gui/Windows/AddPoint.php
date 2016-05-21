<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Quiz\Gui\Controls\AddPointItem;

class AddPoint extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;

    protected $connection;

    protected $storage;

    protected $items = array();

    public static $mainPlugin;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->addComponent($this->pager);
    }


    public function onResize($oldX, $oldY)
    {
        $this->pager->setSize($this->sizeX, $this->sizeY - 2);
        $this->pager->setPosition(2);
        parent::onResize($oldX, $oldY);
    }

    public function onShow()
    {
        $this->setData(self::$mainPlugin->getPlayers());
    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\Quiz\Structures\QuizPlayer[] $quizPlayers
     */
    public function setData($quizPlayers)
    {

        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $login = $this->getRecipient();
        $isadmin = AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN);
        try {
            foreach (\ManiaLive\Data\Storage::getInstance()->players as $login => $player) {
                $this->items[$x] = new AddPointItem($x++, $player, $this, $isadmin, $this->getRecipient(), $this->sizeX);
                if (array_key_exists($login, $quizPlayers)) {
                    $this->items[$x]->setPoints($quizPlayers[$login]->points);
                }
                $this->pager->addItem($this->items[$x]);
            }
        } catch (\Exception $e) {
            Helper::log("[Quiz/AddPoint]On setData Error : " . $e->getMessage());
        }
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
