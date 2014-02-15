<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide;

class LivePanel extends LocalPanel {
    
    public static $connection;
    
    function onConstruct() {
        parent::onConstruct();
        $this->setName("Live Rankings Panel");
	$this->timeScript->setParam('varName','LiveTime1');
	$this->timeScript->setParam('getCurrentTimes','True');
    }

    protected function getScript() {
	if($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK)
	    return parent::getScript();
	else{
	    $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_RecordSide/Gui/Scripts/CpPositions");
	    $this->timeScript = $script;
	    $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
	    $this->timeScript->setParam("nbFields", 20);
	    $this->timeScript->setParam("nbFirstFields", 5);
	    $this->timeScript->setParam('varName','LiveTime1');
	    return $script;
	}
    }
    
    function update() {
	
	$login = $this->getRecipient();
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = array();
        $this->frame->clearComponents();

        $index = 1;

        $this->lbl_title->setText('Live Rankings');


        $recsData = "";
        $nickData = "";

        for ($index = 1; $index <= $this->nbFields; $index++) {
            $this->items[$index - 1] = new Recorditem($index, false);
            $this->frame->addComponent($this->items[$index - 1]);
        }
	
	if($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK)
	    $this->taUpdate ();
	else
	    $this->cpUpdate();
    }

    protected function taUpdate(){

        $index = 1;
	$players = self::$connection->getCurrentRanking(100,0);
	
        foreach($players as $player){
            if(!empty($player->bestTime) && $player->bestTime > 0){
                 if ($index > 1) {
                    $recsData .= ', ';
                    $nickData .= ', ';
                }
                $recsData .= '"'.$player->login. '"=>'.$player->bestTime;
                $nickData .= '"'.$player->login. '"=>"'.$this->fixHyphens($player->nickName).'"';
                $index++;
            }
        }
	echo $recsData."\n";

        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);

        if (empty($recsData)) {
            $recsData = 'Integer[Text]';
            $nickData = 'Text[Text]';
        } else {
            $recsData = '[' . $recsData . ']';
            $nickData = '[' . $nickData . ']';
        }


        /* $recsData = 'Integer[Text]';
          $nickData = 'Text[Text]';
         */
        $this->timeScript->setParam("playerTimes", $recsData);
        $this->timeScript->setParam("playerNicks", $nickData);
    }
    
    protected function cpUpdate(){
	
	
    }
    
}

?>
