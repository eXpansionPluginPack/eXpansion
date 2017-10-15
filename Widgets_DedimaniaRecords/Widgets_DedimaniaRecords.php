<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer;
use ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Widgets\DediPanel;

class Widgets_DedimaniaRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    const NONE = 0x0;
    const DEDIMANIA = 0x2;
    const DEDIMANIA_FORCE = 0x8;
    const All = 0x31;

    public static $me = null;
    public static $dedirecords = array();
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
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania')
            || $this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script')
        ) {
            Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
        }

        $this->config = Config::getInstance();
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();

        $this->lastUpdate = time();
        $this->enableTickerEvent();

        $this->updateDediPanel();
        self::$me = $this;
    }

    public function onTick()
    {
        if (($this->needUpdate & self::DEDIMANIA) == self::DEDIMANIA
            || $this->forceUpdate
            || ($this->needUpdate & self::DEDIMANIA_FORCE) == self::DEDIMANIA_FORCE
        ) {
            if ($this->dedi || $this->needUpdate == self::DEDIMANIA_FORCE) {
                $this->updateDediPanel();
                $this->dedi = false;
            }
        }

        $this->lastUpdate = time();
        $this->forceUpdate = false;
        $this->needUpdate = false;
    }

    public function updateDediPanel($login = null)
    {

        $dedi1 = '\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania';
        $dedi2 = '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script';

        try {
            if (($this->isPluginLoaded($dedi1) && $this->callPublicMethod($dedi1, 'isRunning'))
                || ($this->isPluginLoaded($dedi2) && $this->callPublicMethod($dedi2, 'isRunning'))
            ) {
                $localRecs = DediPanel::GetAll();
                if (!isset($localRecs[0])) {
                    $panelMain = Gui\Widgets\DediPanel::Create($login);
                    $panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
                    $panelMain->setSizeX($this->panelSizeX);
                    if (!$this->config->isHorizontal) {
                        $panelMain->setDirection("right");
                    }
                    $this->widgetIds["DediPanel"] = $panelMain;
                    $this->widgetIds["DediPanel"]->update();
                    $this->widgetIds["DediPanel"]->show();
                } elseif (isset($localRecs[0])) {
                    $localRecs[0]->update();
                    $localRecs[0]->show($login);
                }
            }
        } catch (\Exception $ex) {

        }
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getConfigInstance() instanceof Config) {
            Gui\Widgets\DediPanel::EraseAll();
            $this->updateDediPanel();
        }
    }

    public function showDediPanel($login)
    {
        $this->updateDediPanel($login);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {

        self::$raceOn = false;
        $this->widgetIds = array();
        Gui\Widgets\DediPanel::EraseAll();

    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        if ($restartMap) {
            self::$secondMap = true;
        }
        if ($wasWarmUp) {
            self::$raceOn = false;
            $this->forceUpdate = true;
            $this->updateDediPanel();
            self::$secondMap = true;
            self::$raceOn = true;
        } else {
            self::$secondMap = false;
            self::$dedirecords = array(); // reset
            $this->widgetIds = array();
            Gui\Widgets\DediPanel::EraseAll();

        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        self::$raceOn = false;
        $this->forceUpdate = true;
        $this->widgetIds = array();
        Gui\Widgets\DediPanel::EraseAll();

        $this->updateDediPanel();
        self::$secondMap = true;
        self::$raceOn = true;
    }

    public function onBeginMatch()
    {
        if (self::$raceOn == true) {
            return;
        }

        self::$raceOn = false;
        $this->forceUpdate = true;
        $this->widgetIds = array();
        Gui\Widgets\DediPanel::EraseAll();

        $this->updateDediPanel();
        self::$secondMap = true;
        self::$raceOn = true;
    }

    public function onEndRound()
    {

    }

    public function onDedimaniaGetRecords($data)
    {
        self::$dedirecords = $data['Records'];
        $this->dedi = true;
        $this->needUpdate = self::DEDIMANIA_FORCE;
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $this->showDediPanel($login);
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        Gui\Widgets\DediPanel::Erase($login);

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

    public function eXpOnUnload()
    {
        Gui\Widgets\DediPanel::EraseAll();
        Dispatcher::unregister(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
    }
}
