<?php

namespace ManiaLivePlugins\eXpansion\Adm;

use ManiaLive\Event\Dispatcher;
use Maniaplanet\DedicatedServer\Structures\GameInfos;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerOptions;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\GameOptions;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\AdminPanel;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerControlMain;
use ManiaLivePlugins\eXpansion\Adm\Gui\Widgets\ResSkipButtons;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Adm extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $msg_forceScore_error, $msg_scriptSettings, $msg_databasePlugin, $msg_resOnProgress,
            $msg_resUnused, $msg_resMax, $msg_skipUnused, $msg_skipMax, $msg_prestart, $msg_pskip;
    private $config;
    private $donateConfig;
    private $lastMapUid = null;
    private $resCount = 0;
    private $resActive;
    private $skipCount = 0;
    private $skipActive;
    private $actions = array();

    public function exp_onInit() {
        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }

    public function exp_onLoad() {
        $this->msg_forceScore_error = exp_getMessage("ForceScores can be used only with rounds or team mode");
        $this->msg_scriptSettings = exp_getMessage("ScriptSettings available only in script mode");
        $this->msg_databasePlugin = exp_getMessage("Database plugin not loaded!");
        $this->msg_resOnProgress = exp_getMessage("The restart of this track is in progress!");
        $this->msg_resUnused = exp_getMessage("#error#Player can't restart tracks on this server");
        $this->msg_resMax = exp_getMessage("#error#The map has already been restarted. Limit reached!");
        $this->msg_skipUnused = exp_getMessage("#error#You can't skip tracks on this server.");
        $this->msg_skipMax = exp_getMessage("#error#You have skipped to many maps already!");
        $this->msg_prestart = exp_getMessage("#player#Player #variable# %s #player#pays and restarts the challenge!");
        $this->msg_pskip = exp_getMessage('#player#Player#variable# %s #player#pays and skips the challenge!');

        $this->setPublicMethod('isPublicResIsActive');
        $this->setPublicMethod('isPublicSkipActive');

        if ($this->isPluginLoaded("eXpansion\AdminGroups")) {
            Dispatcher::register(\ManiaLivePlugins\eXpansion\AdminGroups\Events\Event::getClass(), $this);
        }

        $this->config = Config::getInstance();
        $this->donateConfig = \ManiaLivePlugins\eXpansion\DonatePanel\Config::getInstance();

        $this->actions['skip'] = ActionHandler::getInstance()->createAction(array($this, "skipMap"));
        $this->actions['res'] = ActionHandler::getInstance()->createAction(array($this, "restartMap"));
    }

    public function isPublicResIsActive() {
        return !(empty($this->config->publicResAmount) || $this->config->publicResAmount[0] == -1);
    }

    public function isPublicSkipActive() {
        return !(empty($this->config->publicSkipAmount) || $this->config->publicSkipAmount[0] == -1);
    }

    function exp_admin_added($login) {
        $this->onPlayerConnect($login, false);
    }

    function exp_admin_removed($login) {
        AdminPanel::Erase($login);
    }

    function exp_onReady() {
        $this->enableDedicatedEvents();
        ServerControlMain::$mainPlugin = $this;
        Gui\Windows\RoundPoints::$plugin = $this;
        Gui\Windows\ForceScores::$mainPlugin = $this;
        Gui\Windows\AdminPanel::$mainPlugin = $this;

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);

        $this->onBeginMap(null, null, null);
    }

    function onPlayerConnect($login, $isSpectator) {

//        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login) || !(empty($this->config->publicResAmount) || $this->config->publicResAmount[0] == -1) || !(empty($this->config->publicSkipAmount) || $this->config->publicSkipAmount[0] == -1)) {
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
            $info = AdminPanel::Create($login);
            $info->setSize(50, 20);
            $info->setPosition(-160, -46);
            $info->show();
        }
        $this->showResSkip($login);
    }

    public function showResSkip($login) {
        $widget = ResSkipButtons::Create($login);
        $widget->setSize(50, 10);

        $nbSkips = isset($this->skipCount[$login]) ? $this->skipCount[$login] : 0;
        if (isset($this->config->publicSkipAmount[$nbSkips]) && $this->config->publicSkipAmount[$nbSkips] != -1) {
            $amount = $this->config->publicSkipAmount[$nbSkips];
            $widget->setSkipAmount($amount);
        } else {
            if ($nbSkips >= count($this->config->publicSkipAmount)) {
                $widget->setSkipAmount("max");
            } else {
                $widget->setSkipAmount("no");
            }
        }

        if (isset($this->config->publicResAmount[$this->resCount]) && $this->config->publicResAmount[$this->resCount] != -1) {
            $amount = $this->config->publicResAmount[$this->resCount];
            $widget->setResAmount($amount);
        } else {
            if ($this->resCount >= count($this->config->publicResAmount)) {
                $widget->setResAmount("max");
            } else {
                $widget->setResAmount("no");
            }
        }
        $widget->setActions($this->actions['res'], $this->actions['skip']);

        $widget->setPosition(116.0, -65.0);
        $widget->show();
    }

    public function onPlayerDisconnect($login, $reason = null) {
        if (isset($this->skipCount[$login]))
            unset($this->skipCount[$login]);
        AdminPanel::Erase($login);
        ResSkipButtons::Erase($login);
    }

    public function onOliverde8HudMenuReady($menu) {

        $parent = $menu->findButton(array('admin', 'Server Options'));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Options";
            $button["plugin"] = $this;
            $parent = $menu->addButton("admin", "Server Options", $button);
        }

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Options";
        $button["plugin"] = $this;
        $button["function"] = "serverControlMain";
        $menu->addButton($parent, "Server Window", $button);

        $parent = $menu->findButton(array('admin', 'Game Options'));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "ProfileAdvanced";
            $button["plugin"] = $this;
            $parent = $menu->addButton("admin", "Game Options", $button);
        }

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "ProfileAdvanced";
        $button["plugin"] = $this;
        $button["function"] = "gameOptions";
        $button["permission"] = "game_gamemode";
        $menu->addButton($parent, "Game Window", $button);

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Save";
        $button["plugin"] = $this;
        $button["function"] = "matchSettings";
        $button["permission"] = "game_match";
        $menu->addButton($parent, "Match Settings", $button);
    }

    public function serverOptions($login) {
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getAdmin($login) != null) {
            $window = ServerOptions::Create($login);
            $window->setTitle(__('Server Options', $login));
            $window->centerOnScreen();
            $window->setSize(160, 80);
            $window->show();
        }
    }

    public function forceScores($login) {
        if (AdminGroups::hasPermission($login,  'game_settings')) {
            $gamemode = $this->storage->gameInfos->gameMode;
            if ($gamemode == GameInfos::GAMEMODE_ROUNDS || $gamemode == GameInfos::GAMEMODE_TEAM || GameInfos::GAMEMODE_CUP) {
                $window = Gui\Windows\ForceScores::Create($login);
                $window->setTitle(__('Force Scores', $login));
                $window->centerOnScreen();
                $window->setSize(160, 80);
                $window->show();
            } else {
                $this->exp_chatSendServerMessage($this->msg_forceScore_error, $login);
            }
        }
    }

    public function forceScoresOk() {
        $this->exp_chatSendServerMessage('Notice: Admin has altered the scores of current match!');
        if ($this->isPluginLoaded("eXpansion\ESLcup")) {
            $this->callPublicMethod("ManiaLivePlugins\\eXpansion\ESLcup", "syncScores");
        }
    }

    public function gameOptions($login) {
        if (AdminGroups::hasPermission($login,  'game_settings')) {
            $window = GameOptions::Create($login);
            $window->setTitle(__('Game Options', $login));
            $window->setSize(160, 80);
            $window->centerOnScreen();
            $window->show();
        }
    }

    public function serverManagement($login) {
        if (AdminGroups::hasPermission($login,  'server_stopServer') || AdminGroups::hasPermission($login,  'server_stopManialive')) {
            $window = Gui\Windows\ServerManagement::Create($login);
            $window->setTitle(__('Server Control', $login));
            $window->setSize(60, 20);
            $window->centerOnScreen();
            $window->show();
        }
    }

    public function roundPoints($login) {
        if (AdminGroups::hasPermission($login,  'server_admin')) {
            $window = Gui\Windows\RoundPoints::Create($login);
            $window->setTitle(__('Custom Round Points', $login));
            $window->setSize(160, 70);
            $window->centerOnScreen();
            $window->show();
        }
    }

    public function serverControlMain($login) {
        if (AdminGroups::hasPermission($login,  'server_admin')) {
            $window = Gui\Windows\ServerControlMain::Create($login);
            $window->setTitle(__('Server Management', $login));
            $window->setSize(120, 20);
            $window->show();
        }
    }

    public function matchSettings($login) {
        if (AdminGroups::hasPermission($login,  'game_matchSave') || AdminGroups::hasPermission($login,  'game_matchDelete') || AdminGroups::hasPermission($login,  'game_match')) {
            $window = Gui\Windows\MatchSettings::Create($login);
            $window->setTitle(__('Match Settings', $login));
            $window->centerOnScreen();
            $window->setSize(160, 100);
            $window->show();
        }
    }

    public function scriptSettings($login) {
        if (AdminGroups::hasPermission($login,  'game_settings')) {
            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
                $window = Gui\Windows\ScriptSettings::Create($login);
                $window->setTitle(__('Script Settings', $login));
                $window->centerOnScreen();
                $window->setSize(160, 100);
                $window->show();
            } else {
                $this->exp_chatSendServerMessage($this->msg_scriptSettings, $login);
            }
        }
    }

    public function dbTools($login) {
        if (AdminGroups::hasPermission($login,  'db_maintainance')) {
            if ($this->isPluginLoaded("eXpansion\Database")) {
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\Database", "showDbMaintainance", $login);
            } else {
                $this->exp_chatSendServerMessage($this->msg_databasePlugin, $login);
            }
        }
    }

    public function restartMap($login) {

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'map_res')) {
            if ($this->isPluginLoaded("eXpansion\Chat_Admin")) {
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\Chat_Admin", "restartMap", $login);
                return;
            }
            $this->connection->restartMap($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($login);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#restarts the challenge!', null, array($admin->nickName));
        } else {
            //Player restart cost Planets
            if ($this->resActive) {
                //Already restarted no need to do
                $this->exp_chatSendServerMessage($this->msg_resOnProgress, $login);
            } else if (isset($this->config->publicResAmount[$this->resCount]) && $this->config->publicResAmount[$this->resCount] != -1 && $this->resCount < count($this->config->publicResAmount)) {
                $amount = $this->config->publicResAmount[$this->resCount];
                $this->resActive = true;

                if (!empty($this->donateConfig->toLogin))
                    $toLogin = $this->donateConfig->toLogin;
                else
                    $toLogin = $this->storage->serverLogin;

                $bill = $this->exp_startBill($login, $toLogin, $amount, __("Are you sure you want to restart this map", $login), array($this, 'publicRestartMap'));
                $bill->setSubject('map_restart');
                $bill->setErrorCallback(5, array($this, 'failRestartMap'));
                $bill->setErrorCallback(6, array($this, 'failRestartMap'));
            }else {
                if (empty($this->config->publicResAmount) || $this->config->publicResAmount[0] == -1) {
                    $this->exp_chatSendServerMessage($this->msg_resUnused, $login);
                } else {
                    $this->exp_chatSendServerMessage($this->msg_resMax, $login);
                }
            }
        }
    }

    public function publicRestartMap(\ManiaLivePlugins\eXpansion\Core\types\Bill $bill) {
        $player = $this->storage->getPlayerObject($bill->getSource_login());
        $this->exp_chatSendServerMessage($this->msg_prestart, null, array($player->nickName));

        if ($this->isPluginLoaded("eXpansion\Maps")) {
            $this->callPublicMethod("ManiaLivePlugins\\eXpansion\Maps", "replayMap", $bill->getSource_login());
            return;
        }
        $this->connection->restartMap($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
    }

    public function failRestartMap(\ManiaLivePlugins\eXpansion\Core\types\Bill $bill, $state, $stateName) {
        $this->resActive = false;
    }

    public function skipMap($login) {

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'map_skip')) {
            if ($this->isPluginLoaded("eXpansion\Chat_Admin")) {
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\Chat_Admin", "skipMap", $login);
            }
        } else {
            $nbSkips = isset($this->skipCount[$login]) ? $this->skipCount[$login] : 0;

            if (isset($this->config->publicSkipAmount[$nbSkips]) && $this->config->publicSkipAmount[$nbSkips] != -1 && $nbSkips < count($this->config->publicSkipAmount)) {
                $amount = $this->config->publicSkipAmount[$nbSkips];

                if (!empty($this->donateConfig->toLogin))
                    $toLogin = $this->donateConfig->toLogin;
                else
                    $toLogin = $this->storage->serverLogin;

                $bill = $this->exp_startBill($login, $toLogin, $amount, __("Are you sure you want to skip this map", $login), array($this, 'publicSkipMap'));
                $bill->setSubject('map_skip');
            } else {
                if (empty($this->config->publicSkipAmount) || $this->config->publicSkipAmount[0] == -1) {
                    $this->exp_chatSendServerMessage($this->msg_skipUnused, $login);
                } else {
                    $this->exp_chatSendServerMessage($this->msg_skipMax, $login);
                }
            }
        }
    }

    public function cancelVote($login) {
        if ($this->isPluginLoaded("eXpansion\Chat_Admin")) {
            $this->callPublicMethod("ManiaLivePlugins\\eXpansion\Chat_Admin", "cancelVote", $login);
            return;
        }
        $this->connection->cancelVote();
    }

    public function endRound($login) {
        if ($this->isPluginLoaded("eXpansion\Chat_Admin")) {
            $this->callPublicMethod("ManiaLivePlugins\\eXpansion\Chat_Admin", "forceEndRound", $login);
            return;
        }
        $this->connection->forceEndRound();
    }

    public function publicSkipMap(\ManiaLivePlugins\eXpansion\Core\types\Bill $bill) {
        $this->skipActive = true;
        $this->connection->nextMap($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
        $player = $this->storage->getPlayerObject($bill->getSource_login());
        $this->exp_chatSendServerMessage($this->msg_pskip, null, array($player->nickName));
    }

    public function adminGroups($login) {
        \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance()->windowGroups($login);
    }

    public function setPoints($login, $points) {
        try {
            $nick = $this->storage->getPlayerObject($login)->nickName;
            $ipoints = implode(",", $points);
            $msg = exp_getMessage('#admin_action#Admin %s $z$s#admin_action#sets custom round points to #variable#%s');
            $this->exp_chatSendServerMessage($msg, null, array($nick, $ipoints));
            $this->connection->setRoundCustomPoints($points);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('#error#Error: %s', $login, $e->getMessage()), $login);
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        if ($this->storage->currentMap->uId == $this->lastMapUid)
            $this->resCount++;
        else {
            $this->lastMapUid = $this->storage->currentMap->uId;
            $this->resCount = 0;
        }
        $this->resActive = false;

        if (!$this->skipActive) {
            $this->skipCount = array();
        }

        ResSkipButtons::EraseAll();
        foreach ($this->storage->players as $player)
            $this->showResSkip($player->login);
        foreach ($this->storage->spectators as $player)
            $this->showResSkip($player->login);
    }

}

?>