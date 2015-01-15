<?php

namespace ManiaLivePlugins\eXpansion\Gui\Controls;

/**
 * Description of ConfigOption
 *
 * @author Reaby
 */
class ConfigOption extends \ManiaLivePlugins\eXpansion\Gui\Control {

    protected $cb_item;
    private $status;

    function __construct($x, \ManiaLivePlugins\eXpansion\Gui\Structures\ConfigItem $status, $login, $sizeX) {
	$this->status = $status;
	$this->setSize(60, 5);
	$this->cb_item = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4, 50);
	$this->cb_item->setStatus($status->value);
	// $this->cb_item->setText($status->id . " (" . $this->getGamemodeName($status->gameMode) . ")");
	$this->cb_item->setText($status->id);
	$this->addComponent($this->cb_item);
    }

    private function getGamemodeName($mode) {	
	if (is_numeric($mode)) {
	    if ($mode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT)
		return "Script";
	    if ($mode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP)
		return "Cup";
	    if ($mode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS)
		return "Laps";
	    if ($mode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS)
		return "Rounds";
	    if ($mode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM)
		return "Team";
	    if ($mode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK)
		return "TA";
	}
	return "" . $mode;
    }

    public function getText() {
	return $this->status->id;
    }

    public function getStatus() {
	return $this->cb_item->getStatus();
    }

    public function getGamemode() {
	return $this->status->gameMode;
    }

    public function destroy() {
	
    }

    public function erase() {
	$this->destroyComponents();
	parent::destroy();
    }

}
