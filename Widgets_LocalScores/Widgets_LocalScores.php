<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalScores;

use ManiaLive\Event\Dispatcher;
use ManiaLive\PluginHandler\Dependency;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Listener;
use ManiaLivePlugins\eXpansion\Widgets_LocalScores\Gui\Widgets\LocalPanel;

class Widgets_LocalScores extends ExpPlugin implements Listener
{

    public static $localrecords = array();

    /** @var Config */
    private $config;

    public function expOnInit()
    {
        $this->addDependency(new Dependency('\\ManiaLivePlugins\\eXpansion\\SM_PlatformScores\\SM_PlatformScores'));
    }

    public function eXpOnLoad()
    {
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();

        $this->lastUpdate = time();
        if ($this->isPluginLoaded('\\ManiaLivePlugins\\eXpansion\\SM_PlatformScores\\SM_PlatformScores'))
            self::$localrecords = $this->callPublicMethod(
                "\\ManiaLivePlugins\\eXpansion\\SM_PlatformScores\\SM_PlatformScores", "getRecords"
            );

        $this->updateLocalPanel();
    }

    public function updateLocalPanel($login = null)
    {
        $widget = LocalPanel::Create($login);
        $widget->setPosition(114, 64);
        $widget->update();
        $widget->show();
    }

    public function showLocalPanel($login)
    {
        $this->updateLocalPanel($login);
    }

    public function onBeginMatch()
    {
        $this->updateLocalPanel();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        LocalPanel::EraseAll();
    }

    public function onRecordsLoaded($records)
    {
        self::$localrecords = $records;
        $this->updateLocalPanel();
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $this->showLocalPanel($login);
    }

    public function onPlayerDisconnect($login, $reason = null)
    {

    }

    public function onUpdateRecords($data)
    {
        self::$localrecords = $data;
        $this->updateLocalPanel();
    }

    public function eXpOnUnload()
    {
        Dispatcher::unregister(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
        Dispatcher::unregister(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
        LocalPanel::EraseAll();
    }

    public function onPersonalBestRecord($record)
    {

    }

    public function onRecordPlayerFinished($record)
    {

    }

    public function onNewRecord($record, $oldRecord)
    {

    }

}