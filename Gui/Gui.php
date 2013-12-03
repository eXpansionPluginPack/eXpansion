<?php

namespace ManiaLivePlugins\eXpansion\Gui;

use ManiaLivePlugins\eXpansion\Gui\Widgets\HudPanel;

class Gui extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $titleId, $msg_params;

    public function exp_onInit() {
	$this->setVersion("0.1");
    }

    public function exp_onLoad() {
	HudPanel::$mainPlugin = $this;

	$version = $this->connection->getVersion();
	$this->titleId = $version->titleId;

	$SMstorm = array("SMStorm", "SMStormCombo@nadeolabs", "SMStormRoyal@nadeolabs", "SMStormElite@nadeolabs", "SMStormJoust@nadeolabs");
	if (in_array($this->titleId, $SMstorm)) {
	    $settings = array("S_UseScriptCallbacks" => true);
	    $this->connection->setModeScriptSettings($settings);
	}
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();

	$this->registerChatCommand("hud", "showConfigWindow", 0, true);
	$this->registerChatCommand("hud", "hudCommands", 1, true);


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
	$preloader = Widgets\Preloader::Create($login);
	foreach (Config::getInstance() as $property => $value) {
	    if (is_string($value) && substr($value, 0, 7) == "http://" && substr($value, -4) == ".png") {
		$preloader->add($value);
	    }
	}
	$preloader->show();

	$info = HudPanel::Create($login);
	$info->setSize(60, 20);
	$info->setPosition(-160, -40);
	$info->show();

	try {
	    $SMstorm = array("SMStorm", "SMStormCombo@nadeolabs", "SMStormRoyal@nadeolabs", "SMStormElite@nadeolabs", "SMStormJoust@nadeolabs");
	    if (in_array($this->titleId, $SMstorm)) {
		$this->connection->TriggerModeScriptEvent("LibXmlRpc_DisableAltMenu", $login);
	    }
	} catch (\Exception $e) {
	    echo "error: " . $e->getMessage();
	}
    }

    function onPlayerDisconnect($login, $reason = null) {
	
    }

    function hudCommands($login, $param) {
	switch ($param) {
	    case "reset":
		$this->resetHud($login);
		break;
	    case "move":
		$this->enableHudMove($login);
		break;
	    case "freeze":
		$this->disableHudMove($login);
		break;
	    default:
		$this->exp_chatSendServerMessage($this->msg_params, $login);
		break;
	}
    }

    function enableHudMove($login) {
	$window = Windows\HudMove::Create($login, false);
	$window->enable();
	$window->show();
    }

    function disableHudMove($login) {
	$window = Windows\HudMove::Create($login, false);
	$window->disable();
	$window->show();
    }

    function showConfigWindow($login, $entries) {
	$window = Windows\Configuration::Create($login, true);
	$window->setSize(120, 90);
	$window->setData($entries);
	$window->show();
    }

    function resetHud($login) {
	$window = Windows\ResetHud::Create($login, false);
	$window->show();
	$this->exp_chatSendServerMessage(exp_getMessage("Hud positions reset! Please reconnect to server :)"), $login);
    }

    function logMemory() {
	$mem = "Memory Usage: " . round(memory_get_usage() / 1024) . "Kb";
	\ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
	print "\n" . $mem . "\n";
    }

}

?>