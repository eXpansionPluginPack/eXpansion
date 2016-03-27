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
class BetWidget extends Widget
{
    public static $action_acceptBet;
    public static $action_setAmount;
    public static $action_setAmount25;
    public static $action_setAmount50;
    public static $action_setAmount100;
    public static $action_setAmount250;
    public static $action_setAmount500;
    protected $frame, $labelAccept;
    protected $bg, $header, $closeButton, $buttonAccept;
    protected $script;

    protected function exp_onBeginConstruct()
    {
        $sX    = 42;
        $this->setName("Bet widget");
        $login = $this->getRecipient();

        $this->bg = new WidgetBackGround($sX, 20);
        $this->addComponent($this->bg);

        $this->header = new WidgetTitle($sX, 4);
        $this->addComponent($this->header);

        $this->frame = new Frame(1, -8);
       // $this->frame->setLayout(new Column());
        $this->addComponent($this->frame);

        $this->script = new Script("Bets/Gui/Scripts");
        $this->script->setParam("hideFor", "Text[]");
        $this->registerScript($this->script);

        $this->closeButton = new Button();
        $this->closeButton->setText("Close");
        $this->closeButton->setId("closeButton");
        $this->closeButton->setScriptEvents();
        $this->addComponent($this->closeButton);
    }

    protected function exp_onEndConstruct()
    {
        $this->setPosition(20, -65);
    }

    public function onResize($oldX, $oldY)
    {
        $this->header->setSize($this->sizeX, 4);
        $this->bg->setSize($this->sizeX, $this->sizeY);
        parent::onResize($oldX, $oldY);
    }

    public function onDraw()
    {
        if (Bets::$state == Bets::state_setBets) $this->setBets();
        if (Bets::$state == Bets::state_acceptMoreBets) $this->acceptBets();
        $this->closeButton->setPosition($this->sizeX - 28, -$this->sizeY + 5);
        parent::onDraw();
    }

    public function acceptBets()
    {
        $this->frame->clearComponents();
        $this->header->setText(exp_getMessage("Accept Bet"));
        $line = new Frame();
        $line->setLayout(new Flow());
        $line->setSize(80, 12);

        $this->labelAccept = new DicoLabel(50);
        $this->labelAccept->setPosition(5, -2);
        $this->labelAccept->setText(exp_getMessage('Accept bet for %1$s planets ?'), array("".Bets::$betAmount));
        $line->addComponent($this->labelAccept);

        $button = new Button();
        $button->setText("Accept");
        $button->colorize('$0f0');
        $button->setPosition($this->sizeX - 28, -$this->sizeY + 12);
        $button->setAction(self::$action_acceptBet);

        $this->addComponent($button);
        $this->frame->addComponent($line);
    }

    public function setBets()
    {
        $this->frame->clearComponents();

        $this->script->setParam("action", self::$action_setAmount);

        $this->header->setText(exp_getMessage("Start Bet"));

        $line = new Frame();
        $line->setLayout(new Flow());
        $line->setSize(60, 6);

        $config = Config::getInstance();

        foreach (array(25, 50, 100, 250, 500) as $value) {

            $button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 6);
            $var    = "action_setAmount".$value;
            $button->setAction(self::$$var);
            $button->setText($value);
            $button->colorize("3af");
            $line->addComponent($button);
        }

        $inputbox = new Inputbox("betAmount", 18);
        $line->addComponent($inputbox);

        $button             = new Button();
        $button->setText("Accept");
        $button->colorize("0d0");
        $button->setAction(self::$action_setAmount);
        $button->setPosition($this->sizeX - 28, -$this->sizeY + 12);
        $this->addComponent($button);
        
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