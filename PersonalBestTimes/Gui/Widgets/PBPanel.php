<?php

namespace ManiaLivePlugins\eXpansion\PersonalBestTimes\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\PersonalBestTimes\PersonalBestTimes;

class PBPanel extends \ManiaLive\Gui\Window {

    private $label;
    public static $records;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $label = new \ManiaLib\Gui\Elements\Label();        
        $label->setText('$ddd'.__('Personal Best'));
        $label->setAlign("right", "top");                
        $this->addComponent($label);
        
        $label = new \ManiaLib\Gui\Elements\Label(16,4);        
        $info = "-";
        
        $label->setScale(0.8);
        if (array_key_exists($login, PersonalBestTimes::$personalBestTimes))  {
            $info = \ManiaLive\Utilities\Time::fromTM(PersonalBestTimes::$personalBestTimes[$login]->time);
            $info = substr($info, 0, -1);
            $label->setScale(0.7);
        }
        
        $label->setText('$ddd'.$info);
        
        $label->setAlign("center", "top");
        $label->setPosX(5);
        $this->addComponent($label);
        
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    function destroy() {
        parent::destroy();
    }

}

?>
