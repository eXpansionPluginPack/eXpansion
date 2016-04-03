<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Wishitem;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;

class Jukelist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $items = array();
    public static $mainPlugin;

    /** @var \ManiaLive\Gui\Controls\Pager */
    private $pager;
    private $btnRemoveAll;
    private $actionRemoveAll;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->actionRemoveAll = $this->createAction(array(self::$mainPlugin, "emptyWishesGui"));
        $this->btnRemoveAll = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btnRemoveAll->setAction($this->actionRemoveAll);
        $this->btnRemoveAll->setText("Clear Jukebox");
        $this->btnRemoveAll->colorize("d00");
    }

    function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        if (is_object($this->btnRemoveAll))
            $this->btnRemoveAll->setPosition(4, -$this->sizeY + 6);

        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, 0);
    }

    protected function onDraw()
    {
        $login = $this->getRecipient();
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();

        $isAdmin = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::map_jukebox_admin);
        if ($isAdmin) {
            $this->mainFrame->addComponent($this->btnRemoveAll);
        }

        $x = 0;
        foreach ($this->maps as $map) {
            $this->items[$x] = new Wishitem($x, $login, $map, self::$mainPlugin, $isAdmin, $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
        parent::onDraw();
    }

    function setList($maps)
    {
        $this->maps = $maps;
    }

    function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        if (is_object($this->btnRemoveAll))
            $this->btnRemoveAll->destroy();
        $this->pager->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

}

?>
