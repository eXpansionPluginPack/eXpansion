<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

class Permission
{

    const ADMINGROUPS_ONLY_OWN_GROUP = 'admingroups_onlyOwnGroup';
    const ADMINGROUPS_ADMIN_ALL_GROUPS = "admingroups_adminAllGroups";
    //Concerning Players
    const PLAYER_BLACK = 'player_black';
    const PLAYER_UNBLACK = 'player_unblack';
    const PLAYER_BAN = 'player_ban';
    const PLAYER_UNBAN = 'player_unban';
    const PLAYER_IGNORE = 'player_ignore';
    const PLAYER_FORCESPEC = 'player_forcespec';
    const PLAYER_KICK = 'player_kick';
    const PLAYER_GUEST = 'player_guest';
    const PLAYER_CHANGE_TEAM = 'player_changeTeam';

    //concerning Server Settings
    const SERVER_ADMIN = 'server_admin';
    const SERVER_STOP_DEDICATED = 'server_stopDedicated';
    const SERVER_STOP_MANIALIVE = 'server_stopManialive';
    const SERVER_NAME = 'server_name';
    const SERVER_COMMENT = 'server_comment';
    const SERVER_PASSWORD = 'server_password';
    const SERVER_SPECPWD = 'server_specpwd';
    const SERVER_REFPWD = 'server_refpwd';
    const SERVER_MAXPLAYER = 'server_maxplayer';
    const SERVER_MAXSPEC = 'server_maxspec';
    const SERVER_CHATTIME = 'server_chattime';
    const SERVER_REFMODE = 'server_refmode';
    const SERVER_LADDER = 'server_ladder';
    const SERVER_VOTES = 'server_votes';
    const SERVER_CONTROL_PANEL = 'server_controlPanel';
    const SERVER_DATABASE = 'server_database';
    const SERVER_GENERIC_OPTIONS = 'server_genericOptions';
    const SERVER_PLANETS = 'server_planets';
    // conserning expansion
    const SERVER_UPDATE = "server_update";
    const EXPANSION_PLUGIN_SETTINGS = "expansion_pluginSettings";
    const EXPANSION_PLUGIN_START_STOP = "expansion_pluginStartStop";
    //Concerning Game Settings      
    const GAME_GAMEMODE = 'game_gamemode';
    const GAME_SETTINGS = 'game_settings';
    const GAME_MATCH_SAVE = 'game_matchSave';
    const GAME_MATCH_DELETE = 'game_matchDelete';
    const GAME_MATCH_SETTINGS = 'game_matchSettings';
    // concerning maps
    const MAP_SKIP = 'map_skip';
    const MAP_RES = 'map_res';
    const MAP_END_ROUND = 'map_endRound';
    const MAP_ADD_LOCAL = 'map_addLocal';
    const MAP_ADD_MX = 'map_addMX';
    const MAP_REMOVE_MAP = 'map_removeMap';
    const MAP_JUKEBOX_ADMIN = "map_jukebox_admin";
    const MAP_JUKEBOX_FREE = "map_jukebox_free";

    // special permissions
    const TEAM_BALANCE = 'team_balance';

    const CHAT_ADMINCHAT = "chat_adminchat";
    const CHAT_ON_DISABLED = "chat_onDisabled";
    const QUIZ_ADMIN = "quiz_admin";

    const LOCAL_RECORDS_DELETE = 'localRecords_delete';

}

?>
