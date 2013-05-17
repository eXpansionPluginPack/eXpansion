<?php

namespace ManiaLivePlugins\eXpansion\Gui;

class Gui extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onInit() {
        $this->setVersion("0.1");
    }

    public function exp_onLoad() {
           $settings = array("S_UseScriptCallbacks" => true);
        $this->connection->setModeScriptSettings($settings);

    }
    public function exp_onReady() {
        $this->enableDedicatedEvents();
        
     
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    public static function getScaledSize($sizes, $totalSize) {
        $nsize = array();

        $total = 0;
        foreach ($sizes as $val) {
            $total += $val;
        }

        $coff = $totalSize / $total;

        foreach ($sizes as $val) {
            $nsize[] = $val * $coff;
        }
        return $nsize;
    }

    function onPlayerConnect($login, $isSpectator) {
        try {
            var_dump($this->connection->TriggerModeScriptEvent("LibXmlRpc_DisableAltMenu", $login));
        } catch (\Exception $e) {
            echo "error: ". $e->getMessage();
        }
    }

    function onPlayerDisconnect($login, $reason = null) {
        
    }

    function memory() {
        $mem = "Memory Usage: " . round(memory_get_usage() / 1024) . "Mb";
        \ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
        print "\n" . $mem . "\n";
    }

}

?>