<?php
namespace ManiaLivePlugins\eXpansion\AutoQueue\Gui\Widgets;

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


use ManiaLib\Gui\Elements\Label;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\AutoQueue\AutoQueue;
use ManiaLivePlugins\eXpansion\AutoQueue\Structures\QueuePlayer;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

/**
 * Description of EnterQueueWidget
 *
 * @author Reaby
 */
class QueueList extends Widget
{
    /** @var Frame */
    public $frame;

    /** @var QueuePlayer[] */
    public $queueplayers = array();

    /** @var AutoQueue */
    protected $mainInstance;

    /** @var  WidgetBackGround */
    protected $bg;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Queue List");
        $this->bg = new WidgetBackGround(62, 40);
        $this->bg->setAction($this->createAction(array($this, "enterQueue")));
        $this->addComponent($this->bg);

        $header = new WidgetTitle(62, 40);
        $header->setText(eXpGetMessage("Waiting Queue"));
        $this->addComponent($header);

        $this->frame = new Frame(1, -2);
        $this->addComponent($this->frame);
    }

    protected function eXpOnEndConstruct()
    {
        $this->setPosition(80, -30);
        $this->setSize(62, 40);
    }

    protected function onDraw()
    {


        parent::onDraw();
    }

    public function setPlayers($players, $instance)
    {
        $this->queueplayers = $players;
        $this->mainInstance = $instance;

        $this->frame->clearComponents();
        $x = 1;

        foreach ($this->queueplayers as $player) {
            $label = new Label(30, 6);
            $label->setAlign("left", "center2");
            $label->setPosition(0, -($x * 6));
            $label->setText($x . ".  " . $player->nickName);
            $this->frame->addComponent($label);


            $button = new Button();
            $button->setPosition(32, -($x * 6));
            if ($player->login == $this->getRecipient()) {
                $button->setText(__("Leave", $this->getRecipient()));
                $button->setAction($this->createAction(array($this->mainInstance, "leaveQueue")));
                $this->frame->addComponent($button);
                $this->bg->setAction(null);
            }

            if ($player->login != $this->getRecipient()
                && AdminGroups::hasPermission($this->getRecipient(), Permission::SERVER_ADMIN)
            ) {
                $button->setText(__("Remove", $this->getRecipient()));
                $button->setAction($this->createAction(array($this->mainInstance, "admRemoveQueue"), $player->login));
                $this->frame->addComponent($button);
            }
            $x++;
            if ($x > 8) {
                break;
            }
        }
    }

    public function enterQueue($login)
    {
        $widget = EnterQueueWidget::Create($login);
        $widget->show($login);
    }

    public function destroy()
    {
        parent::destroy();
    }
}
