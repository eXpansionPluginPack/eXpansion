<?php

/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace ManiaLivePlugins\eXpansion\AutoLoad\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad;
use ManiaLivePlugins\eXpansion\Core\types\config\MetaData;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

class Plugin extends \ManiaLive\Gui\Control
{

	/**
	 * @var AutoLoad
	 */
	private $autoLoad;

	/**
	 * @var MetaData
	 */
	private $metaData;

	/**
	 * @var Button
	 */
	private $button_running, $button_titleComp, $button_gameComp, $button_otherComp, $button_more, $button_start;

	/**
	 * @var \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround
	 */
	private $bg;

	/**
	 * @var Label
	 */
	private $label_name, $label_author;

	/**
	 * @var Quad
	 */
	private $icon_name, $icon_author;

	function __construct($indexNumber, AutoLoad $autoload, MetaData $plugin, $login, $isLoaded)
	{

		$this->metaData = $plugin;
		$this->autoLoad = $autoload;

		$this->bg = new ListBackGround($indexNumber, 100, 4);
		$this->addComponent($this->bg);

		$titleCompatible = $plugin->checkTitleCompatibility();
		$gameCompatible = $plugin->checkGameCompatibility();
		$otherCompatible = $plugin->checkOtherCompatibility();
		$isInStart = $autoload->isInStartList($plugin->getPlugin());

		$this->button_running = new Button(8, 8);
		$this->button_running->setDescription(__($this->getRunningDescriptionText($isLoaded, $isInStart), $login), 120);
		if ($isLoaded)
			$this->button_running->setIcon("Icons64x64_1", "LvlGreen");
		else if ($isInStart)
			$this->button_running->setIcon("Icons64x64_1", "LvlYellow");
		else
			$this->button_running->setIcon("Icons64x64_1", "LvlRed");
		$this->addComponent($this->button_running);

		$this->icon_name = new Quad();
		$this->icon_name->setSize(4, 4);
		$this->icon_name->setStyle('Icons128x128_1');
		$this->icon_name->setSubStyle('CustomStars');
		$this->icon_name->setPosition(6, 4);
		$this->addComponent($this->icon_name);

		$this->label_name = new Label(40, 4);
		$this->label_name->setScale(0.8);
		$this->label_name->setText($plugin->getName() == "" ? $plugin->getPlugin() : $plugin->getName());
		$this->label_name->setPosition(11, 3);
		$this->addComponent($this->label_name);

		$this->icon_author = new Quad();
		$this->icon_author->setSize(4, 4);
		$this->icon_author->setStyle('Icons64x64_1');
		$this->icon_author->setSubStyle('IconPlayers');
		$this->icon_author->setPosition(6, 0);
		$this->addComponent($this->icon_author);

		$this->label_author = new Label(40, 4);
		$this->label_author->setScale(0.6);
		$this->label_author->setText($plugin->getAuthor());
		$this->label_author->setPosition(11, -1);
		$this->addComponent($this->label_author);

		$this->button_titleComp = new Button(7, 7);
		$this->button_titleComp->setDescription(__($this->getTitleDescriptionText($titleCompatible), $login), 100);
		if ($titleCompatible)
			$this->button_titleComp->setIcon("Icons64x64_1", "LvlGreen");
		else
			$this->button_titleComp->setIcon("Icons64x64_1", "LvlRed");
		$this->addComponent($this->button_titleComp);

		$this->button_gameComp = new Button(7, 7);
		$this->button_gameComp->setDescription(__($this->getGameDescriptionText($gameCompatible), $login), 100);
		if ($gameCompatible)
			$this->button_gameComp->setIcon("Icons64x64_1", "LvlGreen");
		else
			$this->button_gameComp->setIcon("Icons64x64_1", "LvlRed");
		$this->addComponent($this->button_gameComp);

		$this->button_otherComp = new Button(7, 7);
		$this->button_otherComp->setDescription(__($this->getOtherDescriptionText($otherCompatible), $login), 100, 5, sizeof($otherCompatible) + 1);
		if (empty($otherCompatible))
			$this->button_otherComp->setIcon("Icons64x64_1", "LvlGreen");
		else
			$this->button_otherComp->setIcon("Icons64x64_1", "LvlRed");
		$this->addComponent($this->button_otherComp);

		$this->button_more = new Button(8, 8);
		$this->button_more->setIcon("Icons128x128_1", "Options");
		//$this->addComponent($this->button_more);

		$this->button_start = new Button(12, 5);
		$this->button_start->setAction($this->createAction(array($this, "startPlugin")));
		$this->button_start->setText(__($this->getStartText($isLoaded, $isInStart), $login));

		if ($this->getStartText($isLoaded, $isInStart) == "Start") {
			$this->button_start->colorize("0D0");
		} else {
			$this->button_start->colorize("F00");
		}
		$this->addComponent($this->button_start);

		$this->setSize(117, 8);
	}

	protected function onResize($oldX, $oldY)
	{
		$this->label_name->setSizeX(($this->getSizeX() - $this->label_name->getPosX() - 6 * 4 - 5) / $this->label_name->getScale());
		$this->label_author->setSizeX(($this->getSizeX() - $this->label_author->getPosX() - 6 * 4 - 5) / $this->label_author->getScale());

		$this->bg->setSize($this->getSizeX() + 3, $this->getSizeY());

		$this->button_titleComp->setPositionX($this->getSizeX() - 5 * 5 - 5);
		$this->button_gameComp->setPositionX($this->getSizeX() - 5 * 4 - 4);
		$this->button_otherComp->setPositionX($this->getSizeX() - 5 * 3 - 3);
		$this->button_more->setPositionX($this->getSizeX() - 5 * 2 - 4);
		$this->button_start->setPositionX($this->getSizeX() - 8 * 1 - 1);
	}

	private function getRunningDescriptionText($runnig, $inStart)
	{
		if ($runnig)
			return "This plugin is running on the server at the moment";
		else if ($inStart)
			return "This plugin isn't compatible with game mode or title. It is therefor pending";
		else
			return "This plugin isn't running";
	}

	private function getTitleDescriptionText($titleCompatible)
	{
		if ($titleCompatible)
			return "This plugin is compatible with the current Title";
		else
			return "This plugin isn't compatible with the current Title";
	}

	private function getGameDescriptionText($gameCompatible)
	{
		if ($gameCompatible)
			return "This plugin is compatible with the current Game mode";
		else
			return "This plugin isn't compatible with the current Game mode";
	}

	private function getOtherDescriptionText($otherCompatibility)
	{
		if (empty($otherCompatibility))
			return "This plugin is is compatible with current installation";
		else
			return "This plugin has a few compatibility issues : \n" . implode("\n", $otherCompatibility);
	}

	private function getStartText($started, $instart)
	{
		if ($instart || $started) {
			return "Stop";
		} else {
			return "Start";
		}
	}

	public function startPlugin($login)
	{
		$this->autoLoad->tooglePlugin($login, $this->metaData);
	}

}
