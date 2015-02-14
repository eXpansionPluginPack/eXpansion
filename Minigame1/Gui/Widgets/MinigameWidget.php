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

namespace ManiaLivePlugins\eXpansion\Minigame1\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Minigame1\Config;

/**
 * Description of MinigameWidget
 *
 * @author Reaby
 */
class MinigameWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

	public static $action = -1;

	private $quad;

	/** @var Config */
	private $config;

	private $script;

	protected function onConstruct()
	{
		parent::onConstruct();
		
		$this->config = Config::getInstance();
		$this->setScriptEvents();
		
		$size = $this->config->mg1_imageSize;

		$this->quad = new \ManiaLib\Gui\Elements\Quad();
		$this->quad->setAlign("center", "center");

		$x = rand(-160 + ($size / 2), 160 - ($size / 2));
		$y = rand(-90 + ($size / 2), 90 - ($size / 2));

		$this->quad->setPosition($x, $y);
		$this->setPosZ(50);
		$this->quad->setSize();
		$this->quad->setImage($this->config->mg1_imageUrl, true);
		$this->quad->setImageFocus($this->config->mg1_imageFocusUrl, true);
		$this->quad->setId("quad");
		$this->quad->setScriptEvents();
		$this->quad->setAttribute('hidden', '1');
		$this->addComponent($this->quad);

		$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Minigame1/Gui/Script");
		$this->script->setParam("action", self::$action);
		$this->registerScript($this->script);
	}

	public function setDisplayDuration($duration)
	{
		$this->script->setParam("duration", $duration);
	}

}
