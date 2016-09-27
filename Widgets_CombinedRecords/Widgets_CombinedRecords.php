<?php

namespace ManiaLivePlugins\eXpansion\Widgets_CombinedRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use ManiaLivePlugins\eXpansion\Widgets_CombinedRecords\Gui\Widgets\CombiPanel;
use ManiaLivePlugins\eXpansion\Widgets_CombinedRecords\Gui\Widgets\CombiPanel2;

class Widgets_CombinedRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    const NONE = 0x0;
    const DEDIMANIA = 0x2;
    const DEDIMANIA_FORCE = 0x8;
    const All = 0x31;

    public static $me = null;
    public static $dedirecords = array();
    public static $localrecords = array();

    public static $secondMap = false;
    private $lastUpdate;
    private $forceUpdate = false;
    private $needUpdate = false;
    private $dedi = true;
    private $widgetIds = array();
    public static $raceOn;
    public static $roundPoints;

    /** @var Config */
    private $config;
    private $panelSizeX = 42;

    public function eXpOnLoad()
    {
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania') || $this->isPluginLoaded(
                '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script'
            )
        ) {
            Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
        }

        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_NEW_RECORD);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);

        $this->config = Config::getInstance();
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();

        $this->lastUpdate = time();
        $this->enableTickerEvent();

        if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords'))
            self::$localrecords = $this->callPublicMethod(
                "\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRecords"
            );

        $this->updateCombiPanel();
        self::$me = $this;
    }

    public function onTick()
    {

        if ((time() - $this->lastUpdate) > 20) {

            if (($this->needUpdate & self::DEDIMANIA) == self::DEDIMANIA || $this->forceUpdate || ($this->needUpdate & self::DEDIMANIA_FORCE)
                == self::DEDIMANIA_FORCE
            ) {
                if ($this->dedi || $this->needUpdate == self::DEDIMANIA_FORCE) {
                    $this->updateCombiPanel();
                    $this->dedi = false;
                }
            }

            $this->lastUpdate = time();
            $this->forceUpdate = false;
            $this->needUpdate = false;
        }
    }

    public function updateCombiPanel($login = null)
    {

        $dedi1 = '\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania';
        $dedi2 = '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script';
        $gui = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

        try {
            if (($this->isPluginLoaded($dedi1) && $this->callPublicMethod(
                        $dedi1, 'isRunning'
                    )) || ($this->isPluginLoaded($dedi2) && $this->callPublicMethod($dedi2, 'isRunning'))
            ) {
                $localRecs = CombiPanel::GetAll();
                if (!isset($localRecs[0])) {
                    //Gui\Widgets\CombiPanel::EraseAll();
                    $panelMain = Gui\Widgets\CombiPanel::Create($login);
                    $panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
                    $panelMain->setSizeX($this->panelSizeX);
                    if (!$this->config->isHorizontal) {
                        $panelMain->setDirection("right");
                    }
                    $this->widgetIds["CombiPanel"] = $panelMain;
                    $this->widgetIds["CombiPanel"]->setNbFields($this->config->nbTotal);
                    $this->widgetIds["CombiPanel"]->setNbFirstFields($this->config->nbTop);
                    $this->widgetIds["CombiPanel"]->update();
                    $this->widgetIds["CombiPanel"]->show();
                } else if (isset($localRecs[0])) {
                    $localRecs[0]->setNbFields($this->config->nbTotal);
                    $localRecs[0]->setNbFirstFields($this->config->nbTop);
                    $localRecs[0]->update();
                    $localRecs[0]->show($login);
                }
                if (!$gui->disablePersonalHud) {
                    $localRecs = CombiPanel2::GetAll();
                    if (!isset($localRecs[0])) {
                        //Gui\Widgets\CombiPanel2::EraseAll();
                        $panelScore = Gui\Widgets\CombiPanel2::Create($login);
                        $panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
                        $panelScore->setVisibleLayer("scorestable");
                        $panelScore->setSizeX($this->panelSizeX);
                        $this->widgetIds["CombiPanel2"] = $panelScore;
                        $this->widgetIds["CombiPanel2"]->setNbFields($this->config->nbTotal);
                        $this->widgetIds["CombiPanel2"]->setNbFirstFields($this->config->nbTop);
                        $this->widgetIds["CombiPanel2"]->update();
                        $this->widgetIds["CombiPanel2"]->show();
                    } else if (isset($localRecs[0])) {
                        $localRecs[0]->setNbFields($this->config->nbTotal);
                        $localRecs[0]->setNbFirstFields($this->config->nbTop);
                        $localRecs[0]->update();
                        $localRecs[0]->show($login);
                    }
                }
            }
        } catch (\Exception $ex) {

        }
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getConfigInstance() instanceof Config) {
            if ($var->getName() == "isHorizontal") {
                Gui\Widgets\CombiPanel::EraseAll();
                $this->updateCombiPanel();
            }
        }
    }

    public function showCombiPanel($login)
    {
        $this->updateCombiPanel($login);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {

        self::$raceOn = false;
        $this->widgetIds = array();
        Gui\Widgets\CombiPanel::EraseAll();
        Gui\Widgets\CombiPanel2::EraseAll();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        if ($restartMap) {
            self::$secondMap = true;
        }
        if ($wasWarmUp) {
            self::$raceOn = false;
            $this->forceUpdate = true;
            $this->updateCombiPanel();
            self::$secondMap = true;
            self::$raceOn = true;
        } else {
            self::$secondMap = false;
            self::$dedirecords = array(); // reset
            $this->widgetIds = array();
            Gui\Widgets\CombiPanel::EraseAll();
            Gui\Widgets\CombiPanel2::EraseAll();
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        self::$raceOn = false;
        $this->forceUpdate = true;
        $this->widgetIds = array();
        Gui\Widgets\CombiPanel::EraseAll();
        Gui\Widgets\CombiPanel2::EraseAll();
        $this->updateCombiPanel();
        self::$secondMap = true;
        self::$raceOn = true;
    }

    public function onBeginMatch()
    {
        if (self::$raceOn == true) return;

        self::$raceOn = false;
        $this->forceUpdate = true;
        $this->widgetIds = array();
        Gui\Widgets\CombiPanel::EraseAll();
        Gui\Widgets\CombiPanel2::EraseAll();
        $this->updateCombiPanel();
        self::$secondMap = true;
        self::$raceOn = true;
    }


    public function onDedimaniaGetRecords($data)
    {
        self::$dedirecords = $data['Records'];
        $this->dedi = true;
        $this->needUpdate = self::DEDIMANIA_FORCE;
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $this->showCombiPanel($login);
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        Gui\Widgets\CombiPanel::Erase($login);
        Gui\Widgets\CombiPanel2::Erase($login);
    }

    public function onDedimaniaOpenSession()
    {

    }

    public function onDedimaniaUpdateRecords($data)
    {

    }

    public function onDedimaniaNewRecord($data)
    {

    }

    /**
     * @param $data DediPlayer
     */
    public function onDedimaniaPlayerConnect($data)
    {
        if ($data->maxRank > Connection::$serverMaxRank) {
            $this->needUpdate = self::DEDIMANIA_FORCE;
        }
    }

    public function onDedimaniaPlayerDisconnect()
    {

    }

    public function onDedimaniaRecord($record, $oldrecord)
    {

    }

    public function onRecordsLoaded($data)
    {
        self::$localrecords = $data;
    }

    public function onNewRecord($data)
    {
        self::$localrecords = $data;
    }

    public function onUpdateRecords($data)
    {
        self::$localrecords = $data;
    }

    public function eXpOnUnload()
    {
        Gui\Widgets\CombiPanel::EraseAll();
        Gui\Widgets\CombiPanel2::EraseAll();

        Dispatcher::unregister(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
    }
}