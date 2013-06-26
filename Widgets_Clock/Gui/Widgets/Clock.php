<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets;

class Clock extends \ManiaLive\Gui\Window {

    private $xml;
    private $clock;
    private $date;

    protected function onConstruct() {
        parent::onConstruct();

        $this->clock = new \ManiaLib\Gui\Elements\Label();
        $this->clock->setId('clock');
        $this->clock->setAlign("left", "top");
        $this->clock->setPosition(0, -5);
        $this->clock->setTextColor('fff');
        $this->clock->setScale(0.8);
        $this->clock->setStyle('TextCardScores2');
        //$this->clock->setTextPrefix('$s');
        $this->addComponent($this->clock);

        $this->date = new \ManiaLib\Gui\Elements\Label(60,6);
        $this->date->setId('date');
        $this->date->setAlign("left", "top");
        $this->date->setPosition(0, 0);
        $this->date->setTextColor('fff');
        $this->date->setTextPrefix('$s');
        $this->date->setText(\ManiaLive\Data\Storage::getInstance()->server->name);
        $this->addComponent($this->date);

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->xml->setContent('
              <script><!--
              #Include "TextLib" as TextLib 
              
                       main () {     
                            declare Window <=> Page.GetFirstChild("' . $this->getId() . '");    
                            declare CMlLabel lbl_clock <=> (Page.GetFirstChild("clock") as CMlLabel);
                            declare CMlLabel lbl_date <=> (Page.GetFirstChild("date") as CMlLabel);

                            while(True) { 
                                  // lbl_date.SetText(""^TextLib::SubString(CurrentLocalDateText, 8, 2)^"."^TextLib::SubString(CurrentLocalDateText, 5, 2)^"."^TextLib::SubString(CurrentLocalDateText, 0, 4) );
                                   lbl_clock.SetText(""^TextLib::SubString(CurrentLocalDateText, 11, 2)^":"^TextLib::SubString(CurrentLocalDateText, 14, 2)^":"^TextLib::SubString(CurrentLocalDateText, 17, 2));
                            yield;
                            }
                        }
                        -->
                        </script>
                        
');
        $this->addComponent($this->xml);
    }

    function onDraw() {
        
    }

    function destroy() {
        $this->clearComponents();

        parent::destroy();
    }

}

?>
