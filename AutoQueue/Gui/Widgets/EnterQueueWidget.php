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

namespace ManiaLivePlugins\eXpansion\AutoQueue\Gui\Widgets;

/**
 * Description of EnterQueueWidget
 *
 * @author Reaby
 */
class EnterQueueWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

	public static $action_toggleQueue;

	public $dicoLabel;

	protected function exp_onBeginConstruct()
	{
		$this->setName("Enter Queue");
		$login = $this->getRecipient();

		$bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(80, 18);
		$this->addComponent($bg);

		$header = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle(81, 4);
		$header->setText(exp_getMessage("Join Queue"));
		$this->addComponent($header);

		$this->dicoLabel = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(50, 10);
		$this->dicoLabel->setPosition(2, -6);
		$this->dicoLabel->setText(exp_getMessage("Click the button to \njoin the waiting queue!"));
		$this->dicoLabel->setTextColor("fff");
		$this->addComponent($this->dicoLabel);

		$frame = new \ManiaLive\Gui\Controls\Frame(50, -7);
		$frame->setLayout(new \ManiaLib\Gui\Layouts\Column());

		$button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
		$button->setText(__("Join", $login));
		$button->setAction(self::$action_toggleQueue);
		$button->colorize("0f0");
		$frame->addComponent($button);

		$button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
		$button->setText(__("Hide", $login));
		$button->setDescription("Click waiting queue to show this window again.");
		$button->setAction($this->createAction(array($this, "hideWidget")));
		$frame->addComponent($button);

		$this->addComponent($frame);
	}

	protected function exp_onEndConstruct()
	{
		$this->setSize(80, 18);
		$this->setPosition(-30, 60);
	}

	public function hideWidget($login)
	{
		$this->Erase($login);
	}

}
