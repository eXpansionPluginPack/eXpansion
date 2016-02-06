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

namespace ManiaLivePlugins\eXpansion\Notifications\types;

use ManiaLivePlugins\eXpansion\Notifications\Gui\Windows\ConfPluginList;

/**
 * Description of HashListToggable
 *
 * @author Reaby
 */
class NotificationPluginlist extends \ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList
{

	public function __construct($name, $visibleName = "", $configInstance = null, $scope = false, $showMain = false)
	{
		parent::__construct($name, $visibleName, $configInstance, $scope, $showMain);
		$this->setType(new \ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString(""));
	}

	public function showConfWindow($login)
	{
		ConfPluginList::Erase($login);
		$win = ConfPluginList::Create($login);
		$win->setTitle("Config selection");
		$win->centerOnScreen();
		$win->setSize(100, 100);
		$win->populate($this);
		$win->show();
	}

	public function hideConfWindow($login)
	{
		ConfPluginList::Erase($login);
	}

	public function hasConfWindow()
	{
		return true;
	}

}
