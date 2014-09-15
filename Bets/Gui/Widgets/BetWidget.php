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

namespace ManiaLivePlugins\eXpansion\Bets\Gui\Widgets;

use ManiaLib\Gui\Layouts\Column;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Bets\Bets;
use ManiaLivePlugins\eXpansion\Bets\Config;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

/**
 * Description of BetWidget
 *
 * @author Reaby
 */
class BetWidget extends Widget
{

	public static $action_acceptBet, $action_setAmount;

	public $frame, $labelAccept;

	private $bg, $header;

	private $script;

	protected function exp_onBeginConstruct()
	{
		$sX = 42;
		$this->setName("Bet widget");
		$login = $this->getRecipient();

		$this->bg = new WidgetBackGround($sX, 20);
		$this->addComponent($this->bg);

		$this->header = new WidgetTitle($sX, 4);
		$this->addComponent($this->header);

		$this->frame = new Frame(1, -8);
		$this->frame->setLayout(new Column());
		$this->addComponent($this->frame);

		$this->inputBox = new Inputbox("betAmount", $sX - 10);

		$this->labelAccept = new DicoLabel();
		$this->script = new Script("Bets\\Gui\\Scripts");
		$this->script->setParam("hideFor", "Text[]");
		$this->registerScript($this->script);
	}

	protected function exp_onEndConstruct()
	{
		$this->setScale(0.8);
		$this->setPosition(-40, -60);
		
	}

	public function onResize($oldX, $oldY)
	{
		$this->header->setSize($this->sizeX, 4);
		$this->bg->setSize($this->sizeX, $this->sizeY);
		parent::onResize($oldX, $oldY);
	}

	public function onDraw()
	{
		if (Bets::$state == Bets::state_setBets)
			$this->setBets();
		if (Bets::$state == Bets::state_acceptMoreBets)
			$this->acceptBets();

		parent::onDraw();
	}

	public function acceptBets()
	{
		$this->frame->clearComponents();
		$this->header->setText(exp_getMessage("Accept Bet"));
		$line = new Frame();
		$line->setLayout(new Line());
		$line->setSize(80, 6);

		$this->labelAccept->setText(exp_getMessage('Accept bet for %1$s planets ?'), array("" . Bets::$betAmount));
		$line->addComponent($this->labelAccept);

		$button = new Button();
		$button->setText("Accept");
		$button->setAction(self::$action_acceptBet);

		$line->addComponent($button);
		$this->frame->addComponent($line);
	}

	public function setBets()
	{
		$this->frame->clearComponents();

		$this->header->setText(exp_getMessage("Start Bet"));

		$line = new Frame();
		$line->setLayout(new Line());
		$line->setSize(80, 6);

		$line2 = clone $line;

		$config = Config::getInstance();

		/* foreach ($config->betAmounts as $amount) {
		  $button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
		  $button->setText($amount . "p");
		  $button->setAction();
		  $line->addComponent($button);
		  }
		 */

		$inputbox = new Inputbox("betAmount");
		$line->addComponent($inputbox);

		$button = new Button();
		$button->setText("Accept");
		$button->setAction(self::$action_setAmount);
		$line->addComponent($button);



		$label = new DicoLabel(80, 6);
		$label->setAlign("left", "center2");
		$label->setText(exp_getMessage("Enter amount to bet for winning the map!"));
		$line2->addComponent($label);


		$this->frame->addComponent($line2);
		$this->frame->addComponent($line);
	}

	/**
	 * set logins to maniascritp to hide the widget...
	 * @param string[] $players
	 */
	public function setToHide($players)
	{
		$out = \ManiaLivePlugins\eXpansion\Helpers\Maniascript::stringifyAsList($players);
		if (count($players) == 0) {
			$out = "Text[]";
		}
		$this->script->setParam("hideFor", $out);
	}

}
