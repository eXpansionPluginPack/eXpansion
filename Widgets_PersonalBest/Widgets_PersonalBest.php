<?php

namespace ManiaLivePlugins\eXpansion\Widgets_PersonalBest;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;
use ManiaLivePlugins\eXpansion\Widgets_PersonalBest\Gui\Widgets\PBPanel;

/**
 * Description of Widgets_PersonalBest
 *
 * @author oliverde8
 */
class Widgets_PersonalBest extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onLoad() {
        parent::exp_onLoad();
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_PERSONAl_BEST);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_NEW_RECORD);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    function onPlayerConnect($login, $isSpectator) {
        $this->displayRecordWidget($login);
    }

    function onUpdateRecords($record) {
        foreach ($this->storage->players as $player)
            $this->redrawWidget($player->login);
        foreach ($this->storage->spectators as $player)
            $this->redrawWidget($player->login);
    }

    public function onPersonalBestRecord(Record $record) {
        $this->redrawWidget($record->login, $record);
    }

    public function onNewRecord($records) {
        foreach ($this->storage->players as $player)
            $this->redrawWidget($player->login);
        foreach ($this->storage->spectators as $player)
            $this->redrawWidget($player->login);
    }

    public function redrawWidget($login = null) {
        $record = null;
        if ($login != null) {
            $record = $this->callPublicMethod('eXpansion\\LocalRecords', 'getCurrentChallangePlayerRecord', $login);
        }
        $this->displayRecordWidget($login, $record);
    }

    function displayRecordWidget($login, $record = null) {
        PBPanel::Erase($login);

        if ($login == null)
            return;

        if ($record == null)
            $record = $this->callPublicMethod('eXpansion\\LocalRecords', 'getCurrentChallangePlayerRecord', $login);

        $rank = $this->callPublicMethod('eXpansion\\LocalRecords', 'getPlayerRank', $login);
        if ($rank == -1)
            $rank = '--';
        if ($rank == -2)
            $rank = '';
        $rankTotal = $this->callPublicMethod('eXpansion\\LocalRecords', 'getTotalRanked');

        $info = PBPanel::Create($login, $rank, $rankTotal);
        $info->setRecord($record, $rank, $rankTotal);
        $info->setSize(30, 6);
        $info->setPosition(132, -76);
        $info->show();
    }

}

?>