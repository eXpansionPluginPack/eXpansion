<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Controls;

class ItemPlugin extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $label;

    /** @var \ManiaLivePlugins\eXpansion\Gui\Elements\CheckboxScripted */
    public $checkbox;

    public $pluginId;

    public function __construct($pluginId, \ManiaLivePlugins\eXpansion\Core\types\config\MetaData $meta)
    {
        $this->sizeX = 100;
        $this->sizeY = 6;
        $this->setAlign("left", "top");

        $this->pluginId = $pluginId;

        $this->checkbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\CheckboxScripted(4, 4, 60);
        $this->checkbox->setStatus(false);
        $this->checkbox->setText($meta->getName());
        $this->addComponent($this->checkbox);
    }

    public function setStatus($boolean)
    {
        $this->checkbox->setStatus($boolean);
    }

    public function destroy()
    {
        $this->checkbox->destroy();
        parent::destroy();
    }
}
