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
class QueueList extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

	public static $action_toggleQueue;

	public $frame;

	/** @var \ManiaLivePlugins\eXpansion\AutoQueue\Structures\QueuePlayer[] */
	public $queueplayers = array();

	protected function exp_onEndConstruct()
	{
		$this->setName("Queue List");
		$login = $this->getRecipient();

		$bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(60, 30);
		$this->addComponent($bg);

		$this->frame = new \ManiaLive\Gui\Controls\Frame();
		$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
		$this->addComponent($this->frame);
	}

	protected function onDraw()
	{

		$this->frame->clearComponents();
		$x = 1;
		foreach ($this->queueplayers as $player) {
			$label = new \ManiaLib\Gui\Elements\Label(30, 4);
			$label->setText($x . "." . $player->nickName);
			$this->frame->addComponent($label);
			$x++;
		}
		parent::onDraw();
	}
	
	public function setPlayers($players) {
		$this->queueplayers = $players;
	}

}
