<?php

namespace ManiaLivePlugins\eXpansion\LoadScreen;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Preloader;
use ManiaLivePlugins\eXpansion\LoadScreen\Gui\Windows\LScreen;
use ManiaLivePlugins\eXpansion\LoadScreen\Config;

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

/**
 * Description of Widgets_Speedometer
 *
 * @author Reaby
 */
class LoadScreen extends ExpPlugin
{

	private $startTime = 0;

	private $isActive = false;

	public function exp_onReady()
	{
		$this->enableDedicatedEvents();
		$this->enableTickerEvent();

		$config = Config::getInstance();
		foreach ($config->screens as $url) {
			Gui::preloadImage($url);
		}
		Gui::updatePreloader();
	}

	public function onSettingsChanged(Variable $var)
	{
		if ($var->getName() == "screens") {
			$config = Config::getInstance();
			foreach ($config->screens as $url) {
				Gui::preloadImage($url);
			}
		}
		Gui::updatePreloader();
	}

	public function onTick()
	{
		if ($this->isActive == true && time() > ($this->startTime + (($this->storage->gameInfos->chatTime / 1000) + 7) )) {

			$this->isActive = false;
			$this->startTime = 0;
			$widget = LScreen::Create(null);
			$widget->setName("loading Screen");
			$widget->show();
		}
	}

	public function onEndMatch($rankings, $winnerTeamOrMap)
	{
		$this->startTime = time();
		$this->isActive = true;
	}

	public function onBeginMap($map, $warmUp, $matchContinuation)
	{
		$this->isActive = false;
		LScreen::EraseAll();
	}

	public function onBeginMatch()
	{
		$this->isActive = false;
		LScreen::EraseAll();
	}

	public function exp_onUnload()
	{
		
	}

}
