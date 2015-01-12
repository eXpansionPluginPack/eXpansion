<?php

/*
 * Copyright (C) 2014 Reaby
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Widgets;

use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\HookData;
use ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Hooks\BarElements;
use ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Hooks\HookElement;

/**
 * Description of TopPanel
 *
 * @author Reaby
 */
class TopPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

	protected $bg, $logo;

	protected $frameRight, $frameLeft;

	protected $script;

	/** @var \ManiaLive\Data\Storage 	 */
	protected $storage;

	protected function onConstruct()
	{
		parent::onConstruct();
		$this->setPosition(-160, 90);

		$this->setName("Top Panel");
		$this->storage = \ManiaLive\Data\Storage::getInstance();

		$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(320, 9);
		//$this->bg->setOpacity(0.5);
		$this->addComponent($this->bg);

		$config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
		$this->logo = new \ManiaLib\Gui\Elements\Quad(38, 12);
		$this->logo->setImage($config->logo, true);
		$this->logo->setAlign("center", "top");
		$this->logo->setPosition(160, 2);
		// $this->addComponent($this->logo);

		$line = new \ManiaLib\Gui\Layouts\Line();
		$line->setMargin(1, 1);

		$this->frameLeft = new \ManiaLive\Gui\Controls\Frame(3, -1.5);
		$this->frameLeft->setLayout($line);
		$this->addComponent($this->frameLeft);

		$line = new \ManiaLib\Gui\Layouts\Line();
		$line->setMargin(1, 1);
		$line->setAlign("right");

		$offset = 0;
		foreach($this->hookLeft() as $elem){
			$this->frameLeft->addComponent($elem->gui);
		}

		$this->frameRight = new \ManiaLive\Gui\Controls\Frame(0, -1.5);
		$this->frameRight->setAlign("right");
		$this->frameRight->setLayout($line);
		$this->addComponent($this->frameRight);

		foreach($this->hookRight() as $elem){
			$this->frameRight->addComponent($elem->gui);
			$offset += $elem->gui->getSizeX();
		}

		$this->frameRight->setPosX(320 - $offset, 0);
	}

	/**
	 * @return HookElement[]
	 */
	protected function hookLeft(){

		$elements = array();


   $elements['serverName'] = new HookElement($this->getServerNameItem(), 1000);
	//	$elements['gameInfo'] = new HookElement($this->getGameModeItem(), 50);
	//	$elements['clock'] = new HookElement($this->getClock());
	//	$elements['nbPlayer'] = new HookElement($this->getNbPlayer(), 30);
	//	$elements['nbSpec'] = new HookElement($this->getNbSpectators(), 20);

		$hook = new HookData();
		$hook->data = $elements;

		//\ManiaLive\Event\Dispatcher::dispatch(
		//	new BarElements(BarElements::ON_LEFT_CREATE, $hook, 'test')
		//);

		usort($hook->data, array($this, 'cmp'));
		return $hook->data;
	}

	/**
	 * @return HookElement[]
	 */
	protected function hookRight(){

		$elements = array();

		$elements['mapInfo'] = new HookElement($this->getMapInfo(), 999);
		

		$hook = new HookData();
		$hook->data = $elements;

		//\ManiaLive\Event\Dispatcher::dispatch(
		//	new BarElements(BarElements::ON_LEFT_CREATE, $hook, 'test')
		//);

		usort($hook->data, array($this, 'cmp'));
		return $hook->data;
	}

	protected function getServerNameItem(){
		$ladder = "Ladder limits " . ( $this->storage->server->ladderServerLimitMin / 1000 ) . " - " . ( $this->storage->server->ladderServerLimitMax / 1000 ) . "k";
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\ServerInfoItem($ladder, "", 64, "Icons64x64_1", "ToolLeague1");
		$item->setId('serverName');

		return $item;
	}

	protected function getGameModeItem(){
		$gamemode = "";
		$scriptName = "";
		$desc = "";
		switch ($this->storage->gameInfos->gameMode) {
			case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT:
				$gamemode = "Script";
				$desc = str_replace(".script.txt", "", strtolower($this->storage->gameInfos->scriptName));
				break;
			case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS:
				$gamemode = "Rounds";
				$desc = "Limit at " . $this->storage->gameInfos->roundsPointsLimit;
				if ($this->storage->gameInfos->roundsUseNewRules) {
					$gamemode = "Rounds (New rules)";
					$desc = "Limit at " . $this->storage->gameInfos->roundsPointsLimitNewRules;
				}

				break;
			case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK:
				$gamemode = "TimeAttack";
				$desc = "Playing for " . \ManiaLive\Utilities\Time::fromTM($this->storage->gameInfos->timeAttackLimit);
				break;
			case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM:
				$gamemode = "Team";
				$desc = "Limit at " . $this->storage->gameInfos->teamPointsLimit;
				if ($this->storage->gameInfos->teamUseNewRules) {
					$gamemode = "Team (New rules)";
					$desc = "Limit at " . $this->storage->gameInfos->teamPointsLimitNewRules;
				}
				break;
			case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS:
				$gamemode = "Laps";
				$desc = "Playing " . $this->storage->gameInfos->lapsNbLaps . " laps";
				break;
			case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP:
				$gamemode = "Cup";
				$desc = "Limit at " . $this->storage->gameInfos->cupPointsLimit;
				break;
		}
		$icon = $gamemode;
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\PanelItem($desc, $gamemode, 32, "Icons128x32_1", "RT_" . $icon);

		return $item;
	}

	protected function getMapInfo(){
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\MapInfoItem("", "", 32, "Icons128x128_1", "Race");
		$item->setId('mapName');
		$item->setIdTitle('mapAuthor');
		$item->setQuadId("mapIcon");
		return $item;
	}

	protected function getNbPlayer(){
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\NbPlayerItem("Players", "", 16);
		$item->setId('nbPlayer');
		return $item;
	}

	protected function getNbSpectators(){
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\NbSpecItem("Spectators", "", 16);
		$item->setId('nbSpec');
		return $item;
	}

	protected function getClock(){
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\ClockItem("Current Time", "", 24);
		$item->setId('clock');
		return $item;
	}

	public function cmp(HookElement $a, HookElement $b){
		return $b->priority - $a->priority;
	}
}
