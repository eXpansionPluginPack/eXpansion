<?php

namespace ManiaLivePlugins\eXpansion\Gui;

class Gui extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onInit() {
        $this->setVersion("0.1");
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
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
        $this->memory();
    }

    function onPlayerDisconnect($login) {
        $this->memory();
    }

    function memory() {
        $mem = "Memory Usage: " . round(memory_get_usage() / 1024) . "Mb";
        \ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
        print "\n" . $mem . "\n";
    }

}

?>