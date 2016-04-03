<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Players\Gui\Controls\Playeritem;
use ManiaLive\Gui\ActionHandler;

class GenericPlayerList extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $pager;
    private $connection;
    private $storage;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 5, $this->sizeY - 8);
        $this->pager->setPosition(2, 0);
    }

    /**
     *
     * @param type $items ArrayOfObject
     */
    function populateList($items)
    {
        $this->pager->clearItems();

        foreach ($items as $item)
            $this->pager->addItem($item);
    }

    function destroy()
    {
        parent::destroy();
    }

}

?>
