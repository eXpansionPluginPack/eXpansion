<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class TimePanel extends \ManiaLive\Gui\Window {

    const Mode_BestOfAll = 1;
    const Mode_PersonalBest = 2;

    private $checkpoint;
    private $time;
    private $bestRun = array();
    private $currentRun = array();
    private $lastFinish = -1;
    private $counter = 1;

    /** @var \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record[] */
    public static $localrecords = array();
    public static $dedirecords = array();

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->time = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->time->setPosX(7, 40);
        $this->time->setAlign("left", "center");
        $this->time->setStyle("TextTitle2");
        $this->addComponent($this->time);

        $this->checkpoint = new \ManiaLib\Gui\Elements\Label(6, 4);
        $this->checkpoint->setPosX(0, 40);
        $this->checkpoint->setTextColor("fff");
        $this->checkpoint->setAlign("left", "center");
        $this->addComponent($this->checkpoint);
        $this->setAlign("center", "top");
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    public function onCheckpoint($time, $cpIndex, $cpTotal, $mode) {
        $this->currentRun[$cpIndex] = $time;
        $this->checkpoint->setText($this->counter . "(" . ($cpIndex + 1) . "/" . $cpTotal . ")");

        $dedicp = array();
        $localcp = array();

        $this->time->setTextColor('fffa');
        $this->time->setText(\ManiaLive\Utilities\Time::fromTM($time, false));


        if ($mode == self::Mode_BestOfAll) {
            $dedicp = array();
            $localcp = array();
            if (isset(self::$dedirecords[0]))
                if (array_key_exists('Checks', self::$dedirecords[0]))
                    $dedicp = explode(",", self::$dedirecords[0]['Checks']);

            if (isset(self::$localrecords[0]))
                $localcp = self::$localrecords[0]->ScoreCheckpoints;
        }

        if ($mode == self::Mode_PersonalBest) {
            $dedicp = array();
            foreach (self::$dedirecords as $dedirec) {
                if ($dedirec['Login'] == $this->getRecipient()) {
                    if (array_key_exists('Checks', $dedirec))
                        $dedicp = explode(",", $dedirec['Checks']);
                    break;
                }
            }
            $record = \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::getObjbyPropValue(self::$localrecords, "login", $this->getRecipient());
            $localcp = array();
            if ($record) {
                $localcp = $record->ScoreCheckpoints;
            }
        }

        // determin whenever to use deditime or localtime from the checkpoints
        $deditime = 0;
        $localtime = 0;
        $diff = null;
        if (isset($dedicp[$cpIndex])) {
            $deditime = $dedicp[$cpIndex];
            $diff = $deditime;
        }

        if (isset($localcp[$cpIndex]))
            $localtime = $localcp[$cpIndex];


        if ($localtime > $deditime)
            $diff = $localtime;

        if ($diff == null)
            if (isset($this->bestRun[$cpIndex]))
                $diff = $this->bestRun[$cpIndex];

        if ($diff !== null) {
            // if no records found for dedimania or local, fallback to personal best
            $this->time->setText(\ManiaLive\Utilities\Time::fromTM($time - $diff, true));
            $this->time->setTextColor('a00a');
            if ($diff > $time)
                $this->time->setTextColor('00aa');
        }
    }

    public function onFinish($time) {
        if ($time < $this->lastFinish || $this->lastFinish == -1) {
            $this->lastFinish = $time;
            $this->bestRun = $this->currentRun;
            $this->counter++;
        }
    }

    public function onStart() {
        $this->currentRun = array();
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
