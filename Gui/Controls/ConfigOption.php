<?php

namespace ManiaLivePlugins\eXpansion\Gui\Controls;

/**
 * Description of ConfigOption
 *
 * @author Reaby
 */
class ConfigOption extends \ManiaLive\Gui\Control {

    protected $cb_item;
    private $status;

    function __construct($x, \ManiaLivePlugins\eXpansion\Gui\Structures\ConfigItem $status, $login, $sizeX) {
        $this->status = $status;
        $this->setSize(60, 5);
        $this->cb_item = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4, 50);
        $this->cb_item->setStatus($status->value);
        $this->cb_item->setText($status->id);
        $this->addComponent($this->cb_item);
    }

    public function getText() {
        return $this->status->id;
    }

    public function getStatus() {
        return $this->cb_item->getStatus();
    }
    

    public function destroy() {
        
    }

    public function erase() {
        $this->clearComponents();
        parent::destroy();
    }

}
