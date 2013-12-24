<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Dropdown extends \ManiaLive\Gui\Control {

    private $items = array();
    private $dropdown;
    private $output;
    private $frame;
    private $label;
    private $xml;
    private $values;
    private $name;

    function __construct($name, $items, $selectedIndex = 0, $sizeX = 35) {
        if (!is_array($items))
            throw new \Exception("Dropdown constructor needs array of values");
        $this->values = $items;
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
        $this->label->setText($this->values[$selectedIndex]);
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

        $x = 0;
        foreach ($this->values as $item) {
            $obj = new \ManiaLib\Gui\Elements\Label($sizeX);
            $obj->setText($item);
            $obj->setAlign("left", "center");
            $obj->setStyle("TextValueMedium");
            $obj->setScriptEvents(true);
            $obj->setTextSize(1);
            $obj->setFocusAreaColor1('000');
            $obj->setFocusAreaColor2('fff');
            $obj->setId($name . $x);

            $this->items[$x] = $obj;
            $this->frame->addComponent($this->items[$x]);
            $x++;
        }
        $this->addComponent($this->frame);
    }

    function getScriptDeclares($dropdownIndex) {
        return '           
                            declare CMlFrame Frame' . $dropdownIndex . ' <=> (Page.GetFirstChild("' . $this->name . 'f") as CMlFrame);
                            declare CMlLabel Label' . $dropdownIndex . ' <=> (Page.GetFirstChild("' . $this->name . 'l") as CMlLabel);
                            declare CMlEntry Output' . $dropdownIndex . ' <=> (Page.GetFirstChild("' . $this->name . 'e") as CMlEntry);
                            Frame' . $dropdownIndex . '.Hide();
     ';
    }

    function getScriptMainLoop($dropdownIndex) {
        $loop = ' 
                            if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "' . $this->name . 'l") { 
                                    Frame' . $dropdownIndex . '.Show();
                           } else {
                                   if (Event.Type == CMlEvent::Type::MouseClick) {
                                        Frame' . $dropdownIndex . '.Hide();
                                   }
                            }
            ';

        $x = 0;
        foreach ($this->values as $item) {
            $loop .= '
                             if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "' . $this->name . $x . '") {                           
                                           Label' . $dropdownIndex . '.Value = "' . $item . '";
                                           Output' . $dropdownIndex . '.Value = "' . $x . '";
                                           Frame' . $dropdownIndex . '.Hide();
                            } 
                      ';
            $x++;
        }

        return $loop;
    }

    public function getDropdownItems() {
        return $this->values;
    }

    public function setSelected($index) {
        $this->label->setText($this->values[intval($index)]);
    }

}

?>