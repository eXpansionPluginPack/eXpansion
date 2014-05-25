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

    /** @var Config */
    private $config;

    public function exp_onInit() {
	$this->config = Config::getInstance();
	$this->mods = $this->getMods();
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();
	$this->forceMods();
	foreach ($this->storage->players as $login => $player)
	    $this->onPlayerConnect($login, false);
	foreach ($this->storage->spectators as $login => $player)
	    $this->onPlayerConnect($login, true);
    }
    
    private function forceMods() {
	try {
	    $this->console("Enabling forced mods");
	    $this->connection->setForcedMods(true, $this->mods);
	} catch (\Exception $e) {
	    $this->console("[eXp\\ForceMod] error while enabling the mod:" . $e->getMessage());
	    return;
	}
    }

    private function getMods() {
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
	    if (!is_array($this->config->mods)) {
		$this->config->mods = array($this->config->mods);
	    }
	    foreach ($this->config->mods as $entry) {
		if (empty($entry))
		    continue;
		$mod = new \Maniaplanet\DedicatedServer\Structures\Mod();
		$mod->url = $entry;
		$mod->env = $env;
		//$mod->env = $env->titleId;
		$mods[] = $mod;
	    }
	    return $mods;
	} catch (\Exception $e) {
	    return array();
	}
    }

    function exp_onUnload() {

    }

}
