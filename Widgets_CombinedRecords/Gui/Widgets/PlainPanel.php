<?php

namespace ManiaLivePlugins\eXpansion\Widgets_CombinedRecords\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\Config;

use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;
use ManiaLivePlugins\eXpansion\Widgets_CombinedRecords\Widgets_CombinedRecords;
use ManiaLivePlugins\eXpansion\Widgets_CombinedRecords\Config as CombiConfig;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Controls\Recorditem;

class PlainPanel extends \ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Widgets\PlainPanel
{

    public function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();
        $this->setName("Combined Panel");
        $this->timeScript->setParam('varName', 'CombiTime1');
    }

    public function update()
    {

        foreach ($this->items as $item)
            $item->destroy();
        $this->items = array();
        $this->frame->clearComponents();

        $index = 1;

        $this->bgTitle->setText('Combined Records');

        for ($index = 1; $index <= $this->nbFields; $index++) {
            $this->items[$index - 1] = new Recorditem($index, false);
            $this->frame->addComponent($this->items[$index - 1]);
        }


        $combinedRecs = array();
        $combinedNicks = array();

        foreach (Widgets_CombinedRecords::$localrecords as $record) {
            /** @var Record $record */
            $combinedRecs[$record->login] = $record->time;
            $combinedNicks[$record->login] = $record->nickName;
        }

        foreach (Widgets_CombinedRecords::$dedirecords as $record) {
            if (array_key_exists($record['Login'], $combinedRecs)) {
                if ($record['Best'] < $combinedRecs[$record['Login']]) {
                    $combinedRecs[$record['Login']] = $record['Best'];
                }
            } else {
                $combinedRecs[$record['Login']] = $record['Best'];
            }

            if (!array_key_exists($record['Login'], $combinedNicks)) {
                $combinedNicks[$record['Login']] = $record['NickName'];
            }
        }

        asort($combinedRecs, SORT_NUMERIC);

        $recsData = "";
        $nickData = "";
        $index = 1;

        foreach ($combinedRecs as $login => $rec) {
            if ($index > 1) {
                $recsData .= ', ';
                $nickData .= ', ';
            }
            $recsData .= '"' . Gui::fixString($login) . '"=>' . $rec;
            $nickData .= '"' . Gui::fixString($login) . '"=>"' . Gui::fixString($combinedNicks[$login]) . '"';
            $index++;
        }

        if (empty($recsData)) {
            $recsData = 'Integer[Text]';
            $nickData = 'Text[Text]';
        } else {
            $recsData = '[' . $recsData . ']';
            $nickData = '[' . $nickData . ']';
        }


        $this->timeScript->setParam("playerTimes", $recsData);
        $this->timeScript->setParam("playerNicks", $nickData);

        $recCount = Config::getInstance()->recordsCount;

        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
        $this->timeScript->setParam("nbRecord", $recCount);
        $this->timeScript->setParam("acceptMaxServerRank", $recCount);
        $this->timeScript->setParam("acceptMaxPlayerRank", "Integer[Text]");
        $this->timeScript->setParam("useMaxPlayerRank", "False");
        $this->timeScript->setParam("acceptMinCp", 0);
        $this->timeScript->setParam("nbFields", CombiConfig::getInstance()->nbTotal);
        $this->timeScript->setParam("nbFirstFields", CombiConfig::getInstance()->nbTop);
        $this->timeScript->setParam('varName', 'LocalTime1');
        $this->timeScript->setParam('getCurrentTimes', Widgets_CombinedRecords::$secondMap ? "True" : "False");

    }

    public function fixDashes($string)
    {
        $out = str_replace('--', '––', $string);

        return $out;
    }

}
