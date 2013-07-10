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

        $admingroup = AdminGroups::getInstance();
        $this->registerChatCommand("ta", "support_fastTa", -1, true, $admingroup->get());
        $this->registerChatCommand("laps", "support_fastLaps", -1, true, $admingroup->get());
        $this->registerChatCommand("rounds", "support_fastRounds", -1, true, $admingroup->get());
        $this->registerChatCommand("cup", "support_fastCup", -1, true, $admingroup->get());
        $this->registerChatCommand("team", "support_fastTeam", -1, true, $admingroup->get());

        /* 		
         * ******************* 
         * Concerning Players 
         * *******************
         * 
         * 
         */

        $cmd = AdminGroups::addAdminCommand('player kick', $this, 'kick', 'player_kick'); //
        $cmd->setHelp('kick the player from the server');
        $cmd->setHelpMore('$w/admin player kick #login$z will kick the player from the server.
A kicked player may return to the server whanever he desires.');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "kick"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player ban', $this, 'ban', 'player_black');
        $cmd->setHelp('Ban the player from the server');
        $cmd->setHelpMore('$w/admin player ban #login$z will ban  the player from the server. 
He may not return until the server is restarted');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "ban"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player black', $this, 'blacklist', 'player_black');
        $cmd->setHelp('Add the player to the black list');
        $cmd->setHelpMore('$w/admin player black #login$z will add the player to the blacklist of this server. 
He may not return until the server blacklist file is deleted. 
Other server might use the same blacklist file!!');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "black"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player remove ban', $this, 'unban', 'player_black');
        $cmd->setHelp('Removes the ban of the player')
                ->addLineHelpMore('$w/admin player remove ban #login$z will remove the ban of the player from this server')
                ->addLineHelpMore('He may rejoin the server after this.')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "unban"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player clean banlist', $this, 'cleanBanlist', 'player_black');
        $cmd->setHelp('clears the banlist of players')
                ->addLineHelpMore('Will completeley clear the banlist.')
                ->addLineHelpMore('All banned players will be able to rejoin the server.')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "cleanbanlist"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player get banlist', $this, 'getBanlist', 'player_black');
        $cmd->setHelp('shows the current banlist of players')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "getbanlist");

        $cmd = AdminGroups::addAdminCommand('player clean blacklist', $this, 'cleanBlacklist', 'player_black');
        $cmd->setHelp('clears the blacklist of players')
                ->addLineHelpMore('Will completeley clear the blackList.')
                ->addLineHelpMore('All blacklist players will be able to rejoin the server.')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "cleanblacklist");

        $cmd = AdminGroups::addAdminCommand('player get blacklist', $this, 'getBlacklist', 'player_black');
        $cmd->setHelp('shows the current banlist of players')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "getblacklist");

        $cmd = AdminGroups::addAdminCommand('player get guestlist', $this, 'getGuestlist', 'player_guest');
        $cmd->setHelp('shows the current guest of players')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "getguestlist");

        $cmd = AdminGroups::addAdminCommand('player get ignorelist', $this, 'getIgnorelist', 'player_ignore');
        $cmd->setHelp('shows the current ignorelist of players')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "getignorelist");

        $cmd = AdminGroups::addAdminCommand('player remove black', $this, 'unBlacklist', 'player_black');
        $cmd->setHelp('Removes the player from the black list')
                ->addLineHelpMore('$w/admin player remove black #login$z will remove the player from the servers blacklist')
                ->addLineHelpMore('He may rejoin the server after this.')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "unBlack"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player spec', $this, 'forceSpec', 'player_spec');
        $cmd->setHelp('Forces the player to become spectator')
                ->addLineHelpMore('$w/admin player spec #login$z The playing player will be forced to become a spectator')
                ->addLineHelpMore('If the max spectators is reached it the player won\'t become a spectator')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "spec"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player ignore', $this, 'ignore', 'player_ignore');
        $cmd->setHelp('Adds player to ignore list and mutes him from the chat')
                ->addLineHelpMore('$w/admin player ignore #login$z will ignore the players chat')
                ->addLineHelpMore('This player won\'t be able to communicate with other players.')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "ignore"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('player unignore', $this, 'unignore', 'player_ignore');
        $cmd->setHelp('Removes player to ignore list and allows him to chat')
                ->addLineHelpMore('$w/admin player unignore #login$z will allow this player to use the chat again')
                ->addLineHelpMore('This player will be able to communicate with other players')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "unignore"); // xaseco & fast
        //ENDSUPER

        /*
         * *************************** 
         * Concerning Server Settings 
         * ***************************
         */
        $cmd = AdminGroups::addAdminCommand('get server planets', $this, 'getServerPlanets', 'server_admin');
        $cmd->setHelp('Gets the serveraccount planets amount')
                ->addLineHelpMore('$w/admin planets $zreturn the planets amount on server account.')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "planets"); // fast

        $cmd = AdminGroups::addAdminCommand('set server pay', $this, 'pay', 'server_pay');
        $cmd->setHelp('Pays out planets')
                ->addLineHelpMore('$w/admin pay #login #amount$z pays amount of planets to login')
                ->setMinParam(2);
        $cmd->addchecker(2, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "pay"); // xaseco

        $cmd = AdminGroups::addAdminCommand('set server name', $this, 'setServerName', 'server_name');
        $cmd->setHelp('Changes the name of the server')
                ->addLineHelpMore('$w/admin set server name #name$z will change the server name.')
                ->addLineHelpMore('This servers name will be changed.')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "setservername"); // xaseco
        AdminGroups::addAlias($cmd, "name"); // fast

        $cmd = AdminGroups::addAdminCommand('set server comment', $this, 'setServerComment', 'server_comment');
        $cmd->setHelp('Changes the server comment')
                ->addLineHelpMore('$w/admin set server comment #comment$z will change the server comment.')
                ->addLineHelpMore('This servers comment will be changed.')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "setcomment"); // xaseco
        AdminGroups::addAlias($cmd, "comment"); // fast

        $cmd = AdminGroups::addAdminCommand('set server player password', $this, 'setServerPassword', 'server_password');
        $cmd->setHelp('Changes the player password')
                ->setHelpMore('$w/admin set server spec password #pwd$z will change the password needed for players to connect to this server')
                ->setMinParam(0);
        AdminGroups::addAlias($cmd, "setpwd"); // xaseco
        AdminGroups::addAlias($cmd, "pass"); // fast

        $cmd = AdminGroups::addAdminCommand('set server spec password', $this, 'setSpecPassword', 'server_specpwd');
        $cmd->setHelp('Changes the spectator password')
                ->setHelpMore('$w/admin set server spec password #pwd$z will change the password needed for spectators to connect to this server')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "setspecpwd"); // xaseco
        AdminGroups::addAlias($cmd, "spectpass"); // fast


        $cmd = AdminGroups::addAdminCommand('set server ref password', $this, 'setSpecPassword', 'server_specpwd');
        $cmd->setHelp('Changes the Referee password')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, "setrefpwd"); // xaseco


        $cmd = AdminGroups::addAdminCommand('set server maxplayers', $this, 'setServerMaxPlayers', 'server_maxplayer');
        $cmd->setHelp('Sets a new maximum of players')
                ->setHelpMore('Sets the maximum number of players who can play on this server.')
                ->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setmaxplayers"); //xaseco
        AdminGroups::addAlias($cmd, "maxplayers"); // fast

        $cmd = AdminGroups::addAdminCommand('set server maxspectators', $this, 'setServerMaxSpectators', 'server_maxspec');
        $cmd->setHelp('Sets a new maximum of spectator')
                ->setHelp('Sets the maximum number of players who can spectate the players on this server.');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setmaxspecs"); // xaseco
        AdminGroups::addAlias($cmd, "maxspec"); // fast

        $cmd = AdminGroups::addAdminCommand('set server chattime', $this, 'setserverchattime', 'server_chattime');
        $cmd->setHelp('Sets the Chat time duration.')
                ->addLineHelpMore('This is the time players get between the challenge end and the the new map')
                ->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setchattime"); // xaseco
        AdminGroups::addAlias($cmd, "chattime"); // fast

        $cmd = AdminGroups::addAdminCommand('set server hide', $this, 'setHideServer', 'server_admin');
        $cmd->setHelp('Allows you to hide or show the server to players')
                ->addLineHelpMore('$w\admin set server hide true$z Will hide the server from other players. Players would need to have the servers in their favorites or need to know the server login ')
                ->addLineHelpMore('$w\admin set server hide false$z Will make the server visible to any player')
                ->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean::getInstance());
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "sethideserver");

        $cmd = AdminGroups::addAdminCommand('set server mapdownload', $this, 'setServerMapDownload', 'server_admin');
        $cmd->setHelp('Will allow players to download maps they are playing from the server.')
                ->addLineHelpMore('$w\admin set server mapdownload true$z will allow the maps to be downloaded.')
                ->addLineHelpMore('$w\admin set server mapdownload false$z will not allow players to download the maps of this server.')
                ->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean::getInstance());
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "setMapDownload");

        $cmd = AdminGroups::addAdminCommand('stop dedicated', $this, 'stopDedicated', 'server_stopServer');
        $cmd->getHelp("Stops this server. Manialive will crush after this");
        AdminGroups::addAlias($cmd, 'stop ml');

        $cmd = AdminGroups::addAdminCommand('stop manialive', $this, 'stopManiaLive', 'server_stopManialive');
        $cmd->getHelp("Stops the Manialive instance running on for the server.");
        AdminGroups::addAlias($cmd, 'stop dedi');

        /* 		 
         * ************************* 
         * Concerning Game Settings 
         * *************************
         */
        $cmd = AdminGroups::addAdminCommand('skip', $this, 'skipMap', 'map_skip');
        $cmd->setHelp("Skips the current track");
        AdminGroups::addAlias($cmd, 'skip'); // shortcut
        AdminGroups::addAlias($cmd, 'skipmap'); // xaseco
        AdminGroups::addAlias($cmd, 'next'); // fast
        AdminGroups::addAlias($cmd, 'nextmap');

        $cmd = AdminGroups::addAdminCommand('restart', $this, 'restartMap', 'map_restart');
        $cmd->setHelp("Restarts this map to allow you to replay the map");
        AdminGroups::addAlias($cmd, 'res'); // xaseco
        AdminGroups::addAlias($cmd, 'restart'); // fast
        AdminGroups::addAlias($cmd, 'restartmap'); //xaseco


        $cmd = AdminGroups::addAdminCommand('set game mode', $this, 'setGameMode', 'game_gamemode');
        $cmd->setHelp('Sets next mode {ta,rounds,team,laps,stunts,cup}')
                ->addLineHelpMore('$w\admin set game mode ta$z will change gamemode to TimeAttack.')
                ->addLineHelpMore('$w\admin set game mode rounds$z will change gamemode to Rounds mode.')
                ->addLineHelpMore('$w\admin set game mode team$z will change gamemode to Team mode.')
                ->addLineHelpMore('$w\admin set game mode laps$z will change gamemode to Laps mode.')
                ->addLineHelpMore('$w\admin set game mode cup$z will change gamemode to Cup mode.')
                ->addLineHelpMore('$w\admin set game mode stunts$z will change gamemode to Stunts mode.');
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, 'setgamemode'); //xaseco
        AdminGroups::addAlias($cmd, 'mode'); //fast

        $cmd = AdminGroups::addAdminCommand('set game AllWarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setHelp('Set the warmup duration at the begining of the maps for all gamemodes')
                ->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, 'setAllWarmUpDuration');

        $cmd = AdminGroups::addAdminCommand('set game disableRespawn', $this, 'setDisableRespawn', 'game_settings');
        $cmd->setHelp('Will disable the respawn capabilities of the players')
                ->addLineHelpMore('$w/admin set game disableRespawn true$z will force the players to restart the map when they respaw')
                ->addLineHelpMore('$w/admin set game disableRespawn false$z player that respaw will return back to the last checkpoint')
                ->addLineHelpMore("\n" . 'A player respaws when he clicks on backspace on his keyboard')
                ->setMinParam(1);
        AdminGroups::addAlias($cmd, 'setDisableRespawn');

        //TimeAttack
        $cmd = AdminGroups::addAdminCommand('set game ta timelimit', $this, 'setTAlimit', 'game_settings');
        $cmd->setHelp('Changes the time limit of Time Attack mode.')
                ->addLineHelpMore('$w/admin set game ta timelimit #num$z will change the play time of a map')
                ->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, 'setTAlimit');

        $cmd = AdminGroups::addAdminCommand('set game ta WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setHelp('Changes the warmup duration of Time Attack mode only')
                ->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());

        //rounds
        $cmd = AdminGroups::addAdminCommand('set game rounds end', $this, 'forceEndRound', 'map_endRound');
        $cmd->setHelp('Ends a round. Only work in round mode');
        AdminGroups::addAlias($cmd, 'end');  // fast
        AdminGroups::addAlias($cmd, 'endround'); // xaseco
        AdminGroups::addAlias($cmd, 'er'); // xaseco

        $cmd = AdminGroups::addAdminCommand('set game rounds PointsLimit', $this, 'setRoundPointsLimit', 'game_settings');
        $cmd->setHelp('Changes the points limit of rounds mode');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, 'rpoints');

        $cmd = AdminGroups::addAdminCommand('set game rounds ForcedLaps', $this, 'setRoundForcedLaps', 'game_settings');
        $cmd->setHelp('Forces laps in Rounds mode')
                ->addLineHelpMore('$w\admin set game rounds ForcedLaps #num$z will force multi laps maps lap number to the given value')
                ->addLineHelpMore('using 0 as number of laps will change the nb of laps to the default value')
                ->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, 'setRoundForcedLaps');

        $cmd = AdminGroups::addAdminCommand('set game rounds NewRules', $this, 'setUseNewRulesRound', 'game_settings');
        $cmd->setHelp('Allows you tu use new rules in rounds mode')
                ->addLineHelpMore('$w/admin set game rounds NewRules true$z will force the usage of new rules in rounds mode')
                ->addLineHelpMore('$w/admin set game rounds NewRules false$z will force the usage of old rules in rounds mode')
                ->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean::getInstance());
        AdminGroups::addAlias($cmd, 'setUseNewRulesRound');

        $cmd = AdminGroups::addAdminCommand('set game rounds WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setHelp('Changes the warmup duration of Rounds mode only')
                ->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());

        //laps
        $cmd = AdminGroups::addAdminCommand('set game laps TimeLimit', $this, 'setLapsTimeLimit', 'game_settings');
        $cmd->setHelp('Changes the limit of time players has to finish the track')
                ->setMinParam(1)
                ->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setLapsTimeLimit");

        $cmd = AdminGroups::addAdminCommand('set game laps nbLaps', $this, 'setNbLaps', 'game_settings');
        $cmd->setHelp('Changes the numbers of laps players need to do to finish the map');
        $cmd->setMinParam(1);
        $cmd->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());
        AdminGroups::addAlias($cmd, "setNbLaps");

        $cmd = AdminGroups::addAdminCommand('set game laps FinishTimeOut', $this, 'setFinishTimeout', 'game_settings');
        $cmd->setHelp('Changes the time that has a player to finish a map once 1 player has already finished the map')
                ->setMinParam(1)
                ->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms::getInstance());
        AdminGroups::addAlias($cmd, "setFinishTimeout");


        $cmd = AdminGroups::addAdminCommand('set game laps WarmUpDuration', $this, 'setAllWarmUpDuration', 'game_settings');
        $cmd->setHelp('Changes the warmup duration of laps mode only')
                ->setMinParam(1)
                ->addchecker(1, \ManiaLivePlugins\eXpansion\AdminGroups\types\Integer::getInstance());

        //team
        $cmd = AdminGroups::addAdminCommand('set game team PointsLimit', $this, 'setTeamPointsLimit', 'game_settings');
        $cmd->setHelp('Changes the points limit of team mode');
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
        new adapter\oliverde8HudMenu($this, $menu, $this->storage, $this->connection);
    }

    function support_fastTa($fromLogin, $text) {
        try {
            $params = explode(" ", $text);
            $command = array_shift($params);


            switch (strtolower($command)) {
                case "time":
                case "limit":
                case "timelimit":
                    $this->setTAlimit($fromLogin, $params);
                    break;
                case "wud":
                case "wu":
                case "warmupduration":
                    $this->setAllWarmUpDuration($fromLogin, $params);
                    break;
                default:
                    $this->exp_chatSendServerMessage("command not found", $fromLogin);
                    break;
            }
        } catch (\Exception $e) {
            
        }
    }

    function support_fastLaps($fromLogin, $text) {
        try {
            $params = explode(" ", $text);
            $command = array_shift($params);


            switch (strtolower($command)) {
                case "laps":
                case "nb":
                case "nblaps":
                    $this->setNbLaps($fromLogin, $params);
                    break;
                case "time":
                case "limit":
                case "timelimit":
                    $this->setLapsTimeLimit($fromLogin, $params);
                    break;
                case "wud":
                case "wu":
                case "warmupduration":
                    $this->setAllWarmUpDuration($fromLogin, $params);
                    break;
                case "fto":
                case "ftimeout":
                case "finishtimeout":
                    $this->setFinishTimeout($fromLogin, $params);
                    break;
                default:
                    $this->exp_chatSendServerMessage("command not found", $fromLogin);
                    break;
            }
        } catch (\Exception $e) {
            
        }
    }

    function support_fastRounds($fromLogin, $text) {
        try {
            $params = explode(" ", $text);
            $command = array_shift($params);

            switch (strtolower($command)) {
                case "pointslimit":
                    $this->setRoundPointsLimit($fromLogin, $params);
                    break;
                case "newrules":
                    $this->setUseNewRulesRound($fromLogin, $params);
                    break;
                case "wud":
                case "wu":
                case "warmupduration":
                    $this->setAllWarmUpDuration($fromLogin, $params);
                    break;
                case "fto":
                case "ftimeout":
                case "finishtimeout":
                    $this->setFinishTimeout($fromLogin, $params);
                    break;
                default:
                    $this->exp_chatSendServerMessage("command not found", $fromLogin);
                    break;
            }
        } catch (\Exception $e) {
            
        }
    }

    function support_fastCup($fromLogin, $text) {
        try {
            $params = explode(" ", $text);
            $command = array_shift($params);

            switch (strtolower($command)) {
                case "limit":
                case "pointslimit":
                    $this->setCupPointsLimit($fromLogin, $params);
                    break;
                case "rpm":
                case "rpc":
                case "rounds":
                case "roundspermap":
                    $this->setCupRoundsPerMap($fromLogin, $params);
                    break;
                case "nbwinners":
                case "nbwin":
                case "nbw":
                case "nb":
                    $this->setCupNbWinners($fromLogin, $params);
                    break;
                case "wud":
                case "wu":
                case "warmupduration":
                    $this->setAllWarmUpDuration($fromLogin, $params);
                    break;
                case "fto":
                case "ftimeout":
                case "finishtimeout":
                    $this->setFinishTimeout($fromLogin, $params);
                    break;
                default:
                    $this->exp_chatSendServerMessage("command not found", $fromLogin);
                    break;
            }
        } catch (\Exception $e) {
            
        }
    }

    function support_fastTeam($fromLogin, $text) {
        try {
            $params = explode(" ", $text);
            $command = array_shift($params);

            switch (strtolower($command)) {
                case "limit":
                case "pointslimit":
                    $this->setTeamPointsLimit($fromLogin, $params);
                    break;
                case "max":
                case "maxpoint":
                    $this->setMaxPointsTeam($fromLogin, $params);
                    break;
                case "newrules":
                    $this->setUseNewRulesTeam($fromLogin, $params);
                    break;
                case "wud":
                case "wu":
                case "warmupduration":
                    $this->setAllWarmUpDuration($fromLogin, $params);
                    break;
                case "fto":
                case "ftimeout":
                case "finishtimeout":
                    $this->setFinishTimeout($fromLogin, $params);
                    break;
                case "blue":
                    $this->setTeamBlue($fromLogin, $params);
                    break;
                case "red":
                    $this->setTeamRed($fromLogin, $params);
                    break;
            }
        } catch (\Exception $e) {
            
        }
    }

    function pay($fromLogin, $params) {
        try {
            $this->connection->pay($params[0], intval($params[1]), $params[0] . " Planets payed out from server " . $this->storage->server->name);
            $this->exp_chatSendServerMessage('#admin_action#Server just sent#variable# %s #admin_action#Planets to#variable# %s #admin_action#!', $fromLogin, array($params[1], $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function getServerPlanets($fromLogin, $params = null) {
        try {

            $this->exp_chatSendServerMessage('#admin_action#Server has #variable# %s #admin_action#Planets.', $fromLogin, array($this->connection->getServerPlanets()));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setTeamBlue($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->forcePlayerTeam($params[0], 0);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sends player#variable# %s #admin_action#to team $00fBlue.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setTeamRed($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->forcePlayerTeam($params[0], 1);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sends player#variable# %s #admin_action#to team $f00Red.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setCupNbWinners($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupWarmUpDuration(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets cup winners to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setCupWarmUpDuration($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupWarmUpDuration(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new cup points limit to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setCupRoundsPerMap($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupRoundsPerMap(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new cup points limit to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setCupPointsLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setCupPointsLimit(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new cup points limit to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function forcePlayerTeam($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, '#admin_action#Player #variable# %s #admin_action#doesn\' exist.', null, array($params[0]));
            return;
        }
        /** @todo check which if red == 1 and blue == 0 */
        if ($params[1] == "red")
            $params[1] = 1;
        if ($params[1] == "blue")
            $params[1] = 0;

        try {
            $this->connection->forcePlayerTeam($player, intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#forces player #variable# %s #admin_action# to team#variable# %s #admin_action#.', null, array($admin->nickName, $player->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setUseNewRulesTeam($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setMaxPointsTeam(filter_var($params[0], FILTER_VALIDATE_BOOLEAN));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new team rules to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setMaxPointsTeam($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setMaxPointsTeam(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets Team max points to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setTeamPointsLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setTeamPointsLimit(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets Team points limit to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setFinishTimeout($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setFinishTimeout(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new finish timeout to#variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setNbLaps($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setNbLaps(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new number of laps to#variable# %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setLapsTimeLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setLapsTimeLimit(time_TMtoMS($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new laps timelimit to#variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setRoundPointsLimit($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setRoundPointsLimit(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets rounds points limits to#variable# %s.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function forceEndRound($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->forceEndRound();
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#forces the round to end.', null, array($admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setUseNewRulesRound($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setUseNewRulesRound(filter_var($params[0], FILTER_VALIDATE_BOOLEAN));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new round rules to#variable# %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setRoundForcedLaps($fromLogin, $params) {

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->setRoundForcedLaps(intval($params[0]));
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new round forced laps to#variable# %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function blacklist($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, '#admin_action#Player #variable# %s #admin_action#doesn\' exist.', null, array($params[0]));
            return;
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->banAndBlackList($player, "", true);
            $this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action#blacklists the player #variable# %s', null, array($admin->nickName, $player->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanBlacklist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanBlackList();
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#cleans the blacklist.', null, array($admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanBanlist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanBanList();
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#cleans the banlist.', null, array($admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function cleanIgnorelist($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->cleanIgnoreList();
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#cleans the ignorelist.', null, array($admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    public function unBlacklist($fromLogin, $params) {

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->unBlackList($params[0]);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#unblacklists the player %s', null, array($admin->nickName, $params[0]));
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
            $this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action# bans the player#variable# %s', null, array($admin->nickName, $nickname));
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
            $this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action# ignores the player#variable# %s', null, array($admin->nickName, $nickname));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function unban($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);

        try {
            $this->connection->unBan($params[0]);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#unbans the player %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function unignore($fromLogin, $params) {
        $admin = $this->storage->getPlayerObject($fromLogin);

        try {
            $this->connection->unIgnore($params[0]);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#unignores the player %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function kick($fromLogin, $params) {

        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, '#admin_action#Player #variable# %s doesn\' exist.', null, array($params[0]));
            return;
        }

        $admin = $this->storage->getPlayerObject($fromLogin);
        try {
            $this->connection->kick($player);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#kicks the player#variable# %s', null, array($admin->nickName, $player->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function forceSpec($fromLogin, $params) {
        $player = $this->storage->getPlayerObject($params[0]);
        if ($player == null) {
            $this->sendErrorChat($fromLogin, '#admin_action#Player #variable# %s doesn\' exist.', null, array($params[0]));
            return;
        }
        try {
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->connection->forceSpectator($player, 1);
            $this->connection->forceSpectator($player, 0);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#Forces the player #variable# %s #admin_action#to Spectator.', null, array($admin->nickName, $player->nickName));
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
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action# sets new server name:#variable# %s', null, array($admin->nickName, $name));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerComment($fromLogin, $params) {
        $comment = implode(" ", $params);
        try {
            $this->connection->setServerComment($comment);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new server comment:#variable# %s', null, array($admin->nickName, $comment));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerMaxPlayers($fromLogin, $params) {
        $params[0] = (int) $params[0];
        try {
            $this->connection->setMaxPlayers($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets server maximum players to#variable# %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerMaxSpectators($fromLogin, $params) {
        $params[0] = (int) $params[0];
        try {
            $this->connection->setMaxSpectators($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets server maximum spectators to#variable# %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setServerPassword($fromLogin, $params) {        
        try {
            $this->connection->setServerPassword($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action# sets/unsets new server password.', null, array($admin->nickName));
            $this->exp_chatSendServerMessage('#admin_action#New server password:#variable# %s', null, array($params[0]), $fromLogin);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setSpecPassword($fromLogin, $params) {
        try {
            $this->connection->setServerPasswordForSpectator($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets/unsets new spectator password.', null, array($admin->nickName));
            $this->exp_chatSendServerMessage('#admin_action#New spectator password:#variable# %s', null, array($params[0]), $fromLogin);
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setRefereePassword($fromLogin, $params) {
        try {
            $this->connection->setRefereePassword($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets/unsets new referee password.', null, array($admin->nickName));
            $this->exp_chatSendServerMessage('#admin_action#New referee password:#variable# %s', null, array($params[0]), $fromLogin);
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
            $this->exp_chatSendServerMessage('#admin_action#Admin #variable#%s $z#admin_action#sets new chat time limit of #variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setTAlimit($fromLogin, $params) {

        try {
            $this->connection->setTimeAttackLimit(self::time_TMtoMS($params[0]));
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new time limit of #variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
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
            $this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameter for the command is either true or false.');
            return;
        }

        try {
            $this->connection->allowMapDownload($bool);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#set allow download maps to#variable# %s', null, array($admin->nickName, $param1));
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
            $this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameters for command are: 0,1,2,visible,hidden,nations.');
            return;
        }
        try {
            $this->connection->setHideServer($output);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#set Hide Server to#variable# %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function stopDedicated($fromLogin, $params) {
        if (!AdminGroups::hasPermission($login, 'server_stopServer'))
            $this->noPermission();

        try {

            $this->connection->stopServer();
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function stopManiaLive($fromLogin, $params) {
        if (!AdminGroups::hasPermission($login, 'server_stopManialive'))
            $this->noPermission();

        die();
    }

    function skipMap($fromLogin, $params) {
        try {
            $this->connection->nextMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#skips the challenge!', null, array($admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function restartMap($fromLogin, $params) {
        try {
            $this->connection->restartMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($fromLogin);
            //Dispatcher::dispatch(new events\onMaxAdmin_Restart());
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#restarts the challenge!', null, array($admin->nickName));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setGameMode($fromLogin, $params) {
        $gamemode = NULL;

        if (is_numeric($params[0])) {
            $gamemode = $params[0];
        } else {
            $param1 = $params[0];
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
                $this->sendErrorChat($fromLogin, 'Invalid parameter. Valid parameteres are: script,team,timeattack,ta,rounds,laps,stunts,cup.');
                return;
            }
        }

        try {
            $this->connection->setGameMode($gamemode);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets game mode to#variable# %s', null, array($admin->nickName, $params[0]));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    function setAllWarmUpDuration($fromLogin, $params) {

        try {
            $this->connection->setAllWarmUpDuration($params[0]);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action#sets all game modes warmup duration to#variable# %s', null, array($admin->nickName, $params[0]));
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
            $this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameter for the command is either true or false.');
            return;
        }

        try {
            $this->connection->setDisableRespawn($bool);
            $admin = $this->storage->getPlayerObject($fromLogin);
            $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#set allow respawn to #variable# %s', null, array($admin->nickName, $param1));
        } catch (\Exception $e) {
            $this->sendErrorChat($fromLogin, $e->getMessage());
        }
    }

    /* Graphical Methods */

    function getBanList($login) {
        GenericPlayerList::Erase($login);

        try {
            $window = GenericPlayerList::Create($login);
            $window->setTitle('Banned Players on the server');
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
            $window->setTitle(__('Blacklisted Players on the server', $login));
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
            $window->setTitle(__('Guest Players on the server'));
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
            $window->setTitle(__('Ignored Players on the server'));
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

    public static function time_TMtoMS($time) {
        echo $time . "\n";
        $parts = explode(":", $time);
        return ($parts[0] * 60 + $parts[1]) * 1000;
    }

}

?>
