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

/**
 * Description of TopPanel
 *
 * @author Reaby
 */
class TopPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

	private $bg, $logo;

	private $frameRight, $frameLeft;

	private $script;

	/** @var \ManiaLive\Data\Storage 	 */
	private $storage;

	protected function exp_onBeginConstruct()
	{
		$this->setName("Top Panel");
		$this->storage = \ManiaLive\Data\Storage::getInstance();

		$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle(320, 9);
		$this->bg->setOpacity(0.5);
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


		$this->frameRight = new \ManiaLive\Gui\Controls\Frame(0, -1.5);
		$this->frameRight->setAlign("right");
		$this->frameRight->setLayout($line);
		$this->addComponent($this->frameRight);

		$ladder = "Ladder limits " . ( $this->storage->server->ladderServerLimitMin / 1000 ) . " - " . ( $this->storage->server->ladderServerLimitMax / 1000 ) . "k";
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\PanelItem($ladder, "", 48, "Icons64x64_1", "ToolLeague1");
		$item->setId('serverName');
		$this->frameLeft->addComponent($item);

		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\PanelItem("", "", 48, "Icons128x128_1", "Race");
		$item->setId('mapName');
		$item->setIdTitle('mapAuthor');
		$item->setQuadId("mapIcon");
		$this->frameLeft->addComponent($item);

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
		$this->frameLeft->addComponent($item);




		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\PanelItem("Players", "", 16, "Icons64x64_1", "Buddy");
		$item->setId('nbPlayer');

		$this->frameLeft->addComponent($item);
		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\PanelItem("Spectators", "", 16, "Icons64x64_1", "TV");
		$item->setId('nbSpec');

		$this->frameLeft->addComponent($item);





		$item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\PanelItem("Local Time", "", 16, "BgRaceScore2", "SendScore");
		$item->setId('clock');
		$this->frameRight->addComponent($item);

		$offset = 0;
		foreach ($this->frameRight->getComponents() as $panel) {
			$offset += $panel->getSizeX();
		}

		$this->frameRight->setPosX(320 - $offset, 0);


		$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_TM_topPanel\Gui\Scripts");
		$this->script->setParam("maxSpec", $this->storage->server->currentMaxSpectators);
		$this->script->setParam("maxPlayers", $this->storage->server->currentMaxPlayers);

		$this->registerScript($this->script);
	}

	protected function exp_onEndConstruct()
	{
		$this->setPosition(-160, 90);
	}

}
