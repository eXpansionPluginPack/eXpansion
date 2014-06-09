<?php

namespace ManiaLivePlugins\eXpansion\Gui;

use ManiaLivePlugins\eXpansion\Gui\Widgets\HudPanel;
use ManiaLivePlugins\eXpansion\Gui\Widgets as WConfig;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class Gui extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $titleId;
    private $msg_params, $msg_disabled;
    private $resetLogins = array();
    private $counter = 0;

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
	$this->enableTickerEvent();
	$this->registerChatCommand("hud", "hudCommands", 0, true);
	$this->registerChatCommand("hud", "hudCommands", 1, true);
	$this->setPublicMethod("hudCommands");
	$this->setPublicMethod("showConfigWindow");

	$this->msg_params = exp_getMessage("possible parameters: move, lock, reset");
	$this->msg_disabled = exp_getMessage("#error#Server Admin has disabled personal huds. Sorry!");

	$preloader = Widgets\Preloader::Create(null);
	foreach (Config::getInstance() as $property => $value) {
	    if (is_string($value) && substr($value, 0, 7) == "http://" && substr($value, -4) == ".png") {
//if (is_string($value) && substr($value, 0, 7) == "http://") {
		$preloader->add($value);
	    }
	}
	$preloader->show();

	foreach ($this->storage->players as $player) {
	    $this->onPlayerConnect($player->login, false);
	}
	foreach ($this->storage->spectators as $player) {
	    $this->onPlayerConnect($player->login, true);
	}

	$this->loadWidgetConfigs();
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

    public function loadWidgetConfigs() {
	$config = WConfig::getInstance();
	foreach ($config as $confName => $values) {

	    $confs = explode('_', $confName);
	    if (sizeof($confs) > 1) {

		$widgetName = str_replace(" ", "", $confs[0]);
		Widget::setParameter($widgetName, $confs[1], $values);
	    }
	}
    }

    public function onTick() {
	if (count($this->resetLogins) > 0) {
	    /** @var \ManiaLive\Gui\GuiHandler */
	    $guiHandler = \ManiaLive\Gui\GuiHandler::getInstance();
	    foreach ($this->resetLogins as $login => $value) {
// delayed tick
		$this->resetLogins[$login] ++;
		switch ($this->resetLogins[$login]) {
		    case 1:
			Windows\ResetHud::Erase($login);
			break;
		    case 2:
			$guiHandler->toggleGui($login);
			break;
		    case 3:
			$guiHandler->toggleGui($login);
			unset($this->resetLogins[$login]);
			$this->exp_chatSendServerMessage(exp_getMessage("Hud reset done!"), $login);
			break;
		}
	    }
	}
	if ($this->counter != 0 && time() - $this->counter > 2) {
	    $this->connection->sendDisplayManialinkPage(null, "<manialinks><manialink id=\"0\"><quad></quad></manialink><custom_ui><altmenu_scores visible=\"false\" /></custom_ui></manialinks>", 0, false);
	    echo "GOGO";
	    $this->counter = 0;
	}
    }

    function onPlayerConnect($login, $isSpectator) {
// remove f8 for hiding ui
// 	\ManiaLive\Gui\Windows\Shortkey::Erase($login);


	/* reaby disabled this, no need anymore :)
	  $info = HudPanel::Create($login);
	  $info->setSize(75, 6);
	  $info->setPosition(-160, -50);
	  $info->show(); */

	try {
	    $SMstorm = array("SMStorm", "SMStormCombo@nadeolabs", "SMStormRoyal@nadeolabs", "SMStormElite@nadeolabs", "SMStormJoust@nadeolabs");
	    if (in_array($this->titleId, $SMstorm)) {
		$this->counter = time();
		$this->connection->TriggerModeScriptEvent("LibXmlRpc_DisableAltMenu", $login);
		$this->connection->sendDisplayManialinkPage($login, "<manialinks><manialink id=\"0\"><quad></quad></manialink><custom_ui><altmenu_scores visible=\"false\" /></custom_ui></manialinks>", 0, false);
	    }
	} catch (\Exception $e) {
	    echo "error: " . $e->getMessage();
	}
    }

    function onPlayerDisconnect($login, $reason = null) {
	
    }

    function hudCommands($login, $param = "null") {
	switch ($param) {
	    case "reset":
		$this->resetHud($login);
		break;
	    case "move":
		$this->enableHudMove($login);
		break;
	    case "lock":
		$this->disableHudMove($login);
		break;
	    default:
		$this->exp_chatSendServerMessage($this->msg_params, $login);
		break;
	}
    }

    function enableHudMove($login) {
	if (Config::getInstance()->disablePersonalHud) {
	    $this->exp_chatSendServerMessage($this->msg_disabled, $login);
	} else {
	    $window = Windows\HudMove::Create($login, false);
	    $window->enable();
	    $window->show();
	}
    }

    function disableHudMove($login) {
	if (Config::getInstance()->disablePersonalHud) {
	    $this->exp_chatSendServerMessage($this->msg_disabled, $login);
	} else {
	    $window = Windows\HudMove::Create($login, false);
	    $window->disable();
	    $window->show();
	}
    }

    function showConfigWindow($login, $entries) {
	if (Config::getInstance()->disablePersonalHud) {
	    $this->exp_chatSendServerMessage($this->msg_disabled, $login);
	} else {
	    $window = Windows\Configuration::Create($login, true);
	    $window->setSize(120, 90);
	    $window->setData($entries);
	    $window->show();
	}
    }

    function resetHud($login) {
	if (Config::getInstance()->disablePersonalHud) {
	    $this->exp_chatSendServerMessage($this->msg_disabled, $login);
	} else {
	    $window = Windows\ResetHud::Create($login);
	    $window->setTimeout(1);
	    $window->show();
	    $this->resetLogins[$login] = 0;
	    $this->exp_chatSendServerMessage(exp_getMessage("Starting hud reset, please wait"), $login);
	}
    }

    function logMemory() {
	$mem = "Memory Usage: " . round(memory_get_usage() / 1024) . "Kb";
	\ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
	print "\n" . $mem . "\n";
    }


    /**
     * Cleans the string for manialink or maniascript purposes.
     *
     * @param string $string The string to clean
     *
     * @return string cleaned up string
     */
    public static function fixString($string){

        $out = str_replace('"', "'", $string);
        $out = str_replace('\\', '\\\\', $out);
	$out = str_replace('-', '–', $out);

	return $out;
    }

    public static function showConfirmDialog($login, $actionId) {
	$window = Windows\ConfirmDialog::Create($login);
	$window->setInvokeAction($actionId);
	$window->show();
    }

    /**
     * Displays a Confirm Dialog for action. 
     * 
     */
    public static function createConfirm($finalAction) {
//$finalAction = call_user_func_array(array(\ManiaLive\Gui\ActionHandler::getInstance(), 'createAction'), func_get_args());
	$outAction = call_user_func_array(array(\ManiaLive\Gui\ActionHandler::getInstance(), 'createAction'), array(array(__NAMESPACE__ . '\Gui', 'showConfirmDialog'), $finalAction));
	return $outAction;
    }

}

?>