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
		//$this->generate_ServerSettings();
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
		$buton = $menu->addButton($parent, "Restart Track", $button);
		$buton->setPermission('map_skip');

		$button["style"] = "Icons64x64_1";
		$button["substyle"] = "ArrowNext";
		$button["function"] = "skipMap";
		$buton = $menu->addButton($parent, "Skip Track", $button);
		$buton->setPermission('map_res');

		$button["style"] = "Icons64x64_1";
		$button["substyle"] = "ArrowLast";
		$button["function"] = "forceEndRound";
		$button["plugin"] = $this;
		$button["checkFunction"] = "check_gameSettings_NoTimeAttack";
		$buton = $menu->addButton($parent, "End Round", $button);
		$buton->setPermission('map_roundEnd');
	}
    
    private function generate_PlayerLists() {

		$menu = $this->menuPlugin;

        $parent = $menu->findButton(array("admin", "Players"));
		$button["plugin"] = $this->adminPlugin;
		if (!$parent) {
			$button["style"] = "Icons128x128_1";
            $button["substyle"] = "Profile";
            $parent = $menu->addButton("admin", "Players", $button);
		}
        
        unset($button["style"]);
        unset($button["substyle"]);
		//The buttons
		$button["function"] = "getBlackList";
		$menu->addButton($parent, "In Black List", $button);

		$button["function"] = "getGuestList";
		$menu->addButton($parent, "In Guest List", $button);

		$button["function"] = "getIgnoreList";
		$menu->addButton($parent, "In Ignore List", $button);

		$button["function"] = "getBanList";
		$menu->addButton($parent, "In Ban List", $button);
	}
    
    private function generate_GameSettings() {
		$menu = $this->menuPlugin;
        
        $parent = $menu->findButton(array('admin','Game Options'));
        if(!$parent){
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "ProfileAdvanced";        
            $button["plugin"] = $this;
            $parent = $menu->addButton("admin", "Game Options", $button);
        }

		$this->gameSettings_GameMode($parent);

		$this->gameSettings_Rounds($parent);
		$this->gameSettings_TimeAttack($parent);
		//$this->gameSettings_Team($parent);
		//$this->gameSettings_Laps($parent);
		//$this->gameSettings_Stunts($parent);
		//$this->gameSettings_Cup($parent);
	}
    
    private function gameSettings_GameMode($parent) {

		$menu = $this->menuPlugin;

		$button["plugin"] = $this->adminPlugin;
		$button["style"] = "Icons128x128_1";
		$button["substyle"] = "ProfileAdvanced";
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
		$button["checkFunction"] = "check_gameSettings_TimeAttack";
		$parent = $menu->addButton($parent, "TA Settings", $button);

		$this->generate_GameSettings_WarmUp($parent);
		$this->generate_GameSettings_FinishTimeout($parent);
		$this->generate_GameSettings_TATimeLimit($parent);
	}
    
    public function check_gameSettings_NoTimeAttack(){
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
			$new["params"] = $Time;

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

		$times = array(30, 60, 90, 120, 180, 240, 300, 360, 420, 480,600,720,900,1200);
		foreach ($times as $Time) {
			$new['style'] = 'BgRaceScore2';
			$new["substyle"] = 'SandTimer';
			$new['function'] = 'setTAlimit';
			$new["plugin"] = $this->adminPlugin;
			$new["params"] = $this->formatTime($Time);
			$b = $menu->addButton($wup, "Set to : " .  Time::fromTM($Time * 1000, false), $new);
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
    public function save_GameSettings_RoundUseNewRules($login){
        $val = $this->get_GameSettings_RoundUseNewRules() ? 'false' : 'true';
        $this->adminPlugin->setUseNewRulesRound($login,array($val));
    }
    public function get_GameSettings_RoundUseNewRules(){
        return $this->connection->getNextGameInfo()->roundsUseNewRules;
    }
		
    private function formatTime($secs){
        $min = (int)($secs/60);
        $sec = $secs - ($min * 60);
        return sprintf('%1$02d:%2$02d', $min, $sec);
    }
    
   
}

?>
