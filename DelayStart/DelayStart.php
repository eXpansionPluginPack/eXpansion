<?php

namespace ManiaLivePlugins\eXpansion\DelayStart;

use ManiaLivePlugins\eXpansion\Core\Config as CoreConfig;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Helpers\TimeConversion;

class DelayStart extends ExpPlugin
{

    private $startTime = 0;
    private $tick = false;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();

        if (!$this->connection->manualFlowControlIsEnabled()) {
            $this->connection->setApiVersion("2011-10-06");
            $this->connection->manualFlowControlEnable(true);
        }

        $this->enableTickerEvent();
        //$this->registerChatCommand("go", "next");

    }

    public function onTick()
    {

        if ($this->tick) {
            $remain = (time() - $this->startTime  ) % 5;
            if ($remain == 0) {
                $time = round(TimeConversion::MStoTM(Config::GetInstance()->delay) / 1000) + ($this->startTime - time());

                if ($time != 0) {
                    $this->eXpChatSendServerMessage(eXpGetMessage("#player#Will start in #variable#%s#player# sec"), null, array(strval($time)));
                }
                else {
                    $this->eXpChatSendServerMessage(eXpGetMessage('#player# Start of match!'), null);
                }
            }
        }

        if ($this->tick && time() > $this->startTime + round(TimeConversion::MStoTM(Config::GetInstance()->delay) / 1000)) {
            $this->tick = false;
            $this->connection->manualFlowControlProceed();
        }
    }

    public function next()
    {
        $this->connection->manualFlowControlProceed();
    }

    public function onManualFlowControlTransition($transition)
    {
        switch ($transition) {
            case "Synchro -> Play":
                if (!$this->connection->getWarmUp()) {
                    $this->eXpChatSendServerMessage(eXpGetMessage("#player#Match start will be delayed #variable#%s"), null,array(Config::getInstance()->delay));
                    $this->startTime = time();
                    $this->tick = true;

                } else {
                    $this->connection->manualFlowControlProceed();
                }
                break;
            default:
                $this->connection->manualFlowControlProceed();
                break;
        }
    }


    public function eXpOnUnload()
    {
        $this->connection->manualFlowControlEnable(false);
        $this->connection->setApiVersion(CoreConfig::getInstance()->API_Version);
    }

    public function onTerminate()
    {
        $this->connection->setApiVersion(CoreConfig::getInstance()->API_Version);
    }


}