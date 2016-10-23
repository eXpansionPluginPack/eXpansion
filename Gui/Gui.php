<?php

namespace ManiaLivePlugins\eXpansion\Gui;

use Exception;
use ManiaLive\Gui\ActionHandler;
use ManiaLive\Gui\GuiHandler;
use ManiaLive\Utilities\Logger;
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

class Gui extends ExpPlugin
{
    private $titleId;
    private $msg_params;
    private $msg_disabled;
    private $resetLogins = array();
    private $counter = 0;
    private $preloader;
    // next 2 is used by contextMenu
    public static $items = array();
    public static $callbacks = array();

    public function eXpOnInit()
    {
        $this->setVersion("0.1");
    }

    public function eXpOnLoad()
    {
        HudPanel::$mainPlugin = $this;

        if ($this->expStorage->simpleEnviTitle == "SM") {
            $settings = array("S_UseScriptCallbacks" => true);
            $this->connection->setModeScriptSettings($settings);
        }

        $config = Config::getInstance();
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->enableTickerEvent();
        $this->registerChatCommand("hud", "hudCommands", 0, true);
        $this->registerChatCommand("hud", "hudCommands", 1, true);
        $this->setPublicMethod("hudCommands");
        $this->setPublicMethod("showConfigWindow");

        $this->msg_params = eXpGetMessage("possible parameters: move, lock, reset");
        $this->msg_disabled = eXpGetMessage("#error#Server Admin has disabled personal huds. Sorry!");

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

    public static function getScaledSize($sizes, $totalSize)
    {
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

    public function loadWidgetConfigs()
    {
        $config = WConfig::getInstance();
        foreach ($config as $confName => $values) {

            $confs = explode('_', $confName);
            if (sizeof($confs) > 1) {

                $widgetName = str_replace(" ", "", $confs[0]);
                Widget::setParameter($widgetName, $confs[1], $values);
            }
        }
    }

    public function onTick()
    {
        if (count($this->resetLogins) > 0) {
            /** @var GuiHandler */
            $guiHandler = GuiHandler::getInstance();
            foreach ($this->resetLogins as $login => $value) {
                $this->resetLogins[$login]++;
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
                        $this->eXpChatSendServerMessage(eXpGetMessage("Hud reset done!"), $login);
                        break;
                }
            }
        }
        if ($this->counter != 0 && time() - $this->counter > 2) {
            $this->connection->sendDisplayManialinkPage(
                null,
                "<manialinks><manialink id=\"0\"><quad></quad></manialink>
<custom_ui><altmenu_scores visible=\"false\" /></custom_ui></manialinks>",
                0,
                false
            );
            $this->counter = 0;
        }
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        try {

            if ($this->expStorage->simpleEnviTitle == "SM") {
                $this->counter = time();
                $this->connection->TriggerModeScriptEvent("LibXmlRpc_DisableAltMenu", $login);
                $this->connection->sendDisplayManialinkPage(
                    $login,
                    "<manialinks><manialink id=\"0\"><quad></quad></manialink
><custom_ui><altmenu_scores visible=\"false\" /></custom_ui></manialinks>",
                    0,
                    false
                );
            }
        } catch (Exception $e) {
            $this->console("Error while disabling alt menu : " . $e->getMessage());
        }
    }

    public function onPlayerDisconnect($login, $reason = null)
    {

    }

    public function hudCommands($login, $param = "null")
    {
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
                $this->eXpChatSendServerMessage($this->msg_params, $login);
                break;
        }
    }

    public function enableHudMove($login)
    {
        if (Config::getInstance()->disablePersonalHud) {
            $this->eXpChatSendServerMessage($this->msg_disabled, $login);
        } else {
            $window = HudMove::Create($login, false);
            $window->enable();
            $window->show();
        }
    }

    public function disableHudMove($login)
    {
        if (Config::getInstance()->disablePersonalHud) {
            $this->eXpChatSendServerMessage($this->msg_disabled, $login);
        } else {
            $window = HudMove::Create($login, false);
            $window->disable();
            $window->show();
        }
    }

    public function showConfigWindow($login, $entries)
    {
        if (Config::getInstance()->disablePersonalHud) {
            $this->eXpChatSendServerMessage($this->msg_disabled, $login);
        } else {
            $window = Configuration::Create($login, true);
            $window->setSize(120, 90);
            $window->setData($entries);
            $window->show();
        }
    }

    public function resetHud($login)
    {
        if (Config::getInstance()->disablePersonalHud) {
            $this->eXpChatSendServerMessage($this->msg_disabled, $login);
        } else {
            $window = ResetHud::Create($login);
            $window->setTimeout(1);
            $window->show();
            $this->resetLogins[$login] = 0;
            $this->eXpChatSendServerMessage(eXpGetMessage("Starting hud reset, please wait"), $login);
        }
    }

    public function logMemory()
    {
        $mem = "Memory Usage: " . round(memory_get_usage() / 1024) . "Kb";
        Logger::getLog("memory")->write($mem);
        print "\n" . $mem . "\n";
    }

    public function onPlayerManialinkPageAnswer($playerUid, $login, $answer, array $entries)
    {
        if (strpos($answer, "onMenuItemClick") !== false) {

            $parseStr = str_replace("onMenuItemClick?", "", $answer);
            $parsed = array();

            parse_str($parseStr, $parsed);

            if (!array_key_exists($parsed['hash'], self::$callbacks)) {
                return;
            }
            $item = $parsed['item'];
            $hash = $parsed['hash'];
            $value = $parsed['dataId'];

            $test = \call_user_func(self::$callbacks[$hash], array($login, $item, self::$items[$hash][$value]->data));
        }
    }

    /**
     * Cleans the string for manialink or maniascript purposes.
     *
     * @param string $string The string to clean
     * @param bool $multiline if the string is multiline
     * @return string cleaned up string
     */
    public static function fixString($string, $multiline = false)
    {
        $out = str_replace("\r", '', $string);
        if (!$multiline) {
            $out = str_replace("\n", '', $out);
        }
        $out = str_replace('"', "'", $out);
        $out = str_replace('\\', '\\\\', $out);
        $out = str_replace('-', '–', $out);

        return $out;
    }

    /**
     * @param $login
     * @param $actionId
     * @param string $text
     */
    public static function showConfirmDialog($login, $actionId, $text = "")
    {
        $window = ConfirmDialog::Create($login);
        $window->setText($text);
        $window->setInvokeAction($actionId);
        $window->show();
    }

    /**
     * @param $message
     * @param $login
     * @param array $args
     */
    public static function showNotice($message, $login, $args = array())
    {
        $window = null;
        if (is_array($login)) {
            $grp = \ManiaLive\Gui\Group::Create("notice", $login);
            $window = Notice::Create($grp);
        } else {
            $window = Notice::Create($login);
        }

        if (is_string($message)) {
            $message = eXpGetMessage($message);
        }
        $window->setMessage($message, $args);
        $window->show($login);
    }

    /**
     * @param $message
     * @param $login
     */
    public static function showError($message, $login)
    {
        $window = null;
        if (is_array($login)) {
            $grp = \ManiaLive\Gui\Group::Create("error", $login);
            $window = Windows\Error::Create($grp);
        } else {
            $window = Windows\Error::Create($login);
        }
        $window->setMessage($message);
        $window->show($login);
    }

    /**
     * Preload image
     *
     * @param string $url
     */
    public static function preloadImage($url)
    {
        Preloader::add($url);
    }

    /**
     * Preload image
     *
     * @param type $url
     */
    public static function preloadRemove($url)
    {
        Preloader::remove($url);
    }

    public static function preloadUpdate()
    {
        $preloader = Preloader::Create(null);
        $preloader->show();
    }

    /**
     * Displays a Confirm Dialog for action.
     *
     */
    public static function createConfirm($finalAction)
    {
        $outAction = call_user_func_array(
            array(ActionHandler::getInstance(), 'createAction'),
            array(array(__NAMESPACE__ . '\Gui', 'showConfirmDialog'), $finalAction)
        );

        return $outAction;
    }
}
