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

use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AutoQueue\AutoQueue;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

/**
 * Description of EnterQueueWidget
 *
 * @author Reaby
 */
class EnterQueueWidget extends Widget
{
    protected $dicoLabel;
    protected $button;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Enter Queue");
        $login = $this->getRecipient();

        $bg = new WidgetBackGround(80, 18);
        $this->addComponent($bg);

        $header = new WidgetTitle(81, 4);
        $header->setText(eXpGetMessage("Join Queue"));
        $this->addComponent($header);

        $this->dicoLabel = new DicoLabel(50, 10);
        $this->dicoLabel->setPosition(2, -6);
        $this->dicoLabel->setText(eXpGetMessage("Click the button to \njoin the waiting queue!"));
        $this->dicoLabel->setTextColor("fff");
        $this->addComponent($this->dicoLabel);

        $frame = new Frame(50, -7);
        $frame->setLayout(new Column());

        $this->button = new Button();
        $this->button->setText(__("Join", $login));
        $this->button->colorize("0f0");

        $frame->addComponent($this->button);

        $button = new Button();
        $button->setText(__("Hide", $login));
        $button->setDescription("Click waiting queue to show this window again.");
        $button->setAction($this->createAction(array($this, "hideWidget")));
        $frame->addComponent($button);

        $this->addComponent($frame);
    }

    protected function eXpOnEndConstruct()
    {
        $this->setSize(80, 18);
        $this->setPosition(-30, 60);
    }

    public function onDraw()
    {
        $this->button->setAction(AutoQueue::$enterAction);
        parent::onDraw();
    }

    public function hideWidget($login)
    {
        $this->Erase($login);
    }
}
