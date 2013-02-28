<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\adapter;

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
		//$this->generate_GameSettings();
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
    
    
    public function forceEndRound($fromLogin) {
		$this->adminPlugin->forceEndRound($fromLogin);
	}
    public function check_gameSettings_NoTimeAttack(){
		return !$this->check_gameSettings_TimeAttack();
	}
    public function check_gameSettings_TimeAttack() {
		return $this->connection->getNextGameInfo()->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK;
    }
}

?>
