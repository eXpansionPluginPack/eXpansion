<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of Admin
 *
 * @author oliverde8
 */
class Chat_Admin extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onInit() {
        parent::exp_onInit();

        $this->addDependency(new \ManiaLive\PluginHandler\Dependency('eXpansion\AdminGroups'));

        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }

    public function exp_onLoad() {
        parent::exp_onLoad();


        /* 		
         * ******************* 
         * Concerning Players 
         * *******************
         * 
         * 
         */

        $cmd = AdminGroups::addAdminCommand('player kick', $this, 'kick', 'player_kick');
        $cmd->setHelp(_('kick the player from the server'));
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "kick");

        $cmd = AdminGroups::addAdminCommand('player ban', $this, 'ban', 'player_black');
        $cmd->setHelp('Ban the player from the server');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "ban");

        $cmd = AdminGroups::addAdminCommand('player black', $this, 'blacklist', 'player_black');
        $cmd->setHelp('Add the player to the black list');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "black");

        $cmd = AdminGroups::addAdminCommand('player remove ban', $this, 'unban', 'player_black');
        $cmd->setHelp('Removes the ban of the player');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "unban");

        $cmd = AdminGroups::addAdminCommand('player clean banlist', $this, 'cleanBanlist', 'player_black');
        $cmd->setHelp('clears the banlist of players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "cleanbanlist");
       
        $cmd = AdminGroups::addAdminCommand('player clean blacklist', $this, 'cleanBlacklist', 'player_black');
        $cmd->setHelp('clears the blacklist of players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "cleanblacklist");


        $cmd = AdminGroups::addAdminCommand('player remove black', $this, 'unBlacklist', 'player_black');
        $cmd->setHelp('Removes the player from the black list');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "unBlack");







        /* AdminGroups::addAdminCommand('unignore', $this, 'unignore', 'player_ignore', 'The player won\'t be ignored any more');
          AdminGroups::addAdminCommand('player remove ignore', $this, 'unignore', 'player_ignore', 'The player won\'t be ignored any more'); */

        $cmd = AdminGroups::addAdminCommand('player spec', $this, 'forceSpec', 'player_spec');
        $cmd->setHelp('Forces the player to become spectator');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "spec");

        /*
         * *************************** 
         * Concerning Server Settings 
         * ***************************
         */
        $cmd = AdminGroups::addAdminCommand('set server name', $this, 'setServerName', 'server_name');
        $cmd->setHelp('Changes the name of the server');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "setservername");

        $cmd = AdminGroups::addAdminCommand('set server comment', $this, 'setServerComment', 'server_comment');
        $cmd->setHelp('Changes the server comment');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "setcomment");

        $cmd = AdminGroups::addAdminCommand('set server player password', $this, 'setServerPassword', 'server_password');
        $cmd->setHelp('Changes the player password');
        $cmd->setHelpMore("Changes the password for players to connect");
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "setpwd");

        $cmd = AdminGroups::addAdminCommand('set server spec password', $this, 'setSpecPassword', 'server_specpwd');
        $cmd->setHelp('Changes the spectator password');
        $cmd->setHelpMore("Changes the password for spectators to connect");
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "setspecpwd");

        $cmd = AdminGroups::addAdminCommand('set server ref password', $this, 'setSpecPassword', 'server_specpwd');
        $cmd->setHelp('Changes the Referee password');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "setrefpwd");

        $cmd = AdminGroups::addAdminCommand('set server maxplayers', $this, 'setServerMaxPlayers', 'server_maxplayer');
        $cmd->setHelp('Sets a new maximum of players');
        $cmd->setHelpMore('Sets the maximum number of players who can play on this server.');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Interger::getInstance());
        AdminGroups::addAlias($cmd, "setmaxplayers");

        $cmd = AdminGroups::addAdminCommand('set server maxspectators', $this, 'setServerMaxSpectators', 'server_maxspec');
        $cmd->setHelp('Sets a new maximum of spectator');
        $cmd->setHelp('Sets the maximum number of players who can spectate the players on this server.');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Interger::getInstance());
        AdminGroups::addAlias($cmd, "setmaxspecs");

        $cmd = AdminGroups::addAdminCommand('set server chattime', $this, 'setserverchattime', 'server_chattime');
        $cmd->setHelp('Sets the Chat time duration.');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setchattime");

        $cmd = AdminGroups::addAdminCommand('set server hide', $this, 'setHideServer', 'server_admin');
        AdminGroups::addAlias($cmd, "sethideserver");
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set server mapdownload', $this, 'setServerMapDownload', 'server_admin');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('stop dedicated', $this, 'stopDedicated', 'server_admin');
        $cmd->getHelp("Will stop this server.");
        AdminGroups::addAlias($cmd, 'stop ml');

        $cmd = AdminGroups::addAdminCommand('stop manialive', $this, 'stopManiaLive', 'server_admin');
        $cmd->getHelp("Will stop the Manialive instance running on this server.");
        AdminGroups::addAlias($cmd, 'stop dedi');

        /* 		 
         * ************************* 
         * Concerning Game Settings 
         * *************************
         */
        $cmd = AdminGroups::addAdminCommand('skip', $this, 'skipTrack', 'map_skip');
        $cmd->setHelp("Skips the current track");
        AdminGroups::addAlias($cmd, 'skipmap');
        AdminGroups::addAlias($cmd, 'next');
        AdminGroups::addAlias($cmd, 'nextmap');

        $cmd = AdminGroups::addAdminCommand('restart', $this, 'restartTrack', 'map_skip');
        AdminGroups::addAlias($cmd, 'res');
        AdminGroups::addAlias($cmd, 'resmap');
        AdminGroups::addAlias($cmd, 'restartmap');


        $cmd = AdminGroups::addAdminCommand('set game mode', $this, 'setGameMode', 'game_gamemode');
        $cmd->setHelp('Sets next mode {ta,rounds,team,laps,stunts,cup}');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, 'setgamemode');

        $cmd = AdminGroups::addAdminCommand('set game AllWarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Interger::getInstance());
        AdminGroups::addAlias($cmd, 'setAllWarmUpDuration');

        $cmd = AdminGroups::addAdminCommand('set game disableRespawn', $this, 'setDisableRespawn', 'game_settings');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, 'setDisableRespawn');

        //TimeAttack
        $cmd = AdminGroups::addAdminCommand('set game ta timelimit', $this, 'setTAlimit', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, 'setTAlimit');

        $cmd = AdminGroups::addAdminCommand('set game ta WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Interger::getInstance());

        //rounds
        $cmd = AdminGroups::addAdminCommand('set game rounds end', $this, 'forceEndRound', 'map_roundEnd');
        AdminGroups::addAlias($cmd, 'endround');
        AdminGroups::addAlias($cmd, 'er');

        $cmd = AdminGroups::addAdminCommand('set game rounds PointsLimit', $this, 'setRoundPointsLimit', 'game_settings');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, 'rpoints');

        $cmd = AdminGroups::addAdminCommand('set game rounds ForcedLaps', $this, 'setRoundForcedLaps', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game rounds NewRules', $this, 'setUseNewRulesRound', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game rounds WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Interger::getInstance());

        //laps
        $cmd = AdminGroups::addAdminCommand('set game laps TimeLimit', $this, 'setLapsTimeLimit', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());

        $cmd = AdminGroups::addAdminCommand('set game laps nbLaps', $this, 'setNbLaps', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game laps FinishTimeOut', $this, 'setFinishTimeout', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());

        $cmd = AdminGroups::addAdminCommand('set game laps WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Interger::getInstance());

        //team
        $cmd = AdminGroups::addAdminCommand('set game team PointsLimit', $this, 'setTeamPointsLimit', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game team maxPoint', $this, 'setMaxPointsTeam', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game team NewRules', $this, 'setUseNewRulesTeam', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game team forcePlayer', $this, 'forcePlayerTeam', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game team WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Interger::getInstance());

        //cup
        $cmd = AdminGroups::addAdminCommand('set game cup PointsLimit', $this, 'setCupPointsLimit', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game cup RoundsPerChallenge', $this, 'setCupRoundsPerChallenge', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game cup WarmUpDuration', $this, 'setCupWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());

        $cmd = AdminGroups::addAdminCommand('set game cup NbWinners', $this, 'setCupNbWinners', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game cup customPoints', $this, 'prepareRoundPoints', 'game_settings');
        $cmd->setMinParam(1);

        $cmd = AdminGroups::addAdminCommand('set game cup finishtimeout', $this, 'setFinishTimeout', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
    }

    /**
     * onOliverde8HudMenuReady()
     * Function used for adding buttons to Olivers Hud Menu.
     *
     * @param mixed $menu
     * @return void
     */
    public function onOliverde8HudMenuReady($menu) {
        //new oliverde8HudMenu($this, $menu, $this->storage, $this->connection);
    }

    function blacklist($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, 'Player %variable%' . $params[0] . '$0ae doesn\' exist.');
            return;
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->banAndBlackList($player, $params[1], true);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% blacklists the player %variable%' . $player->nickName);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanBlacklist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanBlackList();
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% cleans the blacklist.');
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanBanlist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanBanList();
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% cleans the banlist.');
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanIgnorelist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanIgnoreList();
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% cleans the ignorelist.');
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    public function unBlacklist($fromLogin, $params) {

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->unBlackList($params[0]);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% unblacklists the player ' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function ban($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if (is_object($player)) {
            $nickname = $player->nickName;
        } else {
            $nickname = $params[0];
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->ban($params[0]);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% bans the player %variable%' . $nickname);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function unban($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);

        try {
            $this->connection->unBan($params[0]);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% unban the player ' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function kick($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, 'Player %variable%' . $param1 . '$0ae doesn\' exist.');
            return;
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->kick($player);
            $plNick = $player->nickName;
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% kicks the player %variable%' . $player->nickName);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function forceSpec($fromLogin, $params) {
        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, 'Player %variable%' . $param1 . '$0ae doesn\' exist.');
            return;
        }

        $player = $this->storage->getPlayerObject($param1);
        $admin = $this->storage->getPlayerObject($fromLogin);
        $this->connection->forceSpectator($player, 1);
        $this->connection->forceSpectator($player, 0);
        $plNick = $player->nickName;
        $this->exp_chatSendServerMessage('%admin_action%Admin %variable%' . $admin->nickName . '$z$s%admina_ction% Forces the player %variable%' . $player->nickName . '$z$s%admin_action% to Spectator.');
    }

    public function sendErrorChat($login, $message) {
        $this->exp_chatSendServerMessage('%admin_error%' . $message, $login);
    }

    function setServerName($fromLogin, $params) {

        $name = implode(" ", $params);
        try {
            $this->connection->setServerName($name);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets new server name: %variable%' . $name);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerComment($fromLogin, $params) {

        $comment = implode(" ", $params);
        try {
            $this->connection->setServerName($comment);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets new server comment');
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerMaxPlayers($fromLogin, $params) {
        $params[0] = (int) $params[0];

        if ($params[0] > 150) {
            $this->sendErrorChat($fromLogin, 'Parameter value too big. Max players is limited to 150.');
            return;
        }
        try {
            $this->connection->setMaxPlayers($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets server maximum players to %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerMaxSpectators($fromLogin, $params) {
        $params[0] = (int) $params[0];

        try {
            $this->connection->setMaxSpectators($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets server maximum spectators to %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerPassword($fromLogin, $params) {
        try {
            $this->connection->setServerPassword($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets/unsets new server password.');
            $this->exp_chatSendServerMessage($fromLogin, '%admina_action% New password: %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setSpecPassword($fromLogin, $params) {
        try {
            $this->connection->setServerPasswordForSpectator($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets/unsets new spectator password.');
            $this->exp_chatSendServerMessage($fromLogin, '%admina_action% New spectator password: %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setRefereePassword($fromLogin, $params) {
        try {
            $this->connection->setRefereePassword($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets/unsets new referee password.');
            $this->exp_chatSendServerMessage($fromLogin, '%admina_action% New referee password: %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setserverchattime($fromLogin, $params) {
        $timelimit = explode(":", trim($params[0]));

        $newLimit = intval($timelimit[0] * 60 * 1000) + ($timelimit[1] * 1000) - 8000;
        if ($newLimit < 0)
            $newLimit = 0;
        try {
            $this->connection->SetChatTime($newLimit);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets new chat time limit of %variable%' . $params[0] . '$0ae minutes.');
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerMapDownload($fromLogin, $params) {

        $bool = false;
        if ($params[0] == 'true' || $params[0] == 'false') {
            if ($params[0] == 'true')
                $bool = true;
            if ($params[0] == 'false')
                $bool = false;
        }
        else {
            $this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameter for the command is either true or false.');
            return;
        }

        try {
            $this->connection->allowMapDownload($bool);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% set allow download challenge to %variable%' . $param1);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setHideServer($fromLogin, $params) {
        $validValues = array("1", "0", "2", "all", "visible", "both", "nations", "off", "hidden");
        if (in_array(strtolower($params[0]), $validValues, true)) {
            if ($params[0] == 'off' || $params[0] == 'visible')
                $output = 0;
            if ($params[0] == 'all' || $params[0] == 'both' || $params[0] == 'hidden')
                $output = 1;
            if ($params[0] == 'nations')
                $output = 2;
            if (is_numeric($params[0]))
                $output = $params[0];
        }
        else {
            $this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameters for command are: 0,1,2,visible,hidden,nations.');
            return;
        }
        try {
            $this->connection->setHideServer($output);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% set Hide Server to %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function stopDedicated($fromLogin, $params) {
        $this->connection->stopServer();
    }

    function stopManiaLive($fromLogin, $params) {
        die();
    }

    function skipTrack($fromLogin, $params) {
        try {
            $this->connection->nextMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% skips challenge!');
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function restartTrack($fromLogin, $params) {
        try {
            $this->connection->restartMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($fromLogin);
            Dispatcher::dispatch(new events\onMaxAdmin_Restart());
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% restarts challenge!');
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setGameMode($fromLogin, $params) {
        $gamemode = NULL;

        if (is_numeric($params[0])) {
            $gamemode = $params[0];
        } else {
            if (strtolower($param1) == "script")
                $gamemode = \DedicatedApi\Structures\GameInfos::GAMEMODE_SCRIPT;
            if (strtolower($param1) == "rounds")
                $gamemode = \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS;
            if (strtolower($param1) == "timeattack" || strtolower($param1) == "ta")
                $gamemode = \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK;
            if (strtolower($param1) == "team")
                $gamemode = \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM;
            if (strtolower($param1) == "laps")
                $gamemode = \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS;
            if (strtolower($param1) == "stunts")
                $gamemode = \DedicatedApi\Structures\GameInfos::GAMEMODE_STUNTS;
            if (strtolower($param1) == "cup")
                $gamemode = \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP;
            if ($gamemode === NULL) {
                $this->sendErrorChat($fromLogin, 'Usage: /admin set game mode script,team,ta,rounds,laps,stunts,cup ');
                return;
            }
        }

        try {
            $this->connection->setGameMode($gamemode);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets game mode to %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setAllWarmUpDuration($fromLogin, $params) {

        try {
            $this->connection->setAllWarmUpDuration($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% sets all game modes warmup duration to %variable%' . $params[0]);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
            return;
        }
    }

    function setDisableRespawn($fromLogin, $params) {
        if ($params[0] == 'true' || $params[0] == 'false') {
            if ($params[0] == 'true')
                $bool = false; // reverse the order as the command is for disable;
            if ($params[0] == 'false')
                $bool = true; // ^^
        }
        else {
            $this->sendErrorChat($fromLogin, '%adminerror%Invalid parameter. Correct parameter for the command is either true or false.');
            return;
        }

        try {
            $this->connection->setDisableRespawn($bool);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('%admina_action%Admin %variable%' . $admin->nickName . '$z$s%admina_action% set allow respawn to %variable%' . $param1);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

}

?>
