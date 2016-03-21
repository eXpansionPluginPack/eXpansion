<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin;

use Exception;
use ManiaLib\Utils\Formatting;
use ManiaLib\Utils\Path;
use ManiaLive\Application\Application;
use ManiaLive\Event\Dispatcher;
use ManiaLive\PluginHandler\Dependency;
use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\AdminGroups\types\Boolean;
use ManiaLivePlugins\eXpansion\AdminGroups\types\Integer;
use ManiaLivePlugins\eXpansion\AdminGroups\types\Time_ms;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls\BannedPlayeritem;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls\BlacklistPlayeritem;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls\GuestPlayeritem;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls\IgnoredPlayeritem;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Windows\GenericPlayerList;
use ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Windows\ParameterDialog;
use ManiaLivePlugins\eXpansion\Chat_Admin\Structures\ActionDuration;
use ManiaLivePlugins\eXpansion\Core\Config;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Core\Events\ExpansionEvent;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Core\Events\GlobalEvent;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\Helpers\Storage;
use ManiaLivePlugins\eXpansion\Helpers\TimeConversion;
use Maniaplanet\DedicatedServer\Structures\GameInfos;
use Maniaplanet\DedicatedServer\Structures\Player;
use Maniaplanet\DedicatedServer\Structures\PlayerBan;
use Phine\Exception\Exception as Exception2;

/**
 * Description of Admin
 *
 * @author oliverde8
 */
class Chat_Admin extends ExpPlugin
{

	/** @var integer $dynamicTime */
	private $dynamicTime = 0;

	/** @var integer $teamGap */
	private $teamGap = 0;

	/** @var ActionDuration[] $durations */
	private $durations = array();

	public static $showActions = array();

	public function exp_onInit()
	{
		parent::exp_onInit();
		ParameterDialog::$mainPlugin = $this;
		$this->addDependency(new Dependency('\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups'));

		$this->setPublicMethod("restartMap");
		$this->setPublicMethod("skipMap");
		$this->setPublicMethod("cancelVote");
		$this->setPublicMethod("showGuestList");
		$this->setPublicMethod("showBanList");
		$this->setPublicMethod("showBlackList");
		$this->setPublicMethod("showIgnoreList");
		$this->setPublicMethod("forceEndRound");
		$this->setPublicMethod("shuffleMaps");
	}

	public function exp_onLoad()
	{
		parent::exp_onLoad();

		$admingroup = AdminGroups::getInstance();

		$cmd = AdminGroups::addAdminCommand('game script', $this, 'support_fastScript', Permission::game_settings);
		$cmd->setHelp('/script load toto will load that script.');
		$admingroup->addShortAlias($cmd, 'script');

		$cmd = AdminGroups::addAdminCommand('game ta', $this, 'support_fastTa', Permission::game_settings);
		$cmd->setHelp('/ta limit; Sets timelimit for TimeAttack');
		$admingroup->addShortAlias($cmd, 'ta');

		$cmd = AdminGroups::addAdminCommand('game laps', $this, 'support_fastLaps', Permission::game_settings);
		$cmd->setHelp('/laps laps X; Sets Laps Limit');
		$admingroup->addShortAlias($cmd, 'laps');

		$cmd = AdminGroups::addAdminCommand('game rounds', $this, 'support_fastRounds', Permission::game_settings);
		$cmd->setHelp('/rounds limit X; Sets PointLimit in Rounds');
		$admingroup->addShortAlias($cmd, 'rounds');

		$cmd = AdminGroups::addAdminCommand('game cup', $this, 'support_fastCup', Permission::game_settings);
		$cmd->setHelp('/cup limit X; Sets CupRoundsLimit for Winner');
		$admingroup->addShortAlias($cmd, 'cup');

		$cmd = AdminGroups::addAdminCommand('game team', $this, 'support_fastTeam', Permission::game_settings);
		$cmd->setHelp('/team limit X; Sets Team PointLimit');
		$admingroup->addShortAlias($cmd, 'team');

		/*
		 * *******************
		 * Concerning Players
		 * *******************
		 *
		 *
		 */

		$cmd = AdminGroups::addAdminCommand('player kick', $this, 'kick', Permission::player_kick); //
		$cmd->setHelp('kick the player from the server');
		$cmd->setHelpMore('$w/admin player kick #login$z will kick the player from the server.
A kicked player may return to the server whanever he desires.');
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, "kick"); // xaseco & fast


		$cmd = AdminGroups::addAdminCommand('player guest', $this, 'guest', Permission::player_kick); //
		$cmd->setHelp('guest the player from the server');
		$cmd->setHelpMore('$w/admin player guest #login$z will guest the player from the server.
A guest player doesen\'t need to enter passwords to enter the server.');
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, "guest"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('player remove guest', $this, 'guestRemove', Permission::player_kick); //
		$cmd->setHelp('remove the guest status of the player');
		$cmd->setHelpMore('$w/admin remove guest #login$z will remove the guest status of the player.
A guest player doesen\'t need to enter passwords to enter the server.');
		$cmd->setMinParam(1);

		$cmd = AdminGroups::addAdminCommand('player ban', $this, 'ban', Permission::player_ban);
		$cmd->setHelp('Ban the player from the server');
		$cmd->setHelpMore('$w/admin player ban #login$z will ban  the player from the server.
He may not return until the server is restarted');
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, "ban"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('player black', $this, 'blacklist', Permission::player_black);
		$cmd->setHelp('Add the player to the black list');
		$cmd->setHelpMore('$w/admin player black #login$z will add the player to the blacklist of this server.
He may not return until the server blacklist file is deleted.
Other server might use the same blacklist file!!');
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, "black"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('player remove ban', $this, 'unban', Permission::player_unban);
		$cmd->setHelp('Removes the ban of the player')
				->addLineHelpMore('$w/admin player remove ban #login$z will remove the ban of the player from this server')
				->addLineHelpMore('He may rejoin the server after this.')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "unban"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('clear banlist', $this, 'cleanBanlist', Permission::player_unban);
		$cmd->setHelp('clears the banlist of players')
				->addLineHelpMore('Will completeley clear the banlist.')
				->addLineHelpMore('All banned players will be able to rejoin the server.')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "cleanbanlist"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('get banlist', $this, 'showBanList', Permission::server_genericOptions);
		$cmd->setHelp('shows the current banlist of players')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "getbanlist");

		$cmd = AdminGroups::addAdminCommand('clear blacklist', $this, 'cleanBlacklist', Permission::player_unblack);
		$cmd->setHelp('clears the blacklist of players')
				->addLineHelpMore('Will completeley clear the blackList.')
				->addLineHelpMore('All blacklist players will be able to rejoin the server.')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "cleanblacklist");

		$cmd = AdminGroups::addAdminCommand('get blacklist', $this, 'showBlackList', Permission::server_genericOptions);
		$cmd->setHelp('shows the current banlist of players')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "getblacklist");

		$cmd = AdminGroups::addAdminCommand('get guestlist', $this, 'showGuestList', Permission::server_genericOptions);
		$cmd->setHelp('shows the current guest of players')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "getguestlist");

		$cmd = AdminGroups::addAdminCommand('get ignorelist', $this, 'showIgnoreList', Permission::player_ignore);
		$cmd->setHelp('shows the current ignorelist of players')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "getignorelist");

		$cmd = AdminGroups::addAdminCommand('remove black', $this, 'unBlacklist', Permission::player_unblack);
		$cmd->setHelp('Removes the player from the black list')
				->addLineHelpMore('$w/admin player remove black #login$z will remove the player from the servers blacklist')
				->addLineHelpMore('He may rejoin the server after this.')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "unblack"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('player spec', $this, 'forceSpec', Permission::player_forcespec);
		$cmd->setHelp('Forces the player to become spectator')
				->addLineHelpMore('$w/admin player spec #login$z The playing player will be forced to become a spectator')
				->addLineHelpMore('If the max spectators is reached it the player won\'t become a spectator')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "spec"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('player ignore', $this, 'ignore', Permission::player_ignore);
		$cmd->setHelp('Adds player to ignore list and mutes him from the chat')
				->addLineHelpMore('$w/admin player ignore #login$z will ignore the players chat')
				->addLineHelpMore('This player won\'t be able to communicate with other players.')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "ignore"); // xaseco & fast

		$cmd = AdminGroups::addAdminCommand('player unignore', $this, 'unignore', Permission::player_ignore);
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

		$cmd = AdminGroups::addAdminCommand('settings', $this, 'invokeExpSettings', Permission::expansion_pluginSettings);
		$cmd->setMinParam(0);

		$cmd = AdminGroups::addAdminCommand('netstats', $this, 'invokeNetStats', Permission::chat_adminChannel);
		$cmd->setMinParam(0);
		AdminGroups::addAlias($cmd, "netstat"); // fast

		$cmd = AdminGroups::addAdminCommand('get server planets', $this, 'getServerPlanets', Permission::server_genericOptions);
		$cmd->setHelp('Gets the serveraccount planets amount')
				->addLineHelpMore('$w/admin planets $zreturn the planets amount on server account.')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "planets"); // fast

		$cmd = AdminGroups::addAdminCommand('set server pay', $this, 'pay', Permission::server_usePlanets);
		$cmd->setHelp('Pays out planets')
				->addLineHelpMore('$w/admin pay #login #amount$z pays amount of planets to login')
				->setMinParam(2);
		$cmd->addchecker(2, Integer::getInstance());
		AdminGroups::addAlias($cmd, "pay"); // xaseco

		$cmd = AdminGroups::addAdminCommand('set server name', $this, 'setServerName', Permission::server_name);
		$cmd->setHelp('Changes the name of the server')
				->addLineHelpMore('$w/admin set server name #name$z will change the server name.')
				->addLineHelpMore('This servers name will be changed.')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "setservername"); // xaseco
		AdminGroups::addAlias($cmd, "name"); // fast

		$cmd = AdminGroups::addAdminCommand('set server comment', $this, 'setServerComment', Permission::server_comment);
		$cmd->setHelp('Changes the server comment')
				->addLineHelpMore('$w/admin set server comment #comment$z will change the server comment.')
				->addLineHelpMore('This servers comment will be changed.')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "setcomment"); // xaseco
		AdminGroups::addAlias($cmd, "comment"); // fast

		$cmd = AdminGroups::addAdminCommand('set server player password', $this, 'setServerPassword', Permission::server_password);
		$cmd->setHelp('Changes the player password')
				->setHelpMore('$w/admin set server spec password #pwd$z will change the password needed for players to connect to this server')
				->setMinParam(0);
		AdminGroups::addAlias($cmd, "setpwd"); // xaseco
		AdminGroups::addAlias($cmd, "pass"); // fast

		$cmd = AdminGroups::addAdminCommand('set server spec password', $this, 'setSpecPassword', Permission::server_specpwd);
		$cmd->setHelp('Changes the spectator password')
				->setHelpMore('$w/admin set server spec password #pwd$z will change the password needed for spectators to connect to this server')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "setspecpwd"); // xaseco
		AdminGroups::addAlias($cmd, "spectpass"); // fast


		$cmd = AdminGroups::addAdminCommand('set server ref password', $this, 'setRefereePassword', Permission::server_refpwd);
		$cmd->setHelp('Changes the Referee password')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, "setrefpwd"); // xaseco


		$cmd = AdminGroups::addAdminCommand('set server maxplayers', $this, 'setServerMaxPlayers', Permission::server_maxplayer);
		$cmd->setHelp('Sets a new maximum of players')
				->setHelpMore('Sets the maximum number of players who can play on this server.')
				->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setmaxplayers"); //xaseco
		AdminGroups::addAlias($cmd, "maxplayers"); // fast

		$cmd = AdminGroups::addAdminCommand('set server maxspectators', $this, 'setServerMaxSpectators', Permission::server_maxspec);
		$cmd->setHelp('Sets a new maximum of spectator')
				->setHelp('Sets the maximum number of players who can spectate the players on this server.');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setmaxspecs"); // xaseco
		AdminGroups::addAlias($cmd, "maxspec"); // fast

		$cmd = AdminGroups::addAdminCommand('set server chattime', $this, 'setserverchattime', Permission::server_genericOptions);
		$cmd->setHelp('Sets the Chat time duration.')
				->addLineHelpMore('This is the time players get between the challenge end and the the new map')
				->setMinParam(1);
		$cmd->addchecker(1, Time_ms::getInstance());
		AdminGroups::addAlias($cmd, "setchattime"); // xaseco
		AdminGroups::addAlias($cmd, "chattime"); // fast

		$cmd = AdminGroups::addAdminCommand('set server hide', $this, 'setHideServer', Permission::server_genericOptions);
		$cmd->setHelp('Allows you to hide or show the server to players')
				->addLineHelpMore('$w\admin set server hide true$z Will hide the server from other players. Players would need to have the servers in their favorites or need to know the server login ')
				->addLineHelpMore('$w\admin set server hide false$z Will make the server visible to any player')
				->addchecker(1, Boolean::getInstance());
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, "sethideserver");

		$cmd = AdminGroups::addAdminCommand('set server mapdownload', $this, 'setServerMapDownload', Permission::server_genericOptions);
		$cmd->setHelp('Will allow players to download maps they are playing from the server.')
				->addLineHelpMore('$w\admin set server mapdownload true$z will allow the maps to be downloaded.')
				->addLineHelpMore('$w\admin set server mapdownload false$z will not allow players to download the maps of this server.')
				->addchecker(1, Boolean::getInstance());
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, "setmapdownload");

		$cmd = AdminGroups::addAdminCommand('stop dedicated', $this, 'stopDedicated', Permission::server_stopDedicated);
		$cmd->setHelp("Stops this server. Manialive will stop after this.");
		AdminGroups::addAlias($cmd, 'stop dedi');

		$cmd = AdminGroups::addAdminCommand('stop manialive', $this, 'stopManiaLive', Permission::server_stopManialive);
		$cmd->setHelp("Stops the Manialive instance running on for the server.");
		AdminGroups::addAlias($cmd, 'stop exp');
		AdminGroups::addAlias($cmd, 'stop expansion');
		AdminGroups::addAlias($cmd, 'manialive stop');
		$cmd = AdminGroups::addAdminCommand('manialive restart', $this, 'restartManiaLive', Permission::server_stopManialive);
		$cmd->setHelp("Restart the Manialive instance running on for the server.");
		AdminGroups::addAlias($cmd, 'res exp');
		AdminGroups::addAlias($cmd, 'res expansion');

		/*
		 * *************************
		 * Concerning Game Settings
		 * *************************
		 */
		$cmd = AdminGroups::addAdminCommand('skip', $this, 'skipMap', Permission::map_skip);
		$cmd->setHelp("Skips the current track");
		AdminGroups::addAlias($cmd, 'skip'); // shortcut
		AdminGroups::addAlias($cmd, 'skipmap'); // xaseco
		AdminGroups::addAlias($cmd, 'next'); // fast
		AdminGroups::addAlias($cmd, 'nextmap');

		$cmd = AdminGroups::addAdminCommand('restart', $this, 'restartMap', Permission::map_restart);
		$cmd->setHelp("Restarts this map to allow you to replay the map");
		AdminGroups::addAlias($cmd, 'res'); // xaseco
		AdminGroups::addAlias($cmd, 'restart'); // fast
		AdminGroups::addAlias($cmd, 'restartmap'); //xaseco

		$cmd = AdminGroups::addAdminCommand('rskip', $this, 'skipScoreReset', Permission::map_skip);
		$cmd->setHelp("Skips the current track and reset scores");
		
		$cmd = AdminGroups::addAdminCommand('rres', $this, 'restartScoreReset', Permission::map_restart);
		$cmd->setHelp("Restarts this map and resets the scores");
		
		$cmd = AdminGroups::addAdminCommand('set game mode', $this, 'setGameMode', Permission::game_gamemode);
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

		$cmd = AdminGroups::addAdminCommand('load script', $this, 'loadScript', Permission::game_gamemode);
		$cmd->setHelp('Loads script for the next game.')
			->addLineHelpMore('$w\admin script load TimeAttack.script.txt will switch to TA script mode.');
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, 'loadscript'); //xaseco

		$cmd = AdminGroups::addAdminCommand('reload script', $this, 'reloadScript', Permission::game_gamemode);
		$cmd->setHelp('Loads script for the next game.')
			->addLineHelpMore('$w\admin script reload Reloads current script. (Usefull if script was changed).');
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, 'reloadscript'); //xaseco

		$cmd = AdminGroups::addAdminCommand('set game AllWarmUpDuration', $this, 'setAllWarmUpDuration', Permission::game_settings);
		$cmd->setHelp('Set the warmup duration at the begining of the maps for all gamemodes')
				->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, 'setAllWarmUpDuration');

		$cmd = AdminGroups::addAdminCommand('set game disableRespawn', $this, 'setDisableRespawn', Permission::game_settings);
		$cmd->setHelp('Will disable the respawn capabilities of the players')
				->addLineHelpMore('$w/admin set game disableRespawn true$z will force the players to restart the map when they respaw')
				->addLineHelpMore('$w/admin set game disableRespawn false$z player that respaw will return back to the last checkpoint')
				->addLineHelpMore("\n" . 'A player respaws when he clicks on backspace on his keyboard')
				->setMinParam(1);
		AdminGroups::addAlias($cmd, 'setDisableRespawn');

		//TimeAttack
		$cmd = AdminGroups::addAdminCommand('set game ta timelimit', $this, 'setTAlimit', Permission::game_settings);
		$cmd->setHelp('Changes the time limit of Time Attack mode.')
				->addLineHelpMore('$w/admin set game ta timelimit #num$z will change the play time of a map')
				->setMinParam(1);
		$cmd->addchecker(1, Time_ms::getInstance());
		AdminGroups::addAlias($cmd, 'setTAlimit');

		$cmd = AdminGroups::addAdminCommand('set game ta dynamic', $this, 'setTAdynamic', Permission::game_settings);
		$cmd->setHelp('Enables the dynamic timelimit for Time Attack Mode.')
				->addLineHelpMore('$w/admin set game ta timelimit #num$z will change the multiplier used for map authortime.')
				->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, 'setTAdynamic');

		$cmd = AdminGroups::addAdminCommand('set game ta WarmUpDuration', $this, 'setAllWarmUpDuration', Permission::game_settings);
		$cmd->setHelp('Changes the warmup duration of Time Attack mode only')
				->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());

		//rounds
		$cmd = AdminGroups::addAdminCommand('set game rounds end', $this, 'forceEndRound', Permission::map_endRound);
		$cmd->setHelp('Ends a round. Only work in round mode');
		AdminGroups::addAlias($cmd, 'end'); // fast
		AdminGroups::addAlias($cmd, 'endround'); // xaseco
		AdminGroups::addAlias($cmd, 'er'); // xaseco

		$cmd = AdminGroups::addAdminCommand('set game rounds PointsLimit', $this, 'setRoundPointsLimit', Permission::game_settings);
		$cmd->setHelp('Changes the points limit of rounds mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, 'rpoints');

		$cmd = AdminGroups::addAdminCommand('set game rounds ForcedLaps', $this, 'setRoundForcedLaps', Permission::game_settings);
		$cmd->setHelp('Forces laps in Rounds mode')
				->addLineHelpMore('$w\admin set game rounds ForcedLaps #num$z will force multi laps maps lap number to the given value')
				->addLineHelpMore('using 0 as number of laps will change the nb of laps to the default value')
				->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, 'setRoundForcedLaps');

		$cmd = AdminGroups::addAdminCommand('set game rounds NewRules', $this, 'setUseNewRulesRound', Permission::game_settings);
		$cmd->setHelp('Allows you tu use new rules in rounds mode')
				->addLineHelpMore('$w/admin set game rounds NewRules true$z will force the usage of new rules in rounds mode')
				->addLineHelpMore('$w/admin set game rounds NewRules false$z will force the usage of old rules in rounds mode')
				->setMinParam(1);
		$cmd->addchecker(1, Boolean::getInstance());
		AdminGroups::addAlias($cmd, 'setUseNewRulesRound');

		$cmd = AdminGroups::addAdminCommand('set game rounds WarmUpDuration', $this, 'setAllWarmUpDuration', Permission::game_settings);
		$cmd->setHelp('Changes the warmup duration of Rounds mode only')
				->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, 'setAllWarmUpDuration');

		//laps
		$cmd = AdminGroups::addAdminCommand('set game laps TimeLimit', $this, 'setLapsTimeLimit', Permission::game_settings);
		$cmd->setHelp('Changes the limit of time players has to finish the track')
				->setMinParam(1)
				->addchecker(1, Time_ms::getInstance());
		AdminGroups::addAlias($cmd, "setLapsTimeLimit");

		$cmd = AdminGroups::addAdminCommand('set game laps nbLaps', $this, 'setNbLaps', Permission::game_settings);
		$cmd->setHelp('Changes the numbers of laps players need to do to finish the map');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setNbLaps");

		$cmd = AdminGroups::addAdminCommand('set game laps FinishTimeOut', $this, 'setFinishTimeout', Permission::game_settings);
		$cmd->setHelp('Changes the time that has a player to finish a map once 1 player has already finished the map')
				->setMinParam(1)
				->addchecker(1, Time_ms::getInstance());
		AdminGroups::addAlias($cmd, "setFinishTimeout");


		$cmd = AdminGroups::addAdminCommand('set game laps WarmUpDuration', $this, 'setAllWarmUpDuration', Permission::game_settings);
		$cmd->setHelp('Changes the warmup duration of laps mode only')
				->setMinParam(1)
				->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setAllWarmUpDuration");

		//team
		$cmd = AdminGroups::addAdminCommand('set game team PointsLimit', $this, 'setTeamPointsLimit', Permission::game_settings);
		$cmd->setHelp('Changes the points limit of team mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setTeamPointsLimit");

		$cmd = AdminGroups::addAdminCommand('set game team PointsLimit', $this, 'setTeamBalance', Permission::game_settings);
		$cmd->setHelp('tries to autobalance teams');
		$cmd->setMinParam(0);
		AdminGroups::addAlias($cmd, "setTeamBalance");

		$cmd = AdminGroups::addAdminCommand('set game team maxPoints', $this, 'setMaxPointsTeam', Permission::game_settings);
		$cmd->setHelp('Changes the Max PointsLimit of team mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setMaxPointsTeam");

		$cmd = AdminGroups::addAdminCommand('set game team NewRules', $this, 'setUseNewRulesTeam', Permission::game_settings);
		$cmd->setHelp('Changes the NewRules of team mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Boolean::getInstance());
		AdminGroups::addAlias($cmd, "setUseNewRulesTeam");

		$cmd = AdminGroups::addAdminCommand('set game team forcePlayer', $this, 'forcePlayerTeam', Permission::game_settings);
		$cmd->setHelp('Changes the Team for a Player by Forcing him');
		$cmd->setMinParam(2);
		$cmd->addchecker(2, \ManiaLivePlugins\eXpansion\AdminGroups\types\Arraylist::getInstance()->items("0,1,red,blue"));
		AdminGroups::addAlias($cmd, "forcePlayerTeam");


		$cmd = AdminGroups::addAdminCommand('set game team WarmUpDuration', $this, 'setAllWarmUpDuration', Permission::game_settings);
		$cmd->setHelp('Changes the WarmUpDuration of team mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setAllWarmUpDuration");

		//cup
		$cmd = AdminGroups::addAdminCommand('set game cup PointsLimit', $this, 'setCupPointsLimit', Permission::game_settings);
		$cmd->setHelp('Changes the Cup PointLimit of Cup mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setCupPointsLimit");

		$cmd = AdminGroups::addAdminCommand('set game cup RoundsPerMap', $this, 'setCupRoundsPerMap', Permission::game_settings);
		$cmd->setHelp('Changes the Cup RoundsPerMap of Cup mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setCupRoundsPerMap");

		$cmd = AdminGroups::addAdminCommand('set game cup WarmUpDuration', $this, 'setCupWarmUpDuration', Permission::game_settings);
		$cmd->setHelp('Changes the Cup WarmUpDuration of Cup mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Time_ms::getInstance());
		AdminGroups::addAlias($cmd, "setCupWarmUpDuration");

		$cmd = AdminGroups::addAdminCommand('set game cup NbWinners', $this, 'setCupNbWinners', Permission::game_settings);
		$cmd->setHelp('Changes the Cup NbWinners of Cup mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Integer::getInstance());
		AdminGroups::addAlias($cmd, "setCupNbWinners");

		/* $cmd = AdminGroups::addAdminCommand('set game cup customPoints', $this, 'prepareRoundPoints', Permission::game_settings);
		  $cmd->setHelp('Changes the Cup CustomPoints of Cup mode');
		  $cmd->setMinParam(1);
		  AdminGroups::addAlias($cmd, "prepareRoundPoints"); */

		$cmd = AdminGroups::addAdminCommand('set game cup finishtimeout', $this, 'setFinishTimeout', Permission::game_settings);
		$cmd->setHelp('Changes the Cup Finishtimeout of Cup mode');
		$cmd->setMinParam(1);
		$cmd->addchecker(1, Time_ms::getInstance());
		AdminGroups::addAlias($cmd, "setFinishTimeout");

		$cmd = AdminGroups::addAdminCommand('maps shuffle', $this, 'shuffleMaps', Permission::game_settings);
		$cmd->setHelp('Shuffles the mapslist');
		$cmd->setMinParam(0);
		AdminGroups::addAlias($cmd, "shuffle");

		$this->enableDatabase();
		$this->enableTickerEvent();
		self::$showActions['ignore'] = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'showIgnoreList'));
		self::$showActions['ban'] = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'showBanList'));
		self::$showActions['black'] = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'showBlackList'));
		self::$showActions['guest'] = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'showGuestList'));
	}

	public function exp_onReady()
	{
		$this->enableDedicatedEvents();
	}

	public function onTick()
	{
		if (time() % 30 == 0) {
			foreach ($this->durations as $duration) {
				if ($duration->stamp < time()) {
					switch ($duration->action) {
						case "ban":
							unset($this->durations[$duration->login]);
							if ($this->checkBanList($duration->login)) {
								$this->connection->unBan($duration->login);
							}
							break;
						case "black":
							unset($this->durations[$duration->login]);
							if ($this->checkBlackList($duration->login)) {
								$this->connection->unBlackList($duration->login);
							}
							break;
					}
				}
			}
		}
	}

	public function checkBanList($login)
	{
		foreach ($this->connection->getBanList(-1, 0) as $player) {
			if ($player->login == $login)
				return true;
		}
		return false;
	}

	public function checkBlackList($login)
	{
		foreach ($this->connection->getBlackList(-1, 0) as $player) {
			if ($player->login == $login)
				return true;
		}
		return false;
	}

	/**
	 * Set ban or backlist duration
	 *
	 * @param string $login
	 * @param string $action
	 * @param string $duration
	 */
	public function addActionDuration($login, $action, $duration)
	{
		if ($duration != "permanent") {
			$this->durations[$login] = new ActionDuration($login, $action, $duration);
		}
	}

	function support_fastScript($fromLogin, $params)
	{

		if ($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_SCRIPT) {
			$this->exp_chatSendServerMessage("#admin_error#Error: Not in script mode!", $fromLogin);
			return;
		}

		try {
			$command = array_shift($params);
			switch (strtolower($command)) {
				case "reload":
					$this->reloadScript($fromLogin);
					break;
				case "load":
					$this->loadScript($fromLogin, $params);
					break;
				default:
					break;
			}
		} catch (Exception $e) {
			$this->exp_chatSendServerMessage('#admin_error#Error:' . $e->getMessage(), $fromLogin);
		}
	}

	function support_fastTa($fromLogin, $params)
	{

		try {
			$command = array_shift($params);

			switch (strtolower($command)) {
				case "time":
				case "limit":
				case "timelimit":
					$this->setTAlimit($fromLogin, $params);
					break;
				case "dyn":
				case "dynamic":
					$this->setTAdynamic($fromLogin, $params);
					break;
				case "wud":
				case "wu":
				case "warmupduration":
					$this->setAllWarmUpDuration($fromLogin, $params);
					break;
				default:
					$msg = exp_getMessage("possible parameters: limit, dynamic, wu");
					$this->exp_chatSendServerMessage($msg, $fromLogin);
					break;
			}
		} catch (Exception $e) {
			
		}
	}

	function support_fastLaps($fromLogin, $params)
	{
		try {
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
					$msg = exp_getMessage("possible parameters: laps, limit, wu, fto, ftimeout");
					$this->exp_chatSendServerMessage($msg, $fromLogin);
					break;
			}
		} catch (Exception $e) {
			
		}
	}

	function support_fastRounds($fromLogin, $params)
	{
		try {
			$command = array_shift($params);

			switch (strtolower($command)) {
				case "limit":
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
				case "skip":
					$this->skipScoreReset($fromLogin, $params);
					break;
				case "res":
					$this->restartScoreReset($fromLogin, $params);
					break;
				default:
					$msg = exp_getMessage("possible parameters: pointslimit, newrules, wu, fto, ftimeout");
					$this->exp_chatSendServerMessage($msg, $fromLogin);
					break;
			}
		} catch (Exception $e) {
			
		}
	}

	function support_fastCup($fromLogin, $params)
	{
		try {
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
					$this->setCupWarmUpDuration($fromLogin, $params);
					break;
				case "fto":
				case "ftimeout":
				case "finishtimeout":
					$this->setFinishTimeout($fromLogin, $params);
					break;
				case "skip":
					$this->skipScoreReset($fromLogin, $params);
					break;
				case "res":
					$this->restartScoreReset($fromLogin, $params);
					break;
				default:
					$msg = exp_getMessage("possible parameters: limit, rounds, nbwin, wu, fto, ftimeout");
					$this->exp_chatSendServerMessage($msg, $fromLogin);
					break;
			}
		} catch (Exception $e) {
			
		}
	}

	function support_fastTeam($fromLogin, $params)
	{
		try {
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
				case "gap":
					$this->enableTeamGap($fromLogin, $params);
					break;
				case "balance":
					$this->setTeamBalance($fromLogin, $params);
					break;
				case "skip":
					$this->skipScoreReset($fromLogin, $params);
					break;
				case "res":
					$this->restartScoreReset($fromLogin, $params);
					break;
				default:
					$msg = exp_getMessage("possible parameters: balance, limit, maxpoint, newrules, wu, fto, ftimeout, blue, red, gap");
					$this->exp_chatSendServerMessage($msg, $fromLogin);
					break;
			}
		} catch (Exception $e) {
			
		}
	}

	public function invokeExpSettings($fromLogin, $params = null)
	{
		$this->callPublicMethod('\ManiaLivePlugins\eXpansion\Core\Core', "showExpSettings", $fromLogin);
	}

	public function invokeNetStats($fromLogin, $params = null)
	{
		$this->callPublicMethod('\ManiaLivePlugins\eXpansion\Core\Core', "showNetStats", $fromLogin);
	}

	public function shuffleMaps($login, $params = null)
	{
		$mapsArray = array();
		foreach ($this->storage->maps as $map) {
			$mapsArray[] = $map->fileName;
		}
		try {
			$this->connection->removeMapList($mapsArray);
			shuffle($mapsArray);
			$this->connection->addMapList($mapsArray);
			$msg = exp_getMessage('#admin_action#Admin #variable#%1$s $z$s#admin_action#shuffles the maps list!');
			$nick = $this->storage->getPlayerObject($login)->nickName;

			$this->exp_chatSendServerMessage($msg, null, array($nick));
		} catch (\Exception $e) {
			$this->exp_chatSendServerMessage("#admin_error#there was error while shuffling the maps", $login);
			$this->console("Error while shuffling maps: " . $e->getMessage);
		}
	}

	public function setTeamBalance($fromLogin, $params)
	{
		try {

			$adminNick = $this->storage->getPlayerObject($fromLogin)->nickName;
			$this->exp_chatSendServerMessage('#admin_action#Admin #variable#%s $z$s#admin_action# AutoBalances the Teams!', null, array($adminNick));
			$this->connection->autoTeamBalance();
		} catch (\Exception $e) {
			$this->exp_chatSendServerMessage("#admin_error#error while AutoTeamBalance: " . $e->getMessage(), $fromLogin);
		}
	}

	public function setScriptName($fromLogin, $params)
	{
		if (sizeof($params) == 0) {
			$name = $this->connection->getScriptName();
			$this->exp_chatSendServerMessage("current script name: " . $name['CurrentValue'], $fromLogin);
			return;
		}

		if (!is_string($params[0])) {
			$this->exp_chatSendServerMessage("#admin_error#needs script name to be text!", $fromLogin);
			return;
		}


		try {
			$this->connection->setScriptName($params[0]);
			$this->exp_chatSendServerMessage("new script in run: " . $params[0] . ", please restart or skip the map for changes to be active.", $fromLogin);
		} catch (Exception2 $ex) {
			$this->exp_chatSendServerMessage("#admin_error#Error:" . $ex->getMessage() . " on line:" . $ex->getLine(), $fromLogin);
		}
	}

	public function enableTeamGap($login, $params)
	{
		if ($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_TEAM) {
			$this->exp_chatSendServerMessage("#admin_error#Not in teams mode!", $login);
		}

		if (sizeof($params) > 0 && is_numeric($params[0])) {
			$this->teamGap = intval($params[0]);

			$this->exp_chatSendServerMessage('#admin_action#Team gap set to #variable# %1$s!', $login, array($params[0]));
			$this->connection->restartMap();
		}
	}

	public function onBeginMatch()
	{
		//  if ($this->teamGap > 1 && $this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM) {
		//      $this->connection->setTeamPointsLimit($this->teamGap * 10);
		//  }
	}

	public function onEndMatch($rankings, $winnerTeamOrMap)
	{
		if ($this->teamGap > 1 && $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TEAM) {
			$this->connection->setTeamPointsLimit($this->teamGap * 10);
		}
	}

	public function onEndRound()
	{
		$this->checkTeamGap();
	}

	public function onBeginRound()
	{
		$this->checkTeamGap();
	}

	public function checkTeamGap()
	{
		if ($this->teamGap > 1 && $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TEAM && $this->storage->gameInfos->teamUseNewRules) {
			$ranking = $this->connection->getCurrentRanking(-1, 0);
			$scoregap = abs($ranking[0]->score - $ranking[1]->score);
			$scoremax = $ranking[0]->score > $ranking[1]->score ? $ranking[0]->score : $ranking[1]->score;
			if ($scoremax >= $this->teamGap && $scoregap >= 2) {
				$this->connection->nextMap(false);
			}
		}
	}

	function pay($fromLogin, $params)
	{
		try {
			$this->connection->pay($params[0], intval($params[1]), $params[0] . " Planets payed out from server " . $this->storage->server->name);
			$this->exp_chatSendServerMessage('#admin_action#Server just sent#variable# %s #admin_action#Planets to#variable# %s #admin_action#!', $fromLogin, array($params[1], $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function getServerPlanets($fromLogin, $params = null)
	{
		try {

			$this->exp_chatSendServerMessage('#admin_action#Server has #variable# %s #admin_action#Planets.', $fromLogin, array($this->connection->getServerPlanets()));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setTeamBlue($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->forcePlayerTeam($params[0], 0);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sends player#variable# %s #admin_action#to team $00fBlue.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setTeamRed($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->forcePlayerTeam($params[0], 1);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sends player#variable# %s #admin_action#to team $f00Red.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setCupNbWinners($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setCupWarmUpDuration(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets cup winners to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setCupWarmUpDuration($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setCupWarmUpDuration(TimeConversion::MStoTM($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new warmup duration to#variable# %s #admin_action#.', null, array($admin->nickName, TimeConversion::MStoTM($params[0])));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setCupRoundsPerMap($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setCupRoundsPerMap(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new rounds to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setCupPointsLimit($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setCupPointsLimit(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new cup points limit to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function forcePlayerTeam($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		$player = $this->storage->getPlayerObject($params[0]);
		if ($player == null) {
			$this->exp_chatSendServerMessage('#admin_action#Player #variable# %s #admin_action#doesn\' exist.', null, array($params[0]));
			return;
		}

		if ($params[1] == "red")
			$params[1] = 1;
		if ($params[1] == "blue")
			$params[1] = 0;

		try {
			$this->connection->forcePlayerTeam($player, intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#forces player #variable# %s #admin_action# to team#variable# %s #admin_action#.', null, array($admin->nickName, $player->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setUseNewRulesTeam($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setMaxPointsTeam(filter_var($params[0], FILTER_VALIDATE_BOOLEAN));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets use new team rules to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setMaxPointsTeam($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setMaxPointsTeam(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets Team max points to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setTeamPointsLimit($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setTeamPointsLimit(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets Team points limit to#variable# %s #admin_action#.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setFinishTimeout($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setFinishTimeout(TimeConversion::MStoTM($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new finish timeout to#variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setNbLaps($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setNbLaps(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new number of laps to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setLapsTimeLimit($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setLapsTimeLimit(TimeConversion::MStoTM($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new laps timelimit to#variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setRoundPointsLimit($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setRoundPointsLimit(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets rounds points limits to#variable# %s.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function forceEndRound($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
				$this->connection->triggerModeScriptEvent('Rounds_ForceEndRound');
			}
			else {
				$this->connection->forceEndRound();
			}

			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#forces the round to end.', null, array($admin->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setUseNewRulesRound($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setUseNewRulesRound(filter_var($params[0], FILTER_VALIDATE_BOOLEAN));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new round rules to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setRoundForcedLaps($fromLogin, $params)
	{

		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->setRoundForcedLaps(intval($params[0]));
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new round forced laps to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function blacklist($fromLogin, $params)
	{
		$target = array_shift($params);
		$reason = implode(" ", $params);
		$player = $this->storage->getPlayerObject($target);
		if ($player == null) {
			$this->exp_chatSendServerMessage('#admin_action#Player #variable# %s #admin_action#doesn\' exist.', $fromLogin, array($target));
			return;
		}
		if (empty($reason)) {
			$dialog = ParameterDialog::Create($fromLogin);
			$dialog->setTitle(__("blacklist", $fromLogin), Formatting::stripStyles($player->nickName));
			$dialog->setData("black", $target);
			$dialog->show($fromLogin);
			return;
		}
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->expStorage->loadBlackList();
			$this->connection->banAndBlackList($target, $reason, true);
			$this->expStorage->saveBlackList();

			$this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action#blacklists the player #variable# %s', null, array($admin->nickName, $player->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function cleanBlacklist($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->cleanBlackList();
			$this->expStorage->saveBlackList();
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#cleans the blacklist.', null, array($admin->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function cleanBanlist($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->cleanBanList();
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#cleans the banlist.', null, array($admin->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function cleanIgnorelist($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->cleanIgnoreList();
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#cleans the ignorelist.', null, array($admin->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	public function reloadScript($fromLogin) {

		$scriptNameArr = $this->connection->getScriptName();
		$scriptName = $scriptNameArr['CurrentValue'];

		// Workaround for a 'bug' in setModeScriptText.
		if ($scriptName === '<in-development>') {
			$scriptName = $scriptNameArr['NextValue'];
		}

		$this->loadScript($fromLogin, array($scriptName));
	}

	public function loadScript($fromLogin, $params)
	{
		if ($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_SCRIPT) {
			$this->exp_chatSendServerMessage("#admin_error#Error: Not in script mode!", $fromLogin);
			return;
		}

		$mapsDir = Helper::getPaths()->getDefaultMapPath();
		$mode = "TrackMania";
		if ($this->expStorage->simpleEnviTitle == "SM")
			$mode = "ShootMania";

		$scriptName = dirname($mapsDir) . "/Scripts/Modes/" . $mode . "/" . $params[0];

		// Append .Script.txt if left out
		if (strtolower(substr($scriptName, -11)) !== '.script.txt')
			$scriptName .= '.Script.txt';

		if (file_exists($scriptName)) {
			$data = file_get_contents($scriptName);

			try {
				$this->connection->setModeScriptText($data);
				$this->connection->setScriptName($params[0]);
				$this->exp_chatSendServerMessage("Script loaded to server runtime: " . $params[0]);
			} catch (\Exception $e) {
				$this->exp_chatSendServerMessage("#admin_error#Couldn't load script : ", $e->getMessage());
			}

			return;
		}
		$this->exp_chatSendServerMessage("#admin_error#Error: Script wasn't found !", $fromLogin);
	}

	public function unBlacklist($fromLogin, $params)
	{

		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->expStorage->loadBlackList();
			$this->connection->unBlackList($params[0]);
			$this->expStorage->saveBlackList();

			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#unblacklists the player %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function ban($fromLogin, $params)
	{
		$target = array_shift($params);
		$reason = implode(" ", $params);
		$player = $this->storage->getPlayerObject($target);
		if (is_object($player)) {
			$nickname = $player->nickName;
		}
		else {
			$nickname = $target;
		}
		if (empty($reason)) {
			$dialog = ParameterDialog::Create($fromLogin);
			$dialog->setTitle(__("ban", $fromLogin), Formatting::stripStyles($player->nickName));
			$dialog->setData("ban", $target);
			$dialog->show($fromLogin);
			return;
		}
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->ban($target, $reason);
			$this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action# bans the player#variable# %s', null, array($admin->nickName, $nickname));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function ignore($fromLogin, $params)
	{

		$player = $this->storage->getPlayerObject($params[0]);
		if (is_object($player)) {
			$nickname = $player->nickName;
		}
		else {
			$nickname = $params[0];
		}

		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->ignore($params[0]);
			$this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action# ignores the player#variable# %s', null, array($admin->nickName, $nickname));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function unban($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->unBan($params[0]);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#unbans the player %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function unignore($fromLogin, $params)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);

		try {
			$this->connection->unIgnore($params[0]);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#unignores the player %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function kick($fromLogin, $params)
	{
		$target = array_shift($params);
		$reason = implode(" ", $params);
		$reason = trim($reason);
		$player = $this->storage->getPlayerObject($target);
		if ($player == null) {
			$this->exp_chatSendServerMessage('#admin_error#Player #variable# %s doesn\' exist.', $fromLogin, array($target));
			return;
		}
		if (empty($reason)) {
			$dialog = ParameterDialog::Create($fromLogin);
			$dialog->setTitle(__("kick", $fromLogin), Formatting::stripStyles($player->nickName));
			$dialog->setData("kick", $target);
			$dialog->show($fromLogin);
			return;
		}
		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->connection->kick($player, $reason);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#kicks the player#variable# %s', null, array($admin->nickName, $player->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function guest($fromLogin, $params)
	{
		$target = array_shift($params);

		$player = $this->storage->getPlayerObject($target);
                $nick = $target;
		if ($player != null) {
			$nick = $player->nickName;
		}

		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->expStorage->loadGuestList();
			$this->connection->addGuest($target);
			$this->expStorage->saveGuestList();

			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#add as guest the player#variable# %s', null, array($admin->nickName, $nick));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function guestRemove($fromLogin, $params)
	{
		$target = array_shift($params);
		$player = $this->storage->getPlayerObject($target);
		if ($player == null) {
			$this->exp_chatSendServerMessage('#admin_error#Player #variable# %s doesn\' exist.', $fromLogin, array($target));
			return;
		}

		$admin = $this->storage->getPlayerObject($fromLogin);
		try {
			$this->expStorage->loadGuestList();
			$this->connection->removeGuest($player);
			$this->expStorage->saveGuestList();

			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#removed guest status of the player#variable# %s', null, array($admin->nickName, $player->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function forceSpec($fromLogin, $params)
	{
		$player = $this->storage->getPlayerObject($params[0]);
		if ($player == null) {
			$this->exp_chatSendServerMessage('#admin_action#Player #variable# %s doesn\' exist.', $fromLogin, array($params[0]));
			return;
		}
		try {
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->connection->forceSpectator($player, 1);
			$this->connection->forceSpectator($player, 0);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#Forces the player #variable# %s #admin_action#to Spectator.', null, array($admin->nickName, $player->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	public function sendErrorChat($login, $message)
	{
		$this->exp_chatSendServerMessage('#admin_error#' . $message, $login);
	}

	function setServerName($fromLogin, $params)
	{
		$name = implode(" ", $params);
		try {
			$this->connection->setServerName($name);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action# sets new server name:#variable# %s', null, array($admin->nickName, $name));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setServerComment($fromLogin, $params)
	{
		$comment = implode(" ", $params);
		try {
			$this->connection->setServerComment($comment);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new server comment:#variable# %s', null, array($admin->nickName, $comment));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setServerMaxPlayers($fromLogin, $params)
	{
		$params[0] = (int) $params[0];
		try {
			$this->connection->setMaxPlayers($params[0]);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets server maximum players to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setServerMaxSpectators($fromLogin, $params)
	{
		$params[0] = (int) $params[0];
		try {
			$this->connection->setMaxSpectators($params[0]);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets server maximum spectators to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setServerPassword($fromLogin, $params)
	{
		try {
			$this->connection->setServerPassword($params[0]);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action# sets/unsets new server password.', null, array($admin->nickName));
			$this->exp_chatSendServerMessage('#admin_action#New server password:#variable# %s', null, array($params[0]), $fromLogin);
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setSpecPassword($fromLogin, $params)
	{
		try {
			$this->connection->setServerPasswordForSpectator($params[0]);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets/unsets new spectator password.', null, array($admin->nickName));
			$this->exp_chatSendServerMessage('#admin_action#New spectator password:#variable# %s', null, array($params[0]), $fromLogin);
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setRefereePassword($fromLogin, $params)
	{
		try {
			$this->connection->setRefereePassword($params[0]);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets/unsets new referee password.', null, array($admin->nickName));
			$this->exp_chatSendServerMessage('#admin_action#New referee password:#variable# %s', null, array($params[0]), $fromLogin);
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setserverchattime($fromLogin, $params)
	{
		$newLimit = TimeConversion::MStoTM($params[0]) - 8000;

		if ($newLimit < 0)
			$newLimit = 0;

		try {
			$this->connection->SetChatTime($newLimit);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin #variable#%s $z#admin_action#sets new chat time limit of #variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setTAdynamic($fromLogin, $params)
	{
		try {
			$this->dynamicTime = $params[0];
			$admin = $this->storage->getPlayerObject($fromLogin);
			if ($params[0] == 0) {
				$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action# disables the dynamic time limit!', null, array($admin->nickName));
				$this->exp_chatSendServerMessage('#admin_action#Static timelimit is set to #variable#5:00 #admin_action#minutes.');
				$this->connection->setTimeAttackLimit(300000);
				return;
			}
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets dynamic time limit multiplier to #variable# %s #admin_action#!', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			print $e->getMessage();
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setTAlimit($fromLogin, $params)
	{
		try {
			$this->connection->setTimeAttackLimit(TimeConversion::MStoTM($params[0]));
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets new time limit of #variable# %s #admin_action#minutes.', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			print $e->getMessage();
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setServerMapDownload($fromLogin, $params)
	{

		$bool = false;
		if ($params[0] == 'true' || $params[0] == 'false') {
			if ($params[0] == 'true')
				$bool = true;
			if ($params[0] == 'false')
				$bool = false;
		} else {
			$this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameter for the command is either true or false.');
			return;
		}

		try {
			$this->connection->allowMapDownload($bool);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#set allow download maps to#variable# %s', null, array($admin->nickName, $param1));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setHideServer($fromLogin, $params)
	{
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
		} else {
			$this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameters for command are: 0,1,2,visible,hidden,nations.');
			return;
		}
		try {
			$this->connection->setHideServer($output);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#set Hide Server to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function stopDedicated($fromLogin, $params)
	{
		try {
			$this->connection->sendHideManialinkPage();
			$this->connection->stopServer();
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function stopManiaLive($fromLogin, $params)
	{
		$this->connection->chatSendServerMessage("[Notice] stopping eXpansion...");
		$this->connection->sendHideManialinkPage();
		$this->connection->chatEnableManualRouting(false);
		Application::getInstance()->kill();
	}

	function restartManiaLive($fromLogin, $params)
	{
		Dispatcher::dispatch(new ExpansionEvent(ExpansionEvent::ON_RESTART_START));

		$this->exp_chatSendServerMessage("[Notice] restarting eXpansion...");
		$this->connection->sendHideManialinkPage();
		$this->connection->chatEnableManualRouting(false);

		Application::getInstance()->kill();

		$path = Path::getInstance();
		$dir = $path->getRoot(true) . "bootstrapper.php";
		$cmd = PHP_BINARY . " " . realpath($dir);

		//Getting the server arguments.
		$args = getopt(null, array(
			'help::', //Display Help
			'manialive_cfg::',
			'rpcport::', //Set the XML RPC Port to use
			'address::', //Set the adresse of the server
			'password::', //Set the User Password
			'dedicated_cfg::', //Set the configuration file to use to define XML RPC Port, SuperAdmin, Admin and User passwords
			'user::', //Set the user to use during the communication with the server
			'logsPrefix::', //Set the log prefix option
			'debug::' // Set up the debug option//Set a configuration file to load instead of config.ini
		));
		$arg_string = " ";
		foreach ($args as $key => $value) {
			$arg_string .= "--$key";
			if (!empty($value))
				$arg_string .= "=$value";
			$arg_string .= " ";
		}

		$cmd .= $arg_string;

		Helper::log('[ChatAdmin]Restarting manialive with command : ' . $cmd);

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			if (class_exists("COM")) {
				$WshShell = new \COM("WScript.Shell");
				$WshShell->Run($cmd, 3, false);
			}
			else {
				exec($cmd);
			}
		}
		else {
			exec("cd " . $path->getRoot(true) . "; " . $cmd . " >> /tmp/manialive.log 2>&1 &");
		}
		$this->console("eXpansion will restart!!This instance is stopping now!!");
		Dispatcher::dispatch(new ExpansionEvent(ExpansionEvent::ON_RESTART_END));
		exit();
	}

	function skipMap($fromLogin, $params)
	{
		try {
			\ManiaLive\Event\Dispatcher::dispatch(new GlobalEvent(GlobalEvent::ON_ADMIN_SKIP));
			$this->connection->nextMap($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#skips the challenge!', null, array($admin->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function restartMap($fromLogin, $params)
	{
		try {
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#restarts the challenge!', null, array($admin->nickName));
			if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\Maps\Maps')) {
				\ManiaLive\Event\Dispatcher::dispatch(new GlobalEvent(GlobalEvent::ON_ADMIN_RESTART));
				$this->callPublicMethod('\ManiaLivePlugins\eXpansion\Maps\Maps', "replayMapInstant");
				return;
			}
			\ManiaLive\Event\Dispatcher::dispatch(new GlobalEvent(GlobalEvent::ON_ADMIN_RESTART));
			$this->connection->restartMap($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP);
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function skipScoreReset($fromLogin, $params)
	{
		try {
			\ManiaLive\Event\Dispatcher::dispatch(new GlobalEvent(GlobalEvent::ON_ADMIN_SKIP));
			$this->connection->nextMap(false);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#skips the challenge!', null, array($admin->nickName));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function restartScoreReset($fromLogin, $params)
	{
		try {
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#restarts the challenge!', null, array($admin->nickName));
			if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\Maps\Maps')) {
				\ManiaLive\Event\Dispatcher::dispatch(new GlobalEvent(GlobalEvent::ON_ADMIN_RESTART));
				$this->callPublicMethod('\ManiaLivePlugins\eXpansion\Maps\Maps', "replayScoreReset");
				return;
			}
			\ManiaLive\Event\Dispatcher::dispatch(new GlobalEvent(GlobalEvent::ON_ADMIN_RESTART));
			$this->connection->restartMap(false);
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}


	function setGameMode($fromLogin, $params)
	{
		$gamemode = NULL;

		if (is_numeric($params[0])) {
			$gamemode = $params[0];
		}
		else {
			$param1 = $params[0];
			if (strtolower($param1) == " script")
				$gamemode = GameInfos::GAMEMODE_SCRIPT;
			if (strtolower($param1) == "rounds")
				$gamemode = GameInfos::GAMEMODE_ROUNDS;
			if (strtolower($param1) == "timeattack" || strtolower($param1) == "ta")
				$gamemode = GameInfos::GAMEMODE_TIMEATTACK;
			if (strtolower($param1) == "team")
				$gamemode = GameInfos::GAMEMODE_TEAM;
			if (strtolower($param1) == "laps")
				$gamemode = GameInfos::GAMEMODE_LAPS;
			if (strtolower($param1) == "stunts")
				$gamemode = GameInfos::GAMEMODE_STUNTS;
			if (strtolower($param1) == "cup")
				$gamemode = GameInfos::GAMEMODE_CUP;
			if ($gamemode === NULL) {
				$this->sendErrorChat($fromLogin, 'Invalid parameter. Valid parameteres are: script,team,timeattack,ta,rounds,laps,stunts,cup.');
				return;
			}
		}

		try {
			$this->connection->setGameMode($gamemode);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#sets game mode to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e
		) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function setAllWarmUpDuration($fromLogin, $params)
	{

		try {
			$this->connection->setAllWarmUpDuration($params[0]);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin #variable# %s #admin_action#sets all game modes warmup duration to#variable# %s', null, array($admin->nickName, $params[0]));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
			return;
		}
	}

	function cancelVote(
	$fromLogin)
	{
		$admin = $this->storage->getPlayerObject($fromLogin);
		$vote = $this->connection->getCurrentCallVote();
		if (!empty($vote->cmdName)) {
			try {
				$this->connection->cancelVote();
				$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#cancels the vote.', null, array($admin->nickName));
				return;
			} catch (Exception $e) {
				$this->exp_chatSendServerMessage('#admin_error#Error: Server said %1$s', $admin->login, array($e->getMessage()));
			}
		}
		else {
			$this->exp_chatSendServerMessage('#admin_error#Can\'t cancel a vote, no vote in progress!', $admin->login);
		}
	}

	function setDisableRespawn($fromLogin, $params)
	{
		if ($params[0] == 'true' || $params[0] == 'false') {
			if ($params[0] == 'true')
				$bool = false; // reverse the order as the command is for disable;
			if ($params[0] == 'false')
				$bool = true; // ^^
		} else {
			$this->sendErrorChat($fromLogin, 'Invalid parameter. Correct parameter for the command is either true or false.');
			return;
		}

		try {
			$this->connection->setDisableRespawn($bool);
			$admin = $this->storage->getPlayerObject($fromLogin);
			$this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#set allow respawn to #variable# %s', null, array($admin->nickName, $param1));
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	/* Graphical Methods */

	function showBanList($login)
	{
		GenericPlayerList::Erase($login);

		try {
			$window = GenericPlayerList::Create($login);
			$window->setTitle('Banned Players on the server');
			$indexNumber = 0;
			$items = array();

			/**
			 * @var PlayerBan
			 */
			foreach ($this->connection->getBanList(-1, 0) as $player) {
				$items[] = new BannedPlayeritem($indexNumber, $player, $this, $login);
			}
			$window->populateList($items);
			$window->setSize(90, 120);
			$window->centerOnScreen();
			$window->show();
		} catch (Exception $e) {
			$this->sendErrorChat($fromLogin, $e->getMessage());
		}
	}

	function showBlackList($login)
	{
		GenericPlayerList::Erase($login);

		//	try {
		$window = GenericPlayerList::Create($login);
		$window->setTitle(__('Blacklisted Players on the server', $login));
		$indexNumber = 0;
		$items = array();

		/**
		 * @var Player
		 */
		foreach ($this->connection->getBlackList(-1, 0) as $player) {
			$items[] = new BlacklistPlayeritem($indexNumber, $player, $this, $login);
		}
		$window->populateList($items);
		$window->setSize(90, 120);
		$window->centerOnScreen();
		$window->show();
		//} catch (Exception $e) {
		//		$this->sendErrorChat($login, "".$e->getMessage());
//		}
	}

	function showGuestList($login)
	{
		GenericPlayerList::Erase($login);

		try {
			$window = GenericPlayerList::Create($login);
			$window->setTitle(__('Guest Players on the server'));
			$indexNumber = 0;
			$items = array();

			/**
			 * @var Player
			 */
			foreach ($this->connection->getGuestList(-1, 0) as $player) {
				$items[] = new GuestPlayeritem($indexNumber, $player, $this, $login);
			}
			$window->populateList($items);
			$window->setSize(90, 120);
			$window->centerOnScreen();
			$window->show();
		} catch (Exception $e) {
			throw $e;
			$this->sendErrorChat($login, $e->getMessage());
		}
	}

	function showIgnoreList($login)
	{
		GenericPlayerList::Erase($login);

		try {
			$window = GenericPlayerList::Create($login);
			$window->setTitle(__('Ignored Players on the server'));
			$indexNumber = 0;
			$items = array();

			/**
			 * @var Player
			 */
			foreach ($this->connection->getIgnoreList(-1, 0) as $player) {
				$items[] = new IgnoredPlayeritem($indexNumber, $player, $this, $login);
			}
			$window->populateList($items);
			$window->setSize(90, 120);
			$window->centerOnScreen();
			$window->show();
		} catch (Exception $e) {
			$this->sendErrorChat($login, $e->getMessage());
		}
	}

	public function onStatusChanged($statusCode, $statusName)
	{
		if ($this->expStorage->simpleEnviTitle == Storage::TITLE_SIMPLE_TM && $statusCode == 6 && $this->dynamicTime > 0)
			if ($this->exp_getCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_TIMEATTACK) {
				$map = $this->connection->getNextMapInfo();
				$laps = $map->nbLaps;
				if ($map->nbLaps <= 1) {
					$laps = 1;
				}

				$newLimit = floor((intval($map->authorTime) / intval($laps)) * floatval($this->dynamicTime));

				$max = TimeConversion::MStoTM(Config::getInstance()->time_dynamic_max);
				$min = TimeConversion::MStoTM(Config::getInstance()->time_dynamic_min);

				if ($newLimit > $max) {
					$newLimit = $max;
				}
				if ($newLimit < $min) {
					$newLimit = $min;
				}
				if ($this->exp_getCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_SCRIPT) {
					$scriptLimit = $newLimit / 1000;
					$this->connection->setModeScriptSettings(array("S_TimeLimit" => $scriptLimit));
				}
				else {
					$this->connection->setTimeAttackLimit(intval($newLimit));
				}

				$this->exp_chatSendServerMessage('#admin_action#Dynamic time limit set to: #variable#' . Time::fromTM($newLimit), null);
			}
	}

	public function exp_onUnload()
	{
		parent::exp_onUnload();
		ParameterDialog::EraseAll();
	}

}

?>
