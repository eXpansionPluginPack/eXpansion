<?php

namespace ManiaLivePlugins\eXpansion\Widgets_PersonalBest\Gui\Widgets;

class PBPanel extends \ManiaLive\Gui\Window {

    private $record;

    private $pb;
    private $avg;
    private $wins;
    private $finish;
    private $rank;
    private $rankLoading;

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

        $label = new \ManiaLib\Gui\Elements\Label();
        $label->setText('$ddd'.__('Server Rank'));
        $label->setAlign("right", "top");
        $label->setScale(0.7);
        $label->setPosY(-9);
        $this->addComponent($label);

        $this->rank = new \ManiaLib\Gui\Elements\Label(16,4);
        $this->rank->setScale(0.7);
        $this->rank->setAlign("left", "top");
        $this->rank->setPosX(1);
        $this->rank->setPosY(-9);
        $this->addComponent($this->rank);

        $this->rankLoading = new \ManiaLib\Gui\Elements\Quad(6,6);
        $this->rankLoading->setScale(0.7);
        $this->rankLoading->setPosX(-.8);
        $this->rankLoading->setPosY(-8);
        $this->rankLoading->setStyle('Icons128x128_Blink');
        $this->rankLoading->setSubStyle('Default');
        $this->addComponent($this->rankLoading);

    }

    function setRecord($record, $rank, $rankTotal){
        $this->record = $record;
        if($record == null){
            $pbTime = '--';
            $avgTime = $pbTime;
            $nbFinish = 0;
        }else{
            $pbTime = \ManiaLive\Utilities\Time::fromTM($record->time);
            if (substr($pbTime, 0, 2) === "0:") {
                $pbTime = substr($pbTime, 2);
            }
            $avgTime = \ManiaLive\Utilities\Time::fromTM($record->avgScore);
            if (substr($avgTime, 0, 2) === "0:") {
                $avgTime = substr($avgTime, 2);
            }
            $nbFinish = $record->nbFinish;
        }

        $this->pb->setText('$ddd'.$pbTime);
        $this->avg->setText('$ddd'.$avgTime);
        $this->finish->setText('$ddd'.$nbFinish);
        $this->rank->setText('$ddd'.$rank.'$n $m/$n $m'.$rankTotal);
        if($rank == - 2){
            $this->rankLoading->setVisibility(true);
            $this->rank->setPosX(3);
        }else{
            $this->rankLoading->setVisibility(false);
            $this->rank->setPosX(1);
        }
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
