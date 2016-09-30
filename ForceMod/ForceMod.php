<?php

namespace ManiaLivePlugins\eXpansion\ForceMod;

use Exception;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use Maniaplanet\DedicatedServer\Structures\Mod;

/**
 * ForceMod
 * A plugin to enable custom graphics to be forced on server
 *
 *  * @author Reaby
 */
class ForceMod extends ExpPlugin
{

    /** @var Config */
    private $config;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->forceMods();
    }

    private function forceMods()
    {
        try {
            $rnd_mod = array();
            $mods = $this->getMods();
            if ($this->expStorage->version->titleId == "Trackmania_2@nadeolabs") {

                foreach ($mods as $env => $mod) {
                    $index = mt_rand(0, (count($mod) - 1));
                    if (array_key_exists($index, $mod)) {
                        $rnd_mod[] = $mod[$index];
                    }
                }
            } else {
                $env = $this->fixEnv($this->expStorage->version->titleId);
                if (array_key_exists($env, $mods)) {
                    $mods = $mods[$env];
                    if (count($mods) > 0) {
                        $index = mt_rand(0, (count($mods) - 1));
                        if (array_key_exists($index, $mods)) {
                            $rnd_mod[] = $mods[$index];
                            $this->console("Enabling forced mod at url: " . $rnd_mod[0]->url);
                        }
                    }
                }
            }

            foreach ($rnd_mod as $key => $mod) {
                $rnd_mod[$key] = $this->fixEnv($mod);
            }

            if (empty($rnd_mod)) {
                $this->console("Force mods disabled, since there is no mods defined in config");
            }

            Helper::logDebug("============ forcemod DEBUG info =============\n", array('eXpansion', 'ForceMod'));
            Helper::logDebug(print_r($rnd_mod, true), array('eXpansion', 'ForceMod'));
            Helper::logDebug("=========================\n", array('eXpansion', 'ForceMod'));

            $this->connection->setForcedMods(true, $rnd_mod);
        } catch (Exception $e) {
            $this->console(
                "[eXp\\ForceMod] error while enabling the mod:" . $e->getMessage() . " line:" . $e->getLine()
            );

            return;
        }
    }

    private function getMods()
    {
        $this->config = Config::getInstance();

        try {
            $mods = array();
            if (!is_array($this->config->mods)) {
                $this->config->mods = array($this->config->mods);
            }
            foreach ($this->config->mods as $url => $envString) {
                $env = $envString;
                if (empty($envString)) {
                    $env = $this->expStorage->version->titleId;
                }

                $mod = new Mod();
                $mod->url = $url;
                $mod->env = $env;
                $mods[$env][] = $mod;
            }

            return $mods;
        } catch (Exception $e) {
            \ManiaLivePlugins\eXpansion\Helpers\Helper::logError("Error while forcemod:" . $e->getMessage());

            return array();
        }
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {

    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        $this->forceMods();
    }

    public function eXpOnUnload()
    {
        try {
            $this->console("Disabling forced mods");
            $this->connection->setForcedMods(true, array());
        } catch (Exception $e) {
            $this->console("[eXp\\ForceMod] error while disabling the mods:" . $e->getMessage());

            return;
        }
    }

    private function fixEnv($env)
    {
        switch ($env) {
            case "TMStadium":
                return "Stadium";
                break;
            case "TMValley":
                return "Valley";
                break;
            case "TMCanyon":
                return "Canyon";
                break;
        }

        return $env;
    }
}
