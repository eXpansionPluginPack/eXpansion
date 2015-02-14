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
		// $pluginsToUnload = array("Widgets_ServerInfo", "Widgets_Clock");

		foreach ($pluginsToUnload as $plugin) {
			if ($this->isPluginLoaded($this->getPluginId($plugin))) {
				$this->pluginhandler->unload($this->getPluginId($plugin));
			}
		}

		if ($this->isPluginLoaded($this->getPluginId("MapRatings"))) {
			\ManiaLive\Event\Dispatcher::register(\ManiaLivePlugins\eXpansion\MapRatings\Events\Event::getClass(), $this);
		}

		$this->enableDedicatedEvents();

		$this->show();
	}

	public function onBeginMatch()
	{
		$this->show();
	}

	public function show($ratings = null)
	{
		Gui\Widgets\TopPanel::EraseAll();
		$widget = Gui\Widgets\TopPanel::Create(null);
		if ($ratings) {
			$widget->setRatings($ratings);
		}
		else {
			$widget->setRatings($this->getRatings());
		}
		$widget->show();
	}

	private function getRatings()
	{
		if ($this->isPluginLoaded($this->getPluginId("MapRatings"))) {
			return $this->callPublicMethod($this->getPluginId("MapRatings"), "getVotesForMap");
		}
		return array();
	}

	private function getPluginId($plugin)
	{
		return '\\ManiaLivePlugins\\eXpansion\\' . $plugin . '\\' . $plugin;
	}

	public function exp_onUnload()
	{
		Gui\Widgets\TopPanel::EraseAll();
	}

	/**
	 *
	 * @param \ManiaLivePlugins\eXpansion\MapRatings\Structures\PlayerVote[] $rating
	 */
	public function onRatingsSave($rating)
	{
		print_r($rating);

		// $this->show($rating);
	}

}
