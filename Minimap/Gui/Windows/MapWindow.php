<?php

namespace ManiaLivePlugins\eXpansion\Minimap\Gui\Windows;

use ManiaLivePlugins\eXpansion\Minimap\Config;

class MapWindow extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $minimap, $quad;
    protected $config;
    private $script;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setName("Minimap");
        $this->config = Config::getInstance();
        $this->quad = new \ManiaLivePlugins\eXpansion\Gui\Elements\Minimap();
        $this->quad->setId("map");
        $this->quad->setPosition(90, 0);
        $this->quad->setSize(60, 60);
        $this->addComponent($this->quad);

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Minimap/Gui/Script");
        $this->registerScript($this->script);

    }

}

?>
