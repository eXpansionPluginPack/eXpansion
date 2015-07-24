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
use ManiaLib\Gui\Layouts\Flow;
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
class BetWidget extends Widget {

    public static $action_acceptBet, $action_setAmount;
    protected $frame, $labelAccept;
    protected $bg, $header, $closeButton, $buttonAccept;
    protected $script;

    protected function exp_onBeginConstruct() {
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

	$this->labelAccept = new DicoLabel();
	$this->script = new Script("Bets/Gui/Scripts");
	$this->script->setParam("hideFor", "Text[]");	
	$this->registerScript($this->script);
    }

    protected function exp_onEndConstruct() {	
	$this->setPosition(-40, 60);
    }

    public function onResize($oldX, $oldY) {
	$this->header->setSize($this->sizeX, 4);
	$this->bg->setSize($this->sizeX, $this->sizeY);
	parent::onResize($oldX, $oldY);
    }

    public function onDraw() {
	if (Bets::$state == Bets::state_setBets)
	    $this->setBets();
	if (Bets::$state == Bets::state_acceptMoreBets)
	    $this->acceptBets();

	parent::onDraw();
    }

    public function acceptBets() {
	$this->frame->clearComponents();
	$this->header->setText(exp_getMessage("Accept Bet"));
	$line = new Frame();
	$line->setLayout(new Flow());
	$line->setSize(80, 12);

	$this->labelAccept->setText(exp_getMessage('Accept bet for %1$s planets ?'), array("" . Bets::$betAmount));
	$line->addComponent($this->labelAccept);

	$button = new Button();
	$button->setText("Accept");
	$button->setAction(self::$action_acceptBet);

	$line->addComponent($button);
	$this->frame->addComponent($line);
    }

    public function setBets() {
	$this->frame->clearComponents();
	
	$this->script->setParam("action", self::$action_setAmount);
	
	$this->header->setText(exp_getMessage("Start Bet"));

	$line = new Frame();
	$line->setLayout(new Flow());
	$line->setSize(80, 6);

	$line2 = clone $line;

	$config = Config::getInstance();

	/*foreach ($config->betAmounts as $amount) {

	    $button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
	    $button->setAttribute('data-amount', $amount);
	    $button->setAttribute('class', 'exp_button bet');
	    $button->setScale(0.6);
	    $button->setText($amount);

	    $quad = new \ManiaLib\Gui\Elements\Quad(4,4);
	    $quad->setStyle("ManiaPlanetLogos");
	    $quad->setSubStyle('IconPlanets');
	    $quad->setAlign("left", "center");
	    $quad->setPosX(-17);
	    $line2->addComponent($button);
	    $line2->addComponent($quad);	    
	}
	*/
	
	$label = new DicoLabel(22, 6);
	$label->setAlign("left", "center2");
	$label->setText(exp_getMessage("Custom amount"));
	$line->addComponent($label);

	$inputbox = new Inputbox("betAmount", 12);
	$line->addComponent($inputbox);

	$button = new Button();
	$button->setText("Accept");
	$button->colorize("0d0");
	$button->setAction(self::$action_setAmount);
	$this->buttonAccept = $button;
    	$line->addComponent($this->buttonAccept);

	$button = new Button();
	$button->setPosition($this->sizeX-28, -$this->sizeY+3);
	$button->setText("Close");
	$button->setAction($this->createAction(array($this, 'close')));
	$this->closeButton = $button;
	$this->addComponent($this->closeButton);

	$this->frame->addComponent($line);
	$this->frame->addComponent($line2);
	
    }

    /**
     * set logins to maniascritp to hide the widget...
     * @param string[] $players
     */
    public function setToHide($players) {
	$out = \ManiaLivePlugins\eXpansion\Helpers\Maniascript::stringifyAsList($players);
	if (count($players) == 0) {
	    $out = "Text[]";
	}
	$this->script->setParam("hideFor", $out);
    }

    public function close() {
	$this->closeWindow();
    }
}
