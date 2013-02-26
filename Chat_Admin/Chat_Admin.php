<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Windows\GenericPlayerList;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls\BannedPlayeritem;

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
        $cmd = AdminGroups::addAdminCommand('help', $this, 'help', '');
        $cmd->setHelp(_('help'));        

        $cmd = AdminGroups::addAdminCommand('player kick', $this, 'kick', Permission::player_kick); //
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

        $cmd = AdminGroups::addAdminCommand('player get banlist', $this, 'getBanlist', 'player_black');
        $cmd->setHelp('shows the current banlist of players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "getbanlist");

        $cmd = AdminGroups::addAdminCommand('player clean blacklist', $this, 'cleanBlacklist', 'player_black');
        $cmd->setHelp('clears the blacklist of players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "cleanblacklist");

        $cmd = AdminGroups::addAdminCommand('player get blacklist', $this, 'getBlacklist', 'player_black');
        $cmd->setHelp('shows the current banlist of players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "getblacklist");

        $cmd = AdminGroups::addAdminCommand('player get guestlist', $this, 'getGuestlist', 'player_black');
        $cmd->setHelp('shows the current guest of players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "getguestlist");

        $cmd = AdminGroups::addAdminCommand('player get ignorelist', $this, 'getIgnorelist', 'player_ignore');
        $cmd->setHelp('shows the current ignorelist of players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "getignorelist");

        $cmd = AdminGroups::addAdminCommand('player remove black', $this, 'unBlacklist', 'player_black');
        $cmd->setHelp('Removes the player from the black list');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "unBlack");

        $cmd = AdminGroups::addAdminCommand('player spec', $this, 'forceSpec', 'player_spec');
        $cmd->setHelp('Forces the player to become spectator');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "spec");

        $cmd = AdminGroups::addAdminCommand('player ignore', $this, 'ignore', 'player_ignore');
        $cmd->setHelp('Adds player to ignore list and mutes him from the chat');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "ignore");

        $cmd = AdminGroups::addAdminCommand('player unignore', $this, 'unignore', 'player_ignore');
        $cmd->setHelp('Removes player to ignore list and allows him to chat');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "unignore");

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
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setmaxplayers");

        $cmd = AdminGroups::addAdminCommand('set server maxspectators', $this, 'setServerMaxSpectators', 'server_maxspec');
        $cmd->setHelp('Sets a new maximum of spectator');
        $cmd->setHelp('Sets the maximum number of players who can spectate the players on this server.');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setmaxspecs");

        $cmd = AdminGroups::addAdminCommand('set server chattime', $this, 'setserverchattime', 'server_chattime');
        $cmd->setHelp('Sets the Chat time duration.');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setchattime");

        $cmd = AdminGroups::addAdminCommand('set server hide', $this, 'setHideServer', 'server_admin');
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean::getInstance());
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "sethideserver");

        $cmd = AdminGroups::addAdminCommand('set server mapdownload', $this, 'setServerMapDownload', 'server_admin');
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean::getInstance());
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "setMapDownload");

        $cmd = AdminGroups::addAdminCommand('stop dedicated', $this, 'stopDedicated', 'server_admin');
        $cmd->getHelp("Stops this server.");
        AdminGroups::addAlias($cmd, 'stop ml');

        $cmd = AdminGroups::addAdminCommand('stop manialive', $this, 'stopManiaLive', 'server_admin');
        $cmd->getHelp("Stops the Manialive instance running on for the server.");
        AdminGroups::addAlias($cmd, 'stop dedi');

        /* 		 
         * ************************* 
         * Concerning Game Settings 
         * *************************
         */
        $cmd = AdminGroups::addAdminCommand('skip', $this, 'skipMap', 'map_skip');
        $cmd->setHelp("Skips the current track");
        AdminGroups::addAlias($cmd, 'skipmap');
        AdminGroups::addAlias($cmd, 'next');
        AdminGroups::addAlias($cmd, 'nextmap');

        $cmd = AdminGroups::addAdminCommand('restart', $this, 'restartMap', 'map_skip');
        AdminGroups::addAlias($cmd, 'res');
        AdminGroups::addAlias($cmd, 'resmap');
        AdminGroups::addAlias($cmd, 'restartmap');


        $cmd = AdminGroups::addAdminCommand('set game mode', $this, 'setGameMode', 'game_gamemode');
        $cmd->setHelp('Sets next mode {ta,rounds,team,laps,stunts,cup}');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, 'setgamemode');

        $cmd = AdminGroups::addAdminCommand('set game AllWarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
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
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());

        //rounds
        $cmd = AdminGroups::addAdminCommand('set game rounds end', $this, 'forceEndRound', 'map_roundEnd');
        AdminGroups::addAlias($cmd, 'endround');
        AdminGroups::addAlias($cmd, 'er');

        $cmd = AdminGroups::addAdminCommand('set game rounds PointsLimit', $this, 'setRoundPointsLimit', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, 'rpoints');

        $cmd = AdminGroups::addAdminCommand('set game rounds ForcedLaps', $this, 'setRoundForcedLaps', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, 'setRoundForcedLaps');

        $cmd = AdminGroups::addAdminCommand('set game rounds NewRules', $this, 'setUseNewRulesRound', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean::getInstance());
        AdminGroups::addAlias($cmd, 'setUseNewRulesRound');

        $cmd = AdminGroups::addAdminCommand('set game rounds WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());

        //laps
        $cmd = AdminGroups::addAdminCommand('set game laps TimeLimit', $this, 'setLapsTimeLimit', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setLapsTimeLimit");

        $cmd = AdminGroups::addAdminCommand('set game laps nbLaps', $this, 'setNbLaps', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setNbLaps");

        $cmd = AdminGroups::addAdminCommand('set game laps FinishTimeOut', $this, 'setFinishTimeout', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setFinishTimeout");


        $cmd = AdminGroups::addAdminCommand('set game laps WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());

        //team
        $cmd = AdminGroups::addAdminCommand('set game team PointsLimit', $this, 'setTeamPointsLimit', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setTeamPointsLimit");


        $cmd = AdminGroups::addAdminCommand('set game team maxPoints', $this, 'setMaxPointsTeam', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setMaxPointsTeam");

        $cmd = AdminGroups::addAdminCommand('set game team NewRules', $this, 'setUseNewRulesTeam', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean::getInstance());
        AdminGroups::addAlias($cmd, "setUseNewRulesTeam");

        $cmd = AdminGroups::addAdminCommand('set game team forcePlayer', $this, 'forcePlayerTeam', 'game_settings');
        $cmd->setMinParam(2);
        $cmd->addchecker(2, \ManiaLivePlugins\eXpansion\AdminGroups\types\Arraylist::getInstance()->items("0,1,red,blue"));
        AdminGroups::addAlias($cmd, "forcePlayerTeam");


        $cmd = AdminGroups::addAdminCommand('set game team WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());

        //cup
        $cmd = AdminGroups::addAdminCommand('set game cup PointsLimit', $this, 'setCupPointsLimit', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setCupPointsLimit");

        $cmd = AdminGroups::addAdminCommand('set game cup RoundsPerMap', $this, 'setCupRoundsPerMap', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setCupRoundsPerMap");

        $cmd = AdminGroups::addAdminCommand('set game cup WarmUpDuration', $this, 'setCupWarmUpDuration', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setCupWarmUpDuration");

        $cmd = AdminGroups::addAdminCommand('set game cup NbWinners', $this, 'setCupNbWinners', 'game_settings');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setCupNbWinners");

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

    function help($fromLogin, $param) {
        $adminGroups = AdminGroups::getInstance();
        $commands = $adminGroups->getAdminCommands();
        print_r($commands);        
        
    }

    function setCupNbWinners($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupWarmUpDuration(intval($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets cup winners to#variable# %s #admina_action#.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setCupWarmUpDuration($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupWarmUpDuration(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets use new cup points limit to#variable# %s #admina_action#.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setCupRoundsPerMap($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupRoundsPerMap(intval($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets use new cup points limit to#variable# %s #admina_action#.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setCupPointsLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupPointsLimit(intval($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets use new cup points limit to#variable# %s #admina_action#.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function forcePlayerTeam($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, _('#admina_action#Player #variable# %s #admina_action#doesn\' exist.', $params[0]));
            return;
        }
        /** @todo check which if red == 1 and blue == 0 */
        if ($params[1] == "red")
            $params[1] = 1;
        if ($params[1] == "blue")
            $params[1] = 0;

        try {
            $this->connection->forcePlayerTeam($player, intval($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#forces player #variable# %s #admina_action# to team#variable# %s #admina_action#.', $admin->nickName, $player->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setUseNewRulesTeam($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setMaxPointsTeam(filter_var($params[0], FILTER_VALIDATE_BOOLEAN));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets use new team rules to#variable# %s #admina_action#.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setMaxPointsTeam($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setMaxPointsTeam(intval($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets Team max points to#variable# %s #admina_action#.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setTeamPointsLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setTeamPointsLimit(intval($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets Team points limit to#variable# %s #admina_action#.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setFinishTimeout($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setFinishTimeout(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new finish timeout to#variable# %s #admina_action#minutes.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setNbLaps($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setNbLaps(intval($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new number of laps to#variable# %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setLapsTimeLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setLapsTimeLimit(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new laps timelimit to#variable# %s #admina_action#minutes.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setRoundPointsLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setRoundPointsLimit(int_val($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets rounds points limits to#variable# %s.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function forceEndRound($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->forceEndRound();
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#forces the round to end.', $admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setUseNewRulesRound($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setUseNewRulesRound(filter_var($params[0], FILTER_VALIDATE_BOOLEAN));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new round rules to#variable# %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setRoundForcedLaps($fromLogin, $params) {

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setRoundForcedLaps(int_val($params[0]));
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new round forced laps to#variable# %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function blacklist($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, _('#admina_action#Player #variable# %s #admina_action#doesn\' exist.', $params[0]));
            return;
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->banAndBlackList($player, "", true);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin #variable# %s #admina_action#blacklists the player #variable# %s', $admin->nickName, $player->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanBlacklist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanBlackList();
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#cleans the blacklist.', $admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanBanlist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanBanList();
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#cleans the banlist.', $admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanIgnorelist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanIgnoreList();
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#cleans the ignorelist.', $admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    public function unBlacklist($fromLogin, $params) {

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->unBlackList($params[0]);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#unblacklists the player %s', $admin->nickName, $params[0]));
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
            $this->exp_chatSendServerMessage(_('#admina_action#Admin #variable# %s #admina_action# bans the player#variable# %s', $admin->nickName, $nickname));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function ignore($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if (is_object($player)) {
            $nickname = $player->nickName;
        } else {
            $nickname = $params[0];
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->ignore($params[0]);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin #variable# %s #admina_action# ignores the player#variable# %s', $admin->nickName, $nickname));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function unban($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);

        try {
            $this->connection->unBan($params[0]);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#unbans the player %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function unignore($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);

        try {
            $this->connection->unIgnore($params[0]);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#unignores the player %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function kick($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, _('#admina_action#Player #variable# %s doesn\' exist.', $params[0]));
            return;
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->kick($player);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#kicks the player#variable# %s', $admin->nickName, $player->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function forceSpec($fromLogin, $params) {
        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, _('#admina_action#Player #variable# %s doesn\' exist.', $params[0]));
            return;
        }
        try {
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->connection->forceSpectator($player, 1);
            $this->connection->forceSpectator($player, 0);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#Forces the player #variable# %s #admina_action#to Spectator.', $admin->nickName, $player->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    public function sendErrorChat($login, $message) {
        $this->exp_chatSendServerMessage('#admin_error#' . $message, $login);
    }

    function setServerName($fromLogin, $params) {
        $name = implode(" ", $params);
        try {
            $this->connection->setServerName($name);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action# sets new server name:#variable# %s', $admin->nickName, $name));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerComment($fromLogin, $params) {
        $comment = implode(" ", $params);
        try {
            $this->connection->setServerName($comment);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new server comment:#variable# %s', $admin->nickName, $comment));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerMaxPlayers($fromLogin, $params) {
        $params[0] = (int) $params[0];
        try {
            $this->connection->setMaxPlayers($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets server maximum players to#variable# %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerMaxSpectators($fromLogin, $params) {
        $params[0] = (int) $params[0];
        try {
            $this->connection->setMaxSpectators($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets server maximum spectators to#variable# %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerPassword($fromLogin, $params) {
        try {
            $this->connection->setServerPassword($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin #variable# %s #admina_action# sets/unsets new server password.', $admin->nickName));
            $this->exp_chatSendServerMessage(_('#admina_action#New server password:#variable# %s', $params[0]), $fromLogin);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setSpecPassword($fromLogin, $params) {
        try {
            $this->connection->setServerPasswordForSpectator($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets/unsets new spectator password.', $admin->nickName));
            $this->exp_chatSendServerMessage(_('#admina_action#New spectator password:#variable# %s', $params[0]), $fromLogin);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setRefereePassword($fromLogin, $params) {
        try {
            $this->connection->setRefereePassword($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets/unsets new referee password.', $admin->nickName));
            $this->exp_chatSendServerMessage(_('#admina_action#New referee password:#variable# %s', $params[0]), $fromLogin);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setserverchattime($fromLogin, $params) {
        $newLimit = \ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($params[0]) - 8000;

        if ($newLimit < 0)
            $newLimit = 0;

        try {
            $this->connection->SetChatTime($newLimit);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new chat time limit of #variable# %s #admina_action#minutes.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setTAlimit($fromLogin, $params) {       
        
        try {
            $this->connection->setTimeAttackLimit(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($params[0]));
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets new time limit of #variable# %s #admina_action#minutes.', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            print $e->getMessage();
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
            $this->sendErrorChat($fromLogin, _('Invalid parameter. Correct parameter for the command is either true or false.'));
            return;
        }

        try {
            $this->connection->allowMapDownload($bool);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#set allow download maps to#variable# %s', $admin->nickName, $param1));
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
                $output = intval($params[0]);
        }
        else {
            $this->sendErrorChat($fromLogin, _('Invalid parameter. Correct parameters for command are: 0,1,2,visible,hidden,nations.'));
            return;
        }
        try {
            $this->connection->setHideServer($output);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#set Hide Server to#variable# %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function stopDedicated($fromLogin, $params) {
        try {
            $this->connection->stopServer();
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function stopManiaLive($fromLogin, $params) {
        die();
    }

    function skipMap($fromLogin, $params) {
        try {
            $this->connection->nextMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#skips the challenge!', $admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function restartMap($fromLogin, $params) {
        try {
            $this->connection->restartMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($fromLogin);
            Dispatcher::dispatch(new events\onMaxAdmin_Restart());
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#restarts the challenge!', $admin->nickName));
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
                $this->sendErrorChat($fromLogin, _('Invalid parameter. Valid parameteres are: script,team,timeattack,ta,rounds,laps,stunts,cup.'));
                return;
            }
        }

        try {
            $this->connection->setGameMode($gamemode);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin#variable# %s #admina_action#sets game mode to#variable# %s', $admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setAllWarmUpDuration($fromLogin, $params) {

        try {
            $this->connection->setAllWarmUpDuration($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage(_('#admina_action#Admin #variable# %s #admina_action#sets all game modes warmup duration to#variable# %s', $admin->nickName, $params[0]));
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
            $this->sendErrorChat($fromLogin, _('Invalid parameter. Correct parameter for the command is either true or false.'));
            return;
        }

        try {
            $this->connection->setDisableRespawn($bool);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admina_action#Admin#variable# %s #admina_action#set allow respawn to #variable# %s', $admin->nickName, $param1);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    /* Graphical Methods */

    function getBanList($login) {
        GenericPlayerList::Erase($login);

        try {
            $window = GenericPlayerList::Create($login);
            $window->setTitle(_('Banned Players on the server'));
            $indexNumber = 0;
            $items = array();

            /**
             * @var \DedicatedApi\Structures\Player 
             */
            foreach ($this->connection->getBanList(-1, 0) as $player) {
                $items[] = new BannedPlayeritem($indexNumber, $player, $this);
            }
            $window->populateList($items);
            $window->setSize(120, 100);
            $window->centerOnScreen();
            $window->show();
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function getBlackList($login) {
        GenericPlayerList::Erase($login);

        try {
            $window = GenericPlayerList::Create($login);
            $window->setTitle(_('Blacklisted Players on the server'));
            $indexNumber = 0;
            $items = array();

            /**
             * @var \DedicatedApi\Structures\Player 
             */
            foreach ($this->connection->getBlackList(-1, 0) as $player) {
                $items[] = new BlacklistPlayeritem($indexNumber, $player, $this);
            }
            $window->populateList($items);
            $window->setSize(120, 100);
            $window->centerOnScreen();
            $window->show();
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function getGuestList($login) {
        GenericPlayerList::Erase($login);

        try {
            $window = GenericPlayerList::Create($login);
            $window->setTitle(_('Guest Players on the server'));
            $indexNumber = 0;
            $items = array();

            /**
             * @var \DedicatedApi\Structures\Player 
             */
            foreach ($this->connection->getGuestList(-1, 0) as $player) {
                $items[] = new GuestPlayeritem($indexNumber, $player, $this);
            }
            $window->populateList($items);
            $window->setSize(120, 100);
            $window->centerOnScreen();
            $window->show();
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function getIgnoreList($login) {
        GenericPlayerList::Erase($login);

        try {
            $window = GenericPlayerList::Create($login);
            $window->setTitle(_('Ignored Players on the server'));
            $indexNumber = 0;
            $items = array();

            /**
             * @var \DedicatedApi\Structures\Player 
             */
            foreach ($this->connection->getIgnoreList(-1, 0) as $player) {
                $items[] = new IgnoredPlayeritem($indexNumber, $player, $this);
            }
            $window->populateList($items);
            $window->setSize(120, 100);
            $window->centerOnScreen();
            $window->show();
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

}

?>
