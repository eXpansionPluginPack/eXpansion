<?php

namespace ManiaLivePlugins\eXpansion\Adm;

use ManiaLive\Event\Dispatcher;
use Maniaplanet\DedicatedServer\Structures\GameInfos;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerOptions;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\GameOptions;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\AdminPanel;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerControlMain;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;

class Adm extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $msg_forceScore_error, $msg_scriptSettings, $msg_databasePlugin;
    private $config;

    public function exp_onLoad() {
	$this->msg_forceScore_error = exp_getMessage("ForceScores can be used only with rounds or team mode");
	$this->msg_scriptSettings = exp_getMessage("ScriptSettings available only in script mode");
	$this->msg_databasePlugin = exp_getMessage("Database plugin not loaded!");

	$this->setPublicMethod('serverControlMain');

	if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups')) {
	    Dispatcher::register(\ManiaLivePlugins\eXpansion\AdminGroups\Events\Event::getClass(), $this);
	}

	$cmd = AdminGroups::addAdminCommand('setting expansion', $this, 'showExpSettings', 'expansion_settings');
	$cmd->setHelp('Set up your expansion');
	AdminGroups::addAlias($cmd, "setexp"); // xaseco & fast
    }

    public function showExpSettings($login) {
	$this->callPublicMethod('\ManiaLivePlugins\eXpansion\Core\Core', 'showExpSettings', $login);
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


	$cmd = AdminGroups::addAdminCommand('server control', $this, 'serverControlMain', Permission::server_controlPanel);
	$cmd->setHelp('Displays the main control panel for the server');
	$cmd->setMinParam(0);
	AdminGroups::addAlias($cmd, "server");

	foreach ($this->storage->players as $player)
	    $this->onPlayerConnect($player->login, false);
	foreach ($this->storage->spectators as $player)
	    $this->onPlayerConnect($player->login, true);

	$this->onBeginMap(null, null, null);
    }

    function onPlayerConnect($login, $isSpectator) {
	if ($this->exp_isRelay())
	    return;
//        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login) || !(empty($this->config->publicResAmount) || $this->config->publicResAmount[0] == -1) || !(empty($this->config->publicSkipAmount) || $this->config->publicSkipAmount[0] == -1)) {
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
	    $info = AdminPanel::Create($login);
	    $info->setSize(40, 6);
	    $info->show();
	}
    }

    public function onPlayerDisconnect($login, $reason = null) {
	AdminPanel::Erase($login);
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
	if (AdminGroups::hasPermission($login, Permission::game_settings)) {
	    $gamemode = $this->storage->gameInfos->gameMode;
	    if ($gamemode == GameInfos::GAMEMODE_ROUNDS || $gamemode == GameInfos::GAMEMODE_TEAM || GameInfos::GAMEMODE_CUP) {
		$window = Gui\Windows\ForceScores::Create($login);
		$window->setTitle(__('Force Scores', $login));
		$window->centerOnScreen();
		$window->setSize(160, 80);
		$window->show();
	    }
	    else {
		$this->exp_chatSendServerMessage($this->msg_forceScore_error, $login);
	    }
	}
    }

    public function forceScoresOk() {
	$this->exp_chatSendServerMessage('Notice: Admin has altered the scores of current match!');
	if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\ESLcup\\ESLcup")) {
	    $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\ESLcup\\ESLcup", "syncScores");
	}
    }

    public function gameOptions($login) {
	if (AdminGroups::hasPermission($login, Permission::game_settings)) {
	    $window = GameOptions::Create($login);
	    $window->setTitle(__('Game Options', $login));
	    $window->setSize(160, 65);
	    $window->centerOnScreen();
	    $window->show();
	}
    }

    public function serverManagement($login) {
	if (AdminGroups::hasPermission($login, Permission::server_stopDedicated) || AdminGroups::hasPermission($login, Permission::server_stopManialive)) {
	    $window = Gui\Windows\ServerManagement::Create($login);
	    $window->setTitle(__('Server Control', $login));
	    $window->setSize(60, 20);
	    $window->centerOnScreen();
	    $window->show();
	}
    }

    public function roundPoints($login) {
	if (AdminGroups::hasPermission($login, Permission::game_settings)) {
	    $window = Gui\Windows\RoundPoints::Create($login);
	    $window->setTitle(__('Custom Round Points', $login));
	    $window->setSize(160, 90);
	    $window->centerOnScreen();
	    $window->show();
	}
    }

    public function serverControlMain($login) {
	if (AdminGroups::hasPermission($login, Permission::server_controlPanel)) {
	    $window = Gui\Windows\ServerControlMain::Create($login);
	    $window->setTitle(__('Server Management', $login));
	    $window->setSize(120, 20);
	    $window->show();
	}
    }

    public function showVotesConfig($login) {
	if (AdminGroups::hasPermission($login, Permission::server_votes)) {
	    if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\Votes\Votes'))
		$this->callPublicMethod('\ManiaLivePlugins\eXpansion\Votes\Votes', 'showVotesConfig', $login);
	}
    }

    public function matchSettings($login) {
	if (AdminGroups::hasPermission($login, Permission::game_matchSave) || AdminGroups::hasPermission($login, 'game_matchDelete') || AdminGroups::hasPermission($login, 'game_match')) {
	    $window = Gui\Windows\MatchSettings::Create($login);
	    $window->setTitle(__('Match Settings', $login));
	    $window->centerOnScreen();
	    $window->setSize(160, 100);
	    $window->show();
	}
    }

    public function scriptSettings($login) {
	if (AdminGroups::hasPermission($login, Permission::game_settings)) {
	    if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
		$window = Gui\Windows\ScriptSettings::Create($login);
		$window->setTitle(__('Script Settings', $login));
		$window->centerOnScreen();
		$window->setSize(160, 100);
		$window->show();
	    }
	    else {
		$this->exp_chatSendServerMessage($this->msg_scriptSettings, $login);
	    }
	}
    }

    public function dbTools($login) {
	if (AdminGroups::hasPermission($login, Permission::server_database)) {
	    if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\Database\\Database")) {
		$this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\Database\\Database", "showDbMaintainance", $login);
	    }
	    else {
		$this->exp_chatSendServerMessage($this->msg_databasePlugin, $login);
	    }
	}
    }

    public function skipMap($login) {
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::map_skip)) {
	    if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\Chat_Admin\Chat_Admin")) {
		$this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\Chat_Admin\Chat_Admin", "skipMap", $login);
	    }
	}
    }

    public function cancelVote($login) {
	if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\Chat_Admin\\Chat_Admin")) {
	    $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\Chat_Admin\\Chat_Admin", "cancelVote", $login);
	    return;
	}
	$this->connection->cancelVote();
    }

    public function endRound($login) {
	if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\Chat_Admin\\Chat_Admin")) {
	    $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\Chat_Admin\\Chat_Admin", "forceEndRound", $login);
	    return;
	}
	$this->connection->forceEndRound();
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

}

?>