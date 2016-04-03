<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

class ScriptmodeEvent extends \ManiaLive\Event\Event
{
    /*
     *  when in need to add more suppport for events, just add constant missing event constant here and implement method at the interface :)
     */

    const LibXmlRpc_BeginMatch = 1;

    const LibXmlRpc_LoadingMap = 2;

    const LibXmlRpc_BeginMap = 3;

    const LibXmlRpc_BeginSubmatch = 4;

    const LibXmlRpc_BeginRound = 5;

    const LibXmlRpc_BeginTurn = 6;

    const LibXmlRpc_EndTurn = 7;

    const LibXmlRpc_EndRound = 8;

    const LibXmlRpc_EndSubmatch = 9;

    const LibXmlRpc_EndMap = 10;

    const LibXmlRpc_EndMatch = 11;

    const LibXmlRpc_BeginWarmUp = 12;

    const LibXmlRpc_EndWarmUp = 13;

    /* storm common */

    const LibXmlRpc_Rankings = 14;

    const LibXmlRpc_Scores = 15;

    const LibXmlRpc_PlayerRanking = 16;

    const WarmUp_Status = 17;

    const LibAFK_IsAFK = 18;

    const LibAFK_Properties = 19;

    /* tm common */

    const LibXmlRpc_OnStartLine = 20;

    const LibXmlRpc_OnWayPoint = 21;

    const LibXmlRpc_OnGiveUp = 22;

    const LibXmlRpc_OnRespawn = 23;

    const LibXmlRpc_OnStunt = 24;

    /* more events */

    const LibXmlRpc_OnCapture = 25;

    const LibXmlRpc_BeginPlaying = 26;

    const LibXmlRpc_EndPlaying = 27;

    const LibXmlRpc_UnloadingMap = 28;

    const LibXmlRpc_BeginPodium = 29;

    const LibXmlRpc_EndPodium = 30;

    const LibXmlRpc_OnStartCountdown = 31;

    /* import from scriptmode */

    const LibXmlRpc_Callbacks = 32;

    const LibXmlRpc_CallbackHelp = 33;

    const LibXmlRpc_BlockedCallbacks = 34;

    const LibXmlRpc_BeginServer = 35;

    const LibXmlRpc_BeginServerStop = 36;

    const LibXmlRpc_BeginMatchStop = 37;

    const LibXmlRpc_BeginMapStop = 38;

    const LibXmlRpc_BeginSubmatchStop = 39;

    const LibXmlRpc_BeginRoundStop = 40;

    const LibXmlRpc_BeginTurnStop = 41;

    const LibXmlRpc_EndTurnStop = 42;

    const LibXmlRpc_EndRoundStop = 43;

    const LibXmlRpc_EndSubmatchStop = 44;

    const LibXmlRpc_EndMapStop = 45;

    const LibXmlRpc_EndMatchStop = 46;

    const LibXmlRpc_EndServer = 47;

    const LibXmlRpc_EndServerStop = 48;

    const LibXmlRpc_PlayersRanking = 49;

    const LibXmlRpc_PlayersScores = 50;

    const LibXmlRpc_PlayersTimes = 51;

    const LibXmlRpc_TeamsScores = 52;

    const LibXmlRpc_WarmUp = 53;

    const LibXmlRpc_TeamsMode = 54;

    const UI_Properties = 55;

    protected $params;

    protected static $const = array();

    function __construct($onWhat)
    {
        parent::__construct($onWhat);
        $params = func_get_args();
        array_shift($params);
        $this->params = $params;

        if (empty(self::$const)) {
            $rc = new \ReflectionClass($this);

            foreach ($rc->getConstants() as $key => $value) {
                self::$const[intval($value)] = strval($key);
            }
        }
    }

    function fixBooleans(&$array)
    {
        foreach ($array as $key => $value) {
            if ($value == "True")
                $array[$key] = true;
            if ($value == "False")
                $array[$key] = false;
        }
    }

    function fireDo($listener)
    {
        $p = $this->params;
        $params = $p[0];
        $this->fixBooleans($params);
        if (method_exists($listener, self::$const[$this->onWhat])) {
            call_user_func_array(array($listener, self::$const[$this->onWhat]), $params);
        }
    }

}
