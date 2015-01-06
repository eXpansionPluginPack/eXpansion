<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection;
use ManiaLivePlugins\eXpansion\Dedimania\DedimaniaAbstract;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer;
use ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Widgets\DediPanel;
use ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Widgets\DediPanel2;

class Widgets_DedimaniaRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    const None            = 0x0;
    const Dedimania       = 0x2;
    const Dedimania_force = 0x8;
    const All             = 0x31;

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
    public function exp_onLoad()
    {
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania') || $this->isPluginLoaded(
                '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script'
            )
        )
            Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);

        $this->config = Config::getInstance();
    }

    public function exp_onReady()
    {
        $this->enableDedicatedEvents();

        $this->lastUpdate = time();
        $this->enableTickerEvent();

        $this->updateDediPanel();
        self::$me = $this;
    }

    public function onTick()
    {

        if ((time() - $this->lastUpdate) > 20) {

            if (($this->needUpdate & self::Dedimania) == self::Dedimania || $this->forceUpdate || ($this->needUpdate & self::Dedimania_force) == self::Dedimania_force) {
                if ($this->dedi || $this->needUpdate == self::Dedimania_force) {
                    $this->updateDediPanel();
                    $this->dedi = false;
                }
            }

            $this->lastUpdate  = time();
            $this->forceUpdate = false;
            $this->needUpdate  = false;
        }
    }

    public function updateDediPanel($login = null)
    {

        $dedi1 = '\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania';
        $dedi2 = '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script';

        try {
            if (($this->isPluginLoaded($dedi1) && $this->callPublicMethod(
                        $dedi1,
                        'isRunning'
                    )) || ($this->isPluginLoaded($dedi2) && $this->callPublicMethod($dedi2, 'isRunning'))
            ) {
                $localRecs = DediPanel::GetAll();
                if (!isset($localRecs[0])) {
                    //Gui\Widgets\DediPanel::EraseAll();
                    $panelMain = Gui\Widgets\DediPanel::Create($login);
                    $panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
                    $panelMain->setSizeX($this->panelSizeX);
                    $this->widgetIds["DediPanel"] = $panelMain;
                    $this->widgetIds["DediPanel"]->update();
                    $this->widgetIds["DediPanel"]->show();
                } else if (isset($localRecs[0])) {
                    $localRecs[0]->update();
                    $localRecs[0]->show($login);
                }

                $localRecs = DediPanel2::GetAll();
                if (!isset($localRecs[0])) {
                    //Gui\Widgets\DediPanel2::EraseAll();
                    $panelScore = Gui\Widgets\DediPanel2::Create($login);
                    $panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
                    $panelScore->setVisibleLayer("scorestable");
                    $panelScore->setSizeX($this->panelSizeX);
                    $this->widgetIds["DediPanel2"] = $panelScore;
                    $this->widgetIds["DediPanel2"]->update();
                    $this->widgetIds["DediPanel2"]->show();
                } else if (isset($localRecs[0])) {
                    $localRecs[0]->update();
                    $localRecs[0]->show($login);
                }
            }
        } catch (\Exception $ex) {

        }
    }

    public function showDediPanel($login)
    {
        $this->updateDediPanel($login);
    }


    public function onEndMatch($rankings, $winnerTeamOrMap)
    {

        self::$raceOn    = false;
        $this->widgetIds = array();
        Gui\Widgets\DediPanel::EraseAll();
        Gui\Widgets\DediPanel2::EraseAll();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        if ($wasWarmUp) {
            self::$raceOn      = false;
            $this->forceUpdate = true;
            $this->updateDediPanel();
            self::$secondMap = true;
            self::$raceOn    = true;
        } else {
            self::$dedirecords = array(); // reset
            $this->widgetIds   = array();
            Gui\Widgets\DediPanel::EraseAll();
            Gui\Widgets\DediPanel2::EraseAll();
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        self::$raceOn      = false;
        $this->forceUpdate = true;
        $this->widgetIds   = array();
        Gui\Widgets\DediPanel::EraseAll();
        Gui\Widgets\DediPanel2::EraseAll();
        $this->updateDediPanel();
        self::$secondMap = true;
        self::$raceOn    = true;
    }

    public function onBeginMatch()
    {
        self::$raceOn      = false;
        $this->forceUpdate = true;
        $this->widgetIds   = array();
        Gui\Widgets\DediPanel::EraseAll();
        Gui\Widgets\DediPanel2::EraseAll();
        $this->updateDediPanel();
        self::$secondMap = true;
        self::$raceOn    = true;
    }

    public function onEndRound()
    {
    }

    public function onDedimaniaGetRecords($data)
    {
        self::$dedirecords = $data['Records'];
        $this->dedi        = true;
        $this->needUpdate  = self::Dedimania_force;
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $this->showDediPanel($login);
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        Gui\Widgets\DediPanel::Erase($login);
        Gui\Widgets\DediPanel2::Erase($login);
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
          $this->needUpdate = self::Dedimania_force;
        }
    }

    public function onDedimaniaPlayerDisconnect()
    {

    }

    public function onDedimaniaRecord($record, $oldrecord)
    {

    }

    function exp_onUnload()
    {
        Gui\Widgets\DediPanel::EraseAll();
        Gui\Widgets\DediPanel2::EraseAll();

        Dispatcher::unregister(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
    }

}

?>