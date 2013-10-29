<?php

namespace ManiaLivePlugins\eXpansion\ForceMod;

use ManiaLive\Utilities\Console;

/**
 * ForceMod
 * A plugin to enable custom graphics to be forced on server
 *
 *  * @author Reaby
 */
class ForceMod extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $mods = array();

    public function exp_onInit() {
        if (!file_exists("config/config-eXp-forcemods.ini")) {
            $this->writeConfig();
        }

        $this->mods = $this->getConfig();
        $this->setPublicMethod("showOptions");
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        $this->forceMods();
    }

    public function showOptions($login) {
        
    }

    private function forceMods() {
        try {
            Console::println("Enabling forced mods");
            $this->connection->setForcedMods(true, $this->mods);
        } catch (\Exception $e) {
            Console::println("[eXp\ForceMod] error while enabling the mod:" . $e->getMessage());
            return;
        }
    }

    private function getConfig() {
        $version = $this->connection->getVersion();
        $env = "";
        switch ($version->titleId) {
            case "TMStadium":
                $env = "Stadium";
                break;
            case "TMValley":
                $env = "Valley";
                break;
            case "TMCanyon":
                $env = "Canyon";
                break;
        }

        try {
            $mods = array();
            $values = \parse_ini_file("config/config-eXp-forcemods.ini", true);
            if (array_key_exists("mod", $values)) {
                foreach ($values['mod'] as $entry) {
                    if (empty($entry))
                        continue;
                    $mod = new \DedicatedApi\Structures\Mod();
                    $mod->url = $entry;
                    $mod->env = $env;
                    //$mod->env = $env->titleId;
                    $mods[] = $mod;
                }
            }
            return $mods;
        } catch (\Exception $e) {

            Console::println("[eXp\ForceMod] error reading: config/config-eXp-forcemods.ini");
            return array();
        }
    }

    private function writeConfig() {
        $buffer = ";mod[] = 'http://somewhere.com/directory/mod_name.zip' \r\n;\r\n";
        foreach ($this->mods as $mod) {
            $buffer .="mod[]='" . $mod->url . "'\n";
        }
        try {
            file_put_contents("config/config-eXp-forcemods.ini", $buffer);
        } catch (\Exception $e) {
            Console::println("[eXp\ForceMod] error writing: config/config-eXp-forcemods.ini -->" . $e->getMessage());
        }
    }

}
