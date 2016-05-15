<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times;

use ManiaLive\Event\Dispatcher;
use ManiaLive\PluginHandler\Dependency;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets\TimePanel;

class Widgets_Times extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    protected $references = array();

    function expOnInit()
    {
        //	$this->addDependency(new Dependency('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords'));
    }

    function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
    }

    function eXpOnReady()
    {
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania')) {
            Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
        }
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script')) {
            Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
        }
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords')) {
            Dispatcher::register(LocalEvent::getClass(), $this);
            try {
                TimePanel::$localrecords = $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRecords");
            } catch (\Exception $e) {
                TimePanel::$localrecords = array();
            }
        }

        $this->registerChatCommand("cps", "chat_cps", 1, true);
        $this->showToAll();
    }

    public function chat_cps($login, $value)
    {
        if (!is_numeric($value)) {
            $this->eXpChatSendServerMessage(eXpGetMessage('#error#"%s" is not a numeric value!'), null, array($value));

            return;
        }

        if ($value < 1) {
            $this->eXpChatSendServerMessage(eXpGetMessage('#error#"%s" is less than 1!'), null, array($value));

            return;
        }

        $this->eXpChatSendServerMessage(eXpGetMessage('#info#New time reference point set to %s'), null, array($value));
        $this->references[$login] = (int)$value;
        $this->showPanel($login, $this->storage->getPlayerObject($login));
    }

    public function onPlayerInfoChanged($playerInfo)
    {
        $player = \Maniaplanet\DedicatedServer\Structures\PlayerInfo::fromArray($playerInfo);
        $this->showPanel($player->login, $player);
    }

    public function onBeginMatch()
    {
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords')) {
            if (empty(TimePanel::$localrecords)) {
                try {
                    TimePanel::$localrecords = $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords",
                        "getRecords");
                } catch (\Exception $e) {
                    TimePanel::$localrecords = array();
                }
            }
        }
        $this->showToAll();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        //TimeChooser::EraseAll();
        TimePanel::$dedirecords = Array();
        TimePanel::$localrecords = Array();
    }

    public function showToAll()
    {
        TimePanel::EraseAll();
        foreach ($this->storage->players as $player)
            $this->showPanel($player->login, $player);

        foreach ($this->storage->spectators as $player)
            $this->showPanel($player->login, $player);
    }

    function showPanel($login, $playerObject)
    {

        $spectatorTarget = $login;

        if ($playerObject->currentTargetId) {
            $spec = $this->getPlayerObjectById($playerObject->currentTargetId);
            if ($spec->login) {
                $spectatorTarget = $spec->login;
            }
        }

        TimePanel::Erase($login);
        $info = TimePanel::Create($login);
        $info->setSize(30, 6);
        $info->setPosition(-16, 46);
        if (!$this->expStorage->isRelay) {
            $info->setTarget($spectatorTarget);
        } else {
            $info->setTarget("");
        }
        if (array_key_exists($login, $this->references)) $info->setReference($this->references[$login]);

        $info->setMapInfo($this->storage->currentMap);
        $info->show();
    }

    function onPlayerConnect($login, $isSpectator)
    {
        $this->showPanel($login, $this->storage->getPlayerObject($login));
    }

    function onPlayerDisconnect($login, $reason = null)
    {
        TimePanel::Erase($login);
    }

    public function onRecordsLoaded($data)
    {
        TimePanel::$localrecords = $data;
        $this->showToAll();
    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record $record
     */
    public function onUpdateRecords($data)
    {
        TimePanel::$localrecords = $data;
        /*
          TimePanel::$localrecords = $data;
          $this->console("Records Changed, reload!");
          $this->showToAll(); */
    }

    public function onDedimaniaUpdateRecords($data)
    {
        TimePanel::$dedirecords = $data['Records'];
    }

    public function onDedimaniaGetRecords($data)
    {
        TimePanel::$dedirecords = $data['Records'];
        $this->showToAll();
    }

    public function onRecordPlayerFinished($login)
    {

    }

    public function onDedimaniaOpenSession()
    {

    }

    public function onNewRecord($data)
    {

    }

    public function onPersonalBestRecord($data)
    {
        $this->showPanel($data->login, false);
    }

    public function onDedimaniaPlayerConnect($data)
    {

    }

    public function onDedimaniaPlayerDisconnect($login)
    {

    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord $record
     * @param \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecorr $oldrecord
     */
    public function onDedimaniaRecord($record, $oldrecord)
    {
        foreach (TimePanel::$dedirecords as $index => $data) {
            if (TimePanel::$dedirecords[$index]['Login'] == $record->login) {
                TimePanel::$dedirecords[$index] = Array("Login" => $record->login, "NickName" => $record->nickname, "Rank" => $record->place,
                    "Best" => $record->time, "Checks" => $record->checkpoints);
            }
        }
        $this->showPanel($record->login, false);
    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord $data
     */
    public function onDedimaniaNewRecord($record)
    {
        foreach (TimePanel::$dedirecords as $index => $data) {
            if (TimePanel::$dedirecords[$index]['Login'] == $record->login) {
                TimePanel::$dedirecords[$index] = Array("Login" => $record->login, "NickName" => $record->nickname, "Rank" => $record->place,
                    "Best" => $record->time, "Checks" => $record->checkpoints);
            }
        }
        $this->showPanel($record->login, false);
    }

    public function eXpOnUnload()
    {
        Dispatcher::unregister(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
        Dispatcher::unregister(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
        Dispatcher::unregister(LocalEvent::getClass(), $this);
        TimePanel::EraseAll();
        parent::eXpUnload();
    }
}

?>

