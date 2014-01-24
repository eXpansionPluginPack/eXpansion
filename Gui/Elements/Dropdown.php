<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Dropdown extends \ManiaLive\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer {

    private $items = array();
    private $dropdown;
    private $output;
    private $frame;
    private $label;
    private $xml;
    private $values;
    private $name;
    /** @var \ManiaLivePlugins\eXpansion\Gui\Structures\Script */
    private $script = null;
    
    function __construct($name, $items = array("initial"), $selectedIndex = 0, $sizeX = 35) {
        if (!is_array($items))
            throw new \Exception("Dropdown constructor needs array of values");
        
        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Scripts\DropDownScript();
        $this->script->setParam("name", $name);            
       
        
        $this->values = array();
        $this->name = $name;
        $this->setSize($sizeX, 10);

        $this->output = new \ManiaLib\Gui\Elements\Entry($sizeX, 6);
        $this->output->setName($name);
        $this->output->setTextColor('000');
        $this->output->setTextSize(1);
        $this->output->setId($name . "e");
        $this->output->setScriptEvents(true);
        $this->output->setPosition(1000, 1000);
        $this->addComponent($this->output);

        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, 4);
        $this->label->setId($name . 'l');
        $this->label->setStyle("TextValueMedium");
        $this->label->setTextSize(1);
        $this->label->setAlign('left', 'center');
        $this->label->setScriptEvents(true);
        $this->addComponent($this->label);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, 0);
        $this->frame->setLayout(New \ManiaLib\Gui\Layouts\Flow($sizeX, 6));
        $this->frame->setScriptEvents(true);
        $this->frame->setId($name . "f");
        $this->frame->setAlign("center", "center");
        $this->frame->setSizeY((sizeof($items) + 1) * 6);
        $this->frame->setScale(0.9);
        $this->values = array();
        $this->addItems($items);
        $this->script->setParam("values", $this->values);
        $this->script->setParam("values", $this->values);
        $this->addComponent($this->frame);
        $this->setSelected($selectedIndex);        
    }

    function addItems($items) {
        $x = 0;
        $this->values = array();
        $this->items = array();
        foreach ($items as $item) {
            $this->addEntry($item);
        }
    }

    function addEntry($item) {
        $x = count($this->items);
        $obj = new \ManiaLib\Gui\Elements\Label($this->sizeX);
        $obj->setText($item);
        $obj->setAlign("left", "center");
        $obj->setStyle("TextValueMedium");
        $obj->setScriptEvents(true);
        $obj->setTextSize(1);
        $obj->setFocusAreaColor1('000');
        $obj->setFocusAreaColor2('fff');
        $obj->setId($this->name . $x);
        $this->items[$x] = $obj;
        $this->frame->addComponent($this->items[$x]);
        $this->values[$x] = $item;
        $x++;
    }

    public function getDropdownItems() {
        return $this->values;
    }

    public function setSelected($index) {
        $this->label->setText($this->values[intval($index)]);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function getScript() {
        return $this->script;
    }

}

?>