<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\adapter;

use ManiaLive\Utilities\Time;

/**
 * Description of oliverde8HudMenu
 *
 * @author oliverde8
 */
class oliverde8HudMenu {

    private $adminPlugin;
    private $menuPlugin;
    private $storage;
    private $connection;

    public function __construct($adminPlugin, $menu, $storage, $connection) {

        $this->adminPlugin = $adminPlugin;
        $this->menuPlugin = $menu;
        $this->storage = $storage;
        $this->connection = $connection;

        $this->generate_BasicCommands();
        $this->generate_PlayerLists();
        $this->generate_ServerSettings();
        $this->generate_GameSettings();
    }

    private function generate_BasicCommands() {
        $menu = $this->menuPlugin;

        $parent = $menu->findButton(array("admin", "Basic Commands"));
        $button["plugin"] = $this->adminPlugin;

        if (!$parent) {
            $button["style"] = "Icons64x64_1";
            $button["substyle"] = "GenericButton";
            $parent = $menu->addButton("admin", "Basic Commands", $button);
        }

        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "ClipPause";
        $button["function"] = "restartMap";
        $button["permission"] = "map_res";
        $buton = $menu->addButton($parent, "Restart Track", $button);
        $buton->setPermission('map_skip');

        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "ArrowNext";
        $button["function"] = "skipMap";
        $button["permission"] = "map_skip";
        $buton = $menu->addButton($parent, "Skip Track", $button);
        $buton->setPermission('map_res');

        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "ArrowLast";
        $button["function"] = "forceEndRound";
        $button["plugin"] = $this;
        $button["permission"] = "map_endRound";
        $button["checkFunction"] = "check_gameSettings_NoTimeAttack";
        $buton = $menu->addButton($parent, "End Round", $button);
        $buton->setPermission('map_endRound');
    }

    private function generate_PlayerLists() {

        $menu = $this->menuPlugin;

        $parent = $menu->findButton(array("admin", "Players"));
        $button["plugin"] = $this->adminPlugin;
        $button["style"] = "Icons128x128_1";
        if (!$parent) {
            $button["substyle"] = "Profile";
            $parent = $menu->addButton("admin", "Players", $button);
        }

        $button["substyle"]="Extreme";
        $button["function"] = "getBlacklist";
        $button["permission"] = "player_black";
        $menu->addButton($parent, "Black List", $button);
        
        $button["substyle"]="Hard";
        $button["function"] = "getBanList";
        $button["permission"] = "player_ban";
        $menu->addButton($parent, "Ban List", $button);
                
        $button["substyle"]="Medium";
        $button["function"] = "getIgnoreList";
        $button["permission"] = "player_ignore";
        $menu->addButton($parent, "Ignore List", $button);
        
        $button["substyle"]="Easy";
        $button["function"] = "getGuestList";
        $button["permission"] = "player_guest";
        $menu->addButton($parent, "Guest List", $button);
        
        $separator["seperator"] = true;
        $menu->addButton($parent, "Clear Lists", $separator);
        
        $button["substyle"]="Extreme";
        $button["function"] = "cleanblacklist";
        $button["permission"] = "player_black";
        $menu->addButton($parent, "Clear Black List", $button);
        
        $button["substyle"]="Hard";
        $button["function"] = "cleanbanlist";
        $button["permission"] = "player_ban";
        $menu->addButton($parent, "Clear Ban List", $button);
        
        
    }

    private function generate_GameSettings() {
        $menu = $this->menuPlugin;

        $parent = $menu->findButton(array('admin', 'Game Options'));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "ProfileAdvanced";
            $button["plugin"] = $this;
            $button["permission"] = "game_settings";
            $parent = $menu->addButton("admin", "Game Options", $button);
        }

        $this->gameSettings_GameMode($parent);

        $this->gameSettings_Rounds($parent);
        $this->gameSettings_TimeAttack($parent);
        $this->gameSettings_Team($parent);
        $this->gameSettings_Laps($parent);
        //$this->gameSettings_Stunts($parent);
        $this->gameSettings_Cup($parent);
    }

    private function generate_ServerSettings() {
        $menu = $this->menuPlugin;

        $parent = $menu->findButton(array('admin', 'Server Options'));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Options";
            $button["plugin"] = $this;
            $parent = $menu->addButton("admin", "Server Options", $button);
            unset($button["style"]);
        }
        $separator["seperator"] = true;
        
        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Quit";
        
        $button["plugin"] = $this->adminPlugin;
        $button["function"] = "stopManiaLive";
        $button["permission"] = "server_manialive";
        $menu->addButton($parent, "Stop ManiaLive", $button);
        
        $button["plugin"] = $this->adminPlugin;
        $button["function"] = "stopDedicated";
        $button["permission"] = "server_dedistop";
        $menu->addButton($parent, "Stop Dedicated", $button);
        
        unset($button["style"]);
        unset($button["substyle"]);
        $button["forceRefresh"] = true;
        $button["plugin"] = $this;
        $button["function"] = "ServerSettings_setDisableRespawn";
        $button["permission"] = "server_admin";
        $button["switchFunction"] = "ServerSettings_getDisableRespawn";
        $menu->addButton($parent, "Disable Respawn", $button);

        $button["plugin"] = $this;
        $button["function"] = "ServerSettings_setMapDownload";
        $button["permission"] = "server_admin";
        $button["switchFunction"] = "ServerSettings_getMapDownload";
        $menu->addButton($parent, "Challlange Dwld", $button);
        
        $button["plugin"] = $this;
        $button["function"] = "ServerSettings_setHide";
        $button["permission"] = "server_admin";
        $button["switchFunction"] = "ServerSettings_getHide";
        $menu->addButton($parent, "Hide Server", $button);
        unset($button["switchFunction"]);
        
        $menu->addButton($parent, "Passwords ...", $separator);
        
        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Profile";
        
        $button["plugin"] = $this->adminPlugin;
        $button["function"] = "setServerPassword";
        $button["permission"] = "server_password";
        $button['params'] = "";
        $menu->addButton($parent, "Reset Player Pwd", $button);

        $button["plugin"] = $this->adminPlugin;
        $button["function"] = "setSpecPassword";
        $button["permission"] = "server_specpwd";
        $button['params'] = "";
        $menu->addButton($parent, "Reset Spec Pwd", $button);
        
        $button["substyle"] = "PlayerPage";
        $button["plugin"] = $this->adminPlugin;
        $button["function"] = "setSpecPassword";
        $button["permission"] = "server_refpwd";
        $button['params'] = "";
        $menu->addButton($parent, "Reset Ref Pwd", $button);

        $menu->addButton($parent, "Other ...", $separator);
    }

    public function ServerSettings_setDisableRespawn($login, $params) {
        $respawn = $this->connection->getDisableRespawn();
        if ($respawn['NextValue'])
            $val = "true";
        else
            $val = "false";

        $this->adminPlugin->setDisableRespawn($login, array($val));
    }

    public function ServerSettings_getDisableRespawn() {
        return $this->connection->getDisableRespawn();
    }
    
    public function ServerSettings_getHide() {
        return $this->connection->getHideServer();
    }

    public function ServerSettings_setHide($login) {

        if (!$this->connection->getHideServer())
            $val = "all";
        else
            $val = "off";

        $this->adminPlugin->setHideServer($login, $val);
    }
    
    public function ServerSettings_getMapDownload() {
        return $this->connection->isMapDownloadAllowed();
    }

    public function ServerSettings_setMapDownload($login) {

        if (!$this->connection->isMapDownloadAllowed())
            $val = "true";
        else
            $val = "false";

        $this->adminPlugin->setServerMapDownload($login, $val);
    }

    private function gameSettings_GameMode($parent) {

        $menu = $this->menuPlugin;

        $button["plugin"] = $this->adminPlugin;
        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "ProfileAdvanced";
        $button["permission"] = "game_gamemode";
        $gmode = $menu->addButton($parent, "Game Mode", $button);

        $modes = array("Rounds", "TimeAttack", "Team", "Laps", "Cup");
        $modes2 = array("rounds", "ta", "team", "laps", "cup");
        for ($i = 0; $i < 5; $i++) {
            $new['style'] = 'Icons128x32_1';
            $new["substyle"] = 'RT_' . $modes[$i];
            $new["plugin"] = $this;
            $new['function'] = 'setGameMode';
            $new['params'] = $modes2[$i];
            $new["forceRefresh"] = "true";

            $b = $menu->addButton($gmode, 'Set To:' . $modes[$i], $new);
            $b->setParamsAsArray(true);
            unset($new);
        }
    }

    private function gameSettings_TimeAttack($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["style"] = 'Icons128x32_1';
        $button["substyle"] = "RT_TimeAttack";
        $button['function'] = 'check_gameSettings_TimeAttack';
        $button["permission"] = "game_settings";
        $button["checkFunction"] = "check_gameSettings_TimeAttack";
        $parent = $menu->addButton($parent, "TA Settings", $button);

        $this->generate_GameSettings_WarmUp($parent);
        $this->generate_GameSettings_FinishTimeout($parent);
        $this->generate_GameSettings_TATimeLimit($parent);
    }

    public function check_gameSettings_NoTimeAttack() {
        return !$this->check_gameSettings_TimeAttack();
    }

    public function check_gameSettings_TimeAttack() {
        return $this->connection->getNextGameInfo()->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK;
    }

    private function gameSettings_Rounds($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["style"] = 'Icons128x32_1';
        $button["substyle"] = "RT_rounds";
        $button['function'] = 'check_gameSettings_Rounds';
        $button["permission"] = "game_settings";
        $button["checkFunction"] = "check_gameSettings_Rounds";
        $parent = $menu->addButton($parent, "Round Settings", $button);

        $this->generate_GameSettings_WarmUp($parent);
        $this->generate_GameSettings_FinishTimeout($parent);
        $this->generate_GameSettings_RoundPointsLimit($parent);
        $this->generate_GameSettings_RoundForcedLaps($parent);
        $this->generate_GameSettings_RoundUseNewRules($parent);
    }

    public function check_gameSettings_Rounds() {
        return $this->connection->getNextGameInfo()->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS;
    }

    private function gameSettings_Team($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["style"] = 'Icons128x32_1';
        $button["substyle"] = "RT_Team";
        $button['function'] = 'check_gameSettings_Team';
        $button["permission"] = "game_settings";
        $button["checkFunction"] = "check_gameSettings_Team";
        $parent = $menu->addButton($parent, "Team Settings", $button);

        $this->generate_GameSettings_WarmUp($parent);
        $this->generate_GameSettings_FinishTimeout($parent);
        $this->generate_GameSettings_TeamPointsLimit($parent);
    }

    public function check_gameSettings_Team() {
        return $this->connection->getNextGameInfo()->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM;
    }

    private function gameSettings_Laps($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["style"] = 'Icons128x32_1';
        $button["substyle"] = "RT_Laps";
        $button['function'] = 'check_gameSettings_Laps';
        $button["permission"] = "game_settings";
        $button["checkFunction"] = "check_gameSettings_Laps";
        $parent = $menu->addButton($parent, "Laps Settings", $button);

        $this->generate_GameSettings_WarmUp($parent);
        $this->generate_GameSettings_FinishTimeout($parent);
        $this->generate_GameSettings_LapsTimeLimit($parent);
        $this->generate_GameSettings_LapsNbLaps($parent);
    }

    public function check_gameSettings_Laps() {
        return $this->connection->getNextGameInfo()->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS;
    }

    private function gameSettings_Cup($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["style"] = 'Icons128x32_1';
        $button["substyle"] = "RT_Cup";
        $button['function'] = 'check_gameSettings_Cup';
        $button["permission"] = "game_settings";
        $button["checkFunction"] = "check_gameSettings_Cup";
        $parent = $menu->addButton($parent, "Cup Settings", $button);

        $this->generate_GameSettings_WarmUp($parent);
        $this->generate_GameSettings_FinishTimeout($parent);
        $this->generate_GameSettings_CupPointsLimit($parent);
        $this->generate_GameSettings_CupNbWinners($parent);
        $this->generate_GameSettings_CupRoundsPerChallenge($parent);
    }

    public function check_gameSettings_Cup() {
        return $this->connection->getNextGameInfo()->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP;
    }

    private function generate_GameSettings_CupRoundsPerChallenge($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;

        $wup = $menu->addButton($parent, "Round Par Challenge", $button);

        $times = array(1, 2, 3, 4, 5, 7, 8, 10, 12, 15, 20, 25, 30);
        foreach ($times as $Time) {
            $new['function'] = 'setCupRoundsPerMap';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 1) {
                $b = $menu->addButton($wup, "Disable", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_CupNbWinners($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button['style'] = 'Icons64x64_1';
        $button["substyle"] = 'OfficialRace';
        $wup = $menu->addButton($parent, "Nb Winners", $button);

        $times = array(1, 2, 3, 5, 7, 8, 10, 12, 15, 20, 25, 30);
        foreach ($times as $Time) {
            $new['style'] = 'Icons64x64_1';
            $new["substyle"] = 'OfficialRace';
            $new['function'] = 'setCupNbWinners';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 1) {
                $b = $menu->addButton($wup, "Disable", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_CupPointsLimit($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button['style'] = 'BgRaceScore2';
        $button["substyle"] = 'Points';
        $wup = $menu->addButton($parent, "Points Limit", $button);

        $times = array(10, 20, 30, 40, 50, 75, 100, 120, 150);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'Points';
            $new['function'] = 'setCupPointsLimit';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 1) {
                $b = $menu->addButton($wup, "Disable", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_LapsTimeLimit($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button['style'] = 'BgRaceScore2';
        $button["substyle"] = 'SendScore';
        $wup = $menu->addButton($parent, "Time Limit", $button);

        $times = array(0, 10, 30, 60, 90, 120, 180, 240, 300);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'SandTimer';
            $new['function'] = 'setLapsTimeLimit';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $this->formatTime($Time);

            if ($Time == 1) {
                $b = $menu->addButton($wup, "Disable", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_LapsNbLaps($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $wup = $menu->addButton($parent, "Nb Laps", $button);

        $times = array(1, 2, 5, 8, 10, 20, 25, 30, 45, 50);
        foreach ($times as $Time) {
            $new['function'] = 'setNbLaps';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 1) {
                $b = $menu->addButton($wup, "Disable", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_TeamPointsLimit($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button['style'] = 'BgRaceScore2';
        $button["substyle"] = 'Points';
        $wup = $menu->addButton($parent, "Point Limit", $button);

        $times = array(10, 15, 20, 30, 40, 50, 75, 100, 120, 150);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'Points';
            $new['function'] = 'setTeamPointsLimit';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 1) {
                $b = $menu->addButton($wup, "Disable", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    public function setGameMode($login, $params) {
        $this->adminPlugin->setGameMode($login, $params);
    }

    public function forceEndRound($fromLogin) {
        $this->adminPlugin->forceEndRound($fromLogin);
    }

    private function generate_GameSettings_WarmUp($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["style"] = 'BgRaceScore2';
        $button["substyle"] = "Warmup";
        $wup = $menu->addButton($parent, "Warm Up Duration", $button);

        $times = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 10);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'SandTimer';
            $new['function'] = 'setAllWarmUpDuration';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 0) {
                $b = $menu->addButton($wup, "Close it", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_FinishTimeout($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $wup = $menu->addButton($parent, "Finish Time Out", $button);

        $times = array(0, 10, 20, 30, 45, 60, 90, 120);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'SandTimer';
            $new['function'] = 'setFinishTimeout';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $this->formatTime($Time);

            $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            $b->setParamsAsArray(true);
            unset($new);
        }
    }

    public function generate_GameSettings_TATimeLimit($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["style"] = 'BgRaceScore2';
        $button["substyle"] = "SendScore";
        $wup = $menu->addButton($parent, "Time Limit", $button);

        $times = array(30, 60, 90, 120, 180, 240, 300, 360, 420, 480, 600, 720, 900, 1200);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'SandTimer';
            $new['function'] = 'setTAlimit';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $this->formatTime($Time);
            $b = $menu->addButton($wup, "Set to : " . Time::fromTM($Time * 1000, false), $new);
            $b->setParamsAsArray(true);
            unset($new);
        }
    }

    private function generate_GameSettings_RoundPointsLimit($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button['style'] = 'BgRaceScore2';
        $button["substyle"] = 'Points';
        $wup = $menu->addButton($parent, "Point Limit", $button);

        $times = array(10, 20, 30, 40, 50, 75, 100, 120, 150);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'Points';
            $new['function'] = 'setRoundPointsLimit';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 0) {
                $b = $menu->addButton($wup, "Close it", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_RoundForcedLaps($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button['style'] = 'BgRaceScore2';
        $button["substyle"] = 'Laps';
        $wup = $menu->addButton($parent, "Forced Laps", $button);

        $times = array(1, 2, 5, 8, 10, 20, 25, 30, 45, 50);
        foreach ($times as $Time) {
            $new['style'] = 'BgRaceScore2';
            $new["substyle"] = 'Laps';
            $new['function'] = 'setRoundForcedLaps';
            $new["plugin"] = $this->adminPlugin;
            $new["params"] = $Time;

            if ($Time == 1) {
                $b = $menu->addButton($wup, "Disable", $new);
            } else {
                $b = $menu->addButton($wup, "Set to : " . $Time, $new);
            }
            $b->setParamsAsArray(true);

            unset($new);
        }
    }

    private function generate_GameSettings_RoundUseNewRules($parent) {
        $menu = $this->menuPlugin;

        $button["plugin"] = $this;
        $button["function"] = "save_GameSettings_RoundUseNewRules";
        $button["switchFunction"] = "get_GameSettings_RoundUseNewRules";
        $button["forceRefresh"] = true;

        $wup = $menu->addButton($parent, "Use New Rules", $button);
    }

    public function save_GameSettings_RoundUseNewRules($login) {
        $val = $this->get_GameSettings_RoundUseNewRules() ? 'false' : 'true';
        $this->adminPlugin->setUseNewRulesRound($login, array($val));
    }

    public function get_GameSettings_RoundUseNewRules() {
        return $this->connection->getNextGameInfo()->roundsUseNewRules;
    }

    private function formatTime($secs) {
        $min = (int) ($secs / 60);
        $sec = $secs - ($min * 60);
        return sprintf('%1$02d:%2$02d', $min, $sec);
    }

}

?>
