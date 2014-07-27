<?php

namespace ManiaLivePlugins\eXpansion\ForceMod;

use Exception;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\ForceMod\Config;
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
	
	public function exp_onReady()
	{
		$this->enableDedicatedEvents();
		$this->forceMods();
	}

	private function forceMods()
	{
		try {
			$mods = $this->getMods();
			if (count($mods) > 0) {
				$index = mt_rand(0, (count($mods) - 1));
				if (array_key_exists($index, $mods)) {
					$rnd_mod = $mods[$index];
					$this->console("Enabling forced mod at url: " . $rnd_mod->url);
				}
				else {
					$this->console("Enabling forced mods!");
					$rnd_mod = $mods;
				}
			}
			else {
				$this->console("Force mods disabled, since there is no mods defined in config");
				$rnd_mod = array();
			}
			$this->connection->setForcedMods(true, $rnd_mod);
		} catch (Exception $e) {
			$this->console("[eXp\\ForceMod] error while enabling the mod:" . $e->getMessage());
			return;
		}
	}

	private function getMods()
	{
		$this->config = Config::getInstance();
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
				$mod = new Mod();
				$mod->url = $entry;
				$mod->env = $env;
				//$mod->env = $env->titleId;
				$mods[] = $mod;
			}
			return $mods;
		} catch (Exception $e) {
			return array();
		}
	}

	public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
	{
		$this->forceMods();
	}

	public function exp_onUnload()
	{
		try {
			$this->console("Disabling forced mods");
			$this->connection->setForcedMods(true, array());
		} catch (Exception $e) {
			$this->console("[eXp\\ForceMod] error while disabling the mods:" . $e->getMessage());
			return;
		}
	}

}
