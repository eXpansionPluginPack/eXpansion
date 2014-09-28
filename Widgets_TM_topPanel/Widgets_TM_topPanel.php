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

namespace ManiaLivePlugins\eXpansion\Widgets_TM_topPanel;

/**
 * Description of Widgets_TM_topPanel
 *
 * @author Reaby
 */
class Widgets_TM_topPanel extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	/** @var  \ManiaLive\PluginHandler\PluginHandler */
	private $pluginhandler;

	public function exp_onReady()
	{

		$this->pluginhandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
		$pluginsToUnload = array("Widgets_ServerInfo", "Widgets_Clock", "Widgets_BestCheckpoints");

		foreach ($pluginsToUnload as $plugin) {
			if ($this->isPluginLoaded($this->getPluginId($plugin))) {
				$this->pluginhandler->unload($this->getPluginId($plugin));
			}
		}

		$this->enableDedicatedEvents();
		$widget = Gui\Widgets\TopPanel::Create(null);
		$widget->show();
	}

	public function onBeginMatch()
	{
		Gui\Widgets\TopPanel::EraseAll();
		$widget = Gui\Widgets\TopPanel::Create(null);
		$widget->show();
	}

	private function getPluginId($plugin)
	{
		return '\\ManiaLivePlugins\\eXpansion\\' . $plugin . '\\' . $plugin;
	}

	public function exp_onUnload()
	{
		Gui\Widgets\TopPanel::EraseAll();
	}

}
