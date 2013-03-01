<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem;
use ManiaLive\Gui\ActionHandler;

class Maplist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    /** @var  \DedicatedApi\Connection */
    private $connection;

    /** @var  \ManiaLive\Data\Storage */
    private $storage;
    public static $records = array();
    public static $mapsPlugin = null;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
       
    }

    function gotoMap($login, \DedicatedApi\Structures\Map $map) {
       self::$mapsPlugin->gotoMap($login, $map);
        $this->Erase($login);
    }

    function removeMap($login,  \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->removeMap($login, $map);
    }

    function chooseNextMap($login, \DedicatedApi\Structures\Map $map) {
       self::$mapsPlugin->chooseNextMap($login, $map);
        $this->Erase($login);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);        
         $this->populateList();
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -10);
    }

    function onShow() {
     
    }    

    function populateList() {       
        $this->pager->clearItems();
        $x = 0;
        $login = $this->getRecipient();
        $isAdmin = \ManiaLive\Features\Admin\AdminGroup::contains($login);
        foreach ($this->storage->maps as $map)
            $this->pager->addItem(new Mapitem($x++, $login, $map, $this, $isAdmin));
    }

    function destroy() {           
        parent::destroy();
    }

}

?>
