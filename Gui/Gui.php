<?php

namespace ManiaLivePlugins\eXpansion\Gui;

use Exception;
use ManiaLive\Gui\ActionHandler;
use ManiaLive\Gui\GuiHandler;
use ManiaLive\Utilities\Logger;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Widgets as WConfig;
use ManiaLivePlugins\eXpansion\Gui\Widgets\HudPanel;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Preloader;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Gui\Windows\Configuration;
use ManiaLivePlugins\eXpansion\Gui\Windows\ConfirmDialog;
use ManiaLivePlugins\eXpansion\Gui\Windows\HudMove;
use ManiaLivePlugins\eXpansion\Gui\Windows\Notice;
use ManiaLivePlugins\eXpansion\Gui\Windows\ResetHud;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class Gui extends ExpPlugin {

    private $titleId;
    private $msg_params, $msg_disabled;
    private $resetLogins = array();
    private $counter = 0;
    private $preloader;

    public function exp_onInit() {
        $this->setVersion("0.1");
    }

    public function exp_onLoad() {
        HudPanel::$mainPlugin = $this;

        if ($this->expStorage->simpleEnviTitle == "SM") {
            $settings = array("S_UseScriptCallbacks" => true);
            $this->connection->setModeScriptSettings($settings);
        }

        $config = Config::getInstance();
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

        $this->preloader = Preloader::Create(null);
        $this->preloader->show();

        foreach ($this->storage->players as $player) {
            $this->onPlayerConnect($player->login, false);
        }
        foreach ($this->storage->spectators as $player) {
            $this->onPlayerConnect($player->login, true);
        }

        $this->loadWidgetConfigs();

        $edge = Widgets\Edge::Create(null);
        $edge->setPosition(-160, -35);
        $edge->show(); 
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
            /** @var GuiHandler */
            $guiHandler = GuiHandler::getInstance();
            foreach ($this->resetLogins as $login => $value) {
// delayed tick
                $this->resetLogins[$login] ++;
                switch ($this->resetLogins[$login]) {
                    case 1:
                        ResetHud::Erase($login);
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

            if ($this->expStorage->simpleEnviTitle == "SM") {
                $this->counter = time();
                $this->connection->TriggerModeScriptEvent("LibXmlRpc_DisableAltMenu", $login);
                $this->connection->sendDisplayManialinkPage($login, "<manialinks><manialink id=\"0\"><quad></quad></manialink><custom_ui><altmenu_scores visible=\"false\" /></custom_ui></manialinks>", 0, false);
            }
        } catch (Exception $e) {
            Helper::log("[Gui]Error while disabling alt menu : " . $e->getMessage());
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
        }
        else {
            $window = HudMove::Create($login, false);
            $window->enable();
            $window->show();
        }
    }

    function disableHudMove($login) {
        if (Config::getInstance()->disablePersonalHud) {
            $this->exp_chatSendServerMessage($this->msg_disabled, $login);
        }
        else {
            $window = HudMove::Create($login, false);
            $window->disable();
            $window->show();
        }
    }

    function showConfigWindow($login, $entries) {
        if (Config::getInstance()->disablePersonalHud) {
            $this->exp_chatSendServerMessage($this->msg_disabled, $login);
        }
        else {
            $window = Configuration::Create($login, true);
            $window->setSize(120, 90);
            $window->setData($entries);
            $window->show();
        }
    }

    function resetHud($login) {
        if (Config::getInstance()->disablePersonalHud) {
            $this->exp_chatSendServerMessage($this->msg_disabled, $login);
        }
        else {
            $window = ResetHud::Create($login);
            $window->setTimeout(1);
            $window->show();
            $this->resetLogins[$login] = 0;
            $this->exp_chatSendServerMessage(exp_getMessage("Starting hud reset, please wait"), $login);
        }
    }

    function logMemory() {
        $mem = "Memory Usage: " . round(memory_get_usage() / 1024) . "Kb";
        Logger::getLog("memory")->write($mem);
        print "\n" . $mem . "\n";
    }

    /**
     * Cleans the string for manialink or maniascript purposes.
     *
     * @param string $string The string to clean
     *
     * @return string cleaned up string
     */
    public static function fixString($string, $multiline = false) {
        $out = str_replace("\r", '__', $string);
        if (!$multiline) {
            $out = str_replace("\n", '', $out);
        }
        $out = str_replace('"', "'", $out);
        $out = str_replace('\\', '\\\\', $out);
        $out = str_replace('-', '–', $out);
        ;
        return $out;
    }

    public static function showConfirmDialog($login, $actionId) {
        $window = ConfirmDialog::Create($login);
        $window->setInvokeAction($actionId);
        $window->show();
    }

    public static function showNotice(Message $message, $login, $args = array()) {
        $window = Notice::Create($login);
        $window->setMessage($message, $args);
        $window->show();
    }

    /**
     * Preload image
     * @param type $url
     */
    public static function preloadImage($url) {
         Preloader::add($url);
    }

    /**
     * Preload image
     * @param type $url
     */
    public static function preloadRemove($url) {
        	Preloader::remove($url);
    }

    public static function preloadUpdate() {
        	$preloader = Preloader::Create(null);
        	$preloader->show();
    }

    /**
     * Displays a Confirm Dialog for action.
     *
     */
    public static function createConfirm($finalAction) {
//$finalAction = call_user_func_array(array(\ManiaLive\Gui\ActionHandler::getInstance(), 'createAction'), func_get_args());
        $outAction = call_user_func_array(array(ActionHandler::getInstance(), 'createAction'), array(array(__NAMESPACE__ . '\Gui', 'showConfirmDialog'), $finalAction));
        return $outAction;
    }

}

?>