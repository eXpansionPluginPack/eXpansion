<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLive\PluginHandler\Dependency;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Widgets\LocalPanel;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Widgets\LocalPanel2;

class Widgets_LocalRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    public static $me           = null;
    public static $localrecords = array();
    public static $secondMap    = false;
    private $widgetIds          = array();
    public static $raceOn;
    public static $roundPoints;

    /** @var Config */
    private $config;
    private $panelSizeX = 42;

    public function exp_onInit()
    {
        $this->addDependency(new Dependency('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords'));
    }

    public function exp_onLoad()
    {
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_NEW_RECORD);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
        $this->config = Config::getInstance();
    }

    public function exp_onReady()
    {
        $this->enableDedicatedEvents();

        $this->lastUpdate   = time();
        if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords'))
                self::$localrecords = $this->callPublicMethod(
                "\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRecords"
            );
        $this->updateLocalPanel();
        self::$me           = $this;
    }

    public function updateLocalPanel($login = null)
    {
        $gui = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

        if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords')) {
            /** @var LocalPanel $localRecs */
            $localRecs = LocalPanel::GetAll();
            if ($login == null) {
                $panelMain = Gui\Widgets\LocalPanel::Create($login);
                $panelMain->setSizeX($this->panelSizeX);
                $panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
                if (!$this->config->isHorizontal) {
                    $panelMain->setDirection("left");
                    if ($this->storage->gameInfos->gameMode != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK)
                    {
                        $panelMain->setDirection("right");
                    }
                }
                $this->widgetIds["LocalPanel"] = $panelMain;
                $this->widgetIds["LocalPanel"]->update();
                $this->widgetIds["LocalPanel"]->show();
            } else if (isset($localRecs[0])) {
                $localRecs[0]->update();
                $localRecs[0]->show($login);
            }

            if (!$gui->disablePersonalHud) {
                $localRecs = LocalPanel2::GetAll();
                if ($login == null) {
                    $panelScore = Gui\Widgets\LocalPanel2::Create($login);
                    $panelScore->setSizeX($this->panelSizeX);
                    $panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
                    $panelScore->setVisibleLayer("scorestable");

                    $this->widgetIds["LocalPanel2"] = $panelScore;
                    $this->widgetIds["LocalPanel2"]->update();
                    $this->widgetIds["LocalPanel2"]->show();
                } else if (isset($localRecs[0])) {
                    $localRecs[0]->update();
                    $localRecs[0]->show($login);
                }
            }
        }
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getConfigInstance() instanceof Config) {
            Gui\Widgets\LocalPanel::EraseAll();
            $this->updateLocalPanel();
        }
    }

    public function showLocalPanel($login)
    {
        $this->updateLocalPanel($login);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {

        self::$raceOn    = false;
        $this->widgetIds = array();
        Gui\Widgets\LocalPanel::EraseAll();
        Gui\Widgets\LocalPanel2::EraseAll();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        if ($wasWarmUp) {
            self::$raceOn      = false;
            $this->forceUpdate = true;
            $this->updateLocalPanel();
            self::$secondMap   = true;
            self::$raceOn      = true;
        } else {
            self::$localrecords = array(); //  reset
            $this->widgetIds    = array();
            Gui\Widgets\LocalPanel::EraseAll();
            Gui\Widgets\LocalPanel2::EraseAll();
        }
    }
    /*
      public function onBeginMap($map, $warmUp, $matchContinuation)
      {
      self::$raceOn      = false;
      $this->forceUpdate = true;
      $this->widgetIds   = array();
      Gui\Widgets\LocalPanel::EraseAll();
      Gui\Widgets\LocalPanel2::EraseAll();
      $this->updateLocalPanel();
      self::$secondMap   = true;
      self::$raceOn      = true;
      }
     */

    public function onStatusChanged($statusCode, $statusName)
    {
        if ($statusCode == 4) {
            self::$raceOn      = false;
            $this->forceUpdate = true;
            $this->widgetIds   = array();
            Gui\Widgets\LocalPanel::EraseAll();
            Gui\Widgets\LocalPanel2::EraseAll();
            $this->updateLocalPanel();
            self::$secondMap   = true;
            self::$raceOn      = true;
        }
    }

    public function onEndRound()
    {

    }

    public function onRecordsLoaded($data)
    {
        self::$localrecords = $data;
        $this->local        = true;
        $this->needUpdate   = self::$localrecords;
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $this->showLocalPanel($login);
    }

    public function onNewRecord($data)
    {
        self::$localrecords = $data;
    }

    public function onUpdateRecords($data)
    {
        self::$localrecords = $data;
    }

    function exp_onUnload()
    {
        Gui\Widgets\LocalPanel::EraseAll();
        Gui\Widgets\LocalPanel2::EraseAll();
        Dispatcher::unregister(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
        Dispatcher::unregister(LocalEvent::getClass(), $this, LocalEvent::ON_NEW_RECORD);
        Dispatcher::unregister(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
    }
}
?>