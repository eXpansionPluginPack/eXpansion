<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Widgets_DedimaniaRecords;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Controls\Recorditem;

class PlainPanel extends \ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Widgets\PlainPanel
{

    public function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();
        $this->setName("Dedimania Panel");
        $this->timeScript->setParam("acceptMinCp", 1);
        $this->timeScript->setParam('varName', 'DediTime1');
        $this->timeScript->setParam("acceptMaxServerRank", Connection::$serverMaxRank);
        $this->bg->setAction(\ManiaLivePlugins\eXpansion\Dedimania\DedimaniaAbstract::$actionOpenRecs);
    }

    public function update()
    {
        $this->timeScript->setParam(
            "acceptMaxPlayerRank",
            \ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection::$serverMaxRank
        );
        $login = $this->getRecipient();

        foreach ($this->items as $item)
            $item->destroy();
        $this->items = array();
        $this->frame->clearComponents();

        $index = 1;

        $this->bgTitle->setText('Dedimania Records');


        $recsData = "";
        $nickData = "";

        for ($index = 1; $index <= $this->nbFields; $index++) {
            $this->items[$index - 1] = new Recorditem($index, false);
            $this->frame->addComponent($this->items[$index - 1]);
        }

        $index = 1;
        foreach (Widgets_DedimaniaRecords::$dedirecords as $record) {
            if ($index > 1) {
                $recsData .= ', ';
                $nickData .= ', ';
            }
            $recsData .= '"' . Gui::fixString($record['Login']) . '"=> ' . $record['Best'];
            $nickData .= '"' . Gui::fixString($record['Login']) . '"=>"' . Gui::fixString($record['NickName']) . '"';
            $index++;
        }
        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);

        if (empty($recsData)) {
            $recsData = 'Integer[Text]';
            $nickData = 'Text[Text]';
        } else {
            $recsData = '[' . $recsData . ']';
            $nickData = '[' . $nickData . ']';
        }

        $this->timeScript->setParam("nbRecord", 100);
        $this->timeScript->setParam("acceptMaxServerRank", Connection::$serverMaxRank);
        $this->timeScript->setParam("playerTimes", $recsData);
        $this->timeScript->setParam("playerNicks", $nickData);
        $this->timeScript->setParam("acceptMaxPlayerRank", "Integer[Text]");
        $this->timeScript->setParam("useMaxPlayerRank", "True");
        if (count(\ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection::$players) > 0) {
            $out = "[";
            foreach (\ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection::$players as $dediplayer) {
                $out .= '"' . Gui::fixString($dediplayer->login) . '" => ' . $dediplayer->maxRank . ',';
            }
            $out = trim($out, ",");
            $out = $out . "]";

            $this->timeScript->setParam("acceptMaxPlayerRank", $out);
        }
    }

    public function fixDashes($string)
    {
        $out = str_replace('--', '––', $string);

        return $out;
    }

    public function fixHyphens($string)
    {
        $out = str_replace('"', "'", $string);
        $out = str_replace('\\', '\\\\', $out);
        $out = str_replace('-', '–', $out);

        return $out;
    }

}
