<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Widgets;

use \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint;
use ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Controls\CheckpointElem;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class BestCpPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget {

    private $cps = array();
    private $maxCpIndex = 18;
    
    
    /** @var \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint[]  */
    public static $bestTimes;
    private $frame;
    private $storage;
    private $timeScript;

    protected function onConstruct() {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(220, 20));
        $this->frame->setSize(220, 20);
        $this->frame->setPosY(-2);
        $this->addComponent($this->frame);
        $this->setName("Best CheckPoints Widget");
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        
        $this->timeScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script('Widgets_BestCheckpoints/Gui/Scripts/BestCps');
        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
        $this->registerScript($this->timeScript);
    }

    function onDraw() {
        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
        
        foreach ($this->cps as $cp) {
            $cp->destroy();
        }
        $this->cps = array();
        $this->frame->clearComponents();

        $x = 0;

        $timeData = "";
        $nickData = "";

        foreach (self::$bestTimes as $cp) {
            $this->cps[$x] = new CheckpointElem($x, $cp);
            $this->frame->addComponent($this->cps[$x]);
            
            if ($x > 0) {
                $timeData .= ', ';
                $nickData .= ', ';
            }
            $timeData .= '' . $x . '=>' . $cp->time;
            $nickData .= '' . $x . '=>"' . Gui::fixString($cp->nickname) . '"';
            $x++;
        }

        for (;$x < 24 && $x < $this->storage->currentMap->nbCheckpoints; $x++) {
            $timeData .= '' . $x . '=>0';
            $nickData .= '' . $x . '=>"-"';
            
            $this->cps[$x] = new CheckpointElem($x);
            $this->frame->addComponent($this->cps[$x]);
        }
        
        if (empty($timeData)) {
            $timeData = 'Integer[Integer]';
            $nickData = 'Text[Integer]';
        } else {
            $timeData = '[' . $timeData . ']';
            $nickData = '[' . Gui::fixString($nickData) . ']';
        }

        $this->timeScript->setParam("cpTimes", $timeData);
        $this->timeScript->setParam("playerNicks", $nickData);
	$this->timeScript->setParam("maxCpIndex", $this->maxCpIndex);
        parent::onDraw();
    }

    function destroy() {
        parent::destroy();
        foreach ($this->cps as $cp) {
            $cp->destroy();
        }
        $this->cps = array();
        $this->clearComponents();

        parent::destroy();
    }
    
}

?>
