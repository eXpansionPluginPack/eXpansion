<?php

namespace ManiaLivePlugins\eXpansion\Widgets_PersonalBest\Gui\Widgets;

class PBPanel extends \ManiaLive\Gui\Window {

    private $record;

    private $pb;
    private $avg;
    private $wins;
    private $finish;
    
    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $label = new \ManiaLib\Gui\Elements\Label();        
        $label->setText('$ddd'.__('Personal Best'));
        $label->setAlign("right", "top");
        $label->setScale(0.7);
        $this->addComponent($label);
        
        $this->pb = new \ManiaLib\Gui\Elements\Label(16,4);
        $this->pb->setScale(0.7);
        $this->pb->setAlign("left", "top");
        $this->pb->setPosX(1);
        $this->addComponent($this->pb);
        
        $label = new \ManiaLib\Gui\Elements\Label();        
        $label->setText('$ddd'.__('Average'));
        $label->setAlign("right", "top");
        $label->setScale(0.7);
        $label->setPosY(-3);
        $this->addComponent($label);
        
        $this->avg = new \ManiaLib\Gui\Elements\Label(16,4);
        $this->avg->setScale(0.7);
        $this->avg->setAlign("left", "top");
        $this->avg->setPosX(1);
        $this->avg->setPosY(-3);
        $this->addComponent($this->avg);
        
        $label = new \ManiaLib\Gui\Elements\Label();        
        $label->setText('$ddd'.__('Finishes'));
        $label->setAlign("right", "top");
        $label->setScale(0.7);
        $label->setPosY(-6);
        $this->addComponent($label);
        
        $this->finish = new \ManiaLib\Gui\Elements\Label(16,4);
        $this->finish->setScale(0.7);
        $this->finish->setAlign("left", "top");
        $this->finish->setPosX(1);
        $this->finish->setPosY(-6);
        $this->addComponent($this->finish);
     
    }
    
    function setRecord($record){
        $this->record = $record;
        if($record == null){
            $pbTime = \ManiaLive\Utilities\Time::fromTM(0);
            $avgTime = $pbTime;
            $nbFinish = 0;
        }else{
             $pbTime = \ManiaLive\Utilities\Time::fromTM($record->time);
             $avgTime = \ManiaLive\Utilities\Time::fromTM($record->avgScore);
             $nbFinish = $record->nbFinish;
        }
        $pbTime = substr($pbTime, 0, -1);
        $avgTime = substr($avgTime, 0, -1);
        
        $this->pb->setText('$ddd'.$pbTime);
        $this->avg->setText('$ddd'.$avgTime);
        $this->finish->setText('$ddd'.$nbFinish);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
