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

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Widget;

/**
 * Description of QuizImageWidget
 *
 * @author Reaby
 */
class QuizImageWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

	private $quad, $title, $bg, $script;

	protected function exp_onBeginConstruct()
	{
		$this->setName("Quiz Widget");

		$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(20, 22);
		$this->addComponent($this->bg);

		$this->title = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle(20, 4);
		$this->title->setText(exp_getMessage("Question"));
		$this->addComponent($this->title);

		$this->quad = new \ManiaLib\Gui\Elements\Quad(16, 16);
		$this->quad->setId("image");
		$this->quad->setScriptEvents();
		$this->quad->setPosition(2, -4);
		$this->addComponent($this->quad);
		
		$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Quiz/Gui/Scripts");
		$this->registerScript($this->script);
		
		
	}

	protected function exp_onEndConstruct()
	{
		$this->setSize(20, 22);
		$this->setPosition(-152, 80);
	}

	public function setImage($url)
	{
		$this->quad->setImage($url, true);
	}

}
