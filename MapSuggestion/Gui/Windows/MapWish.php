<?php

/*
 * Copyright (C)
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

namespace ManiaLivePlugins\eXpansion\MapSuggestion\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Column;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\TextEdit;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\MapSuggestion\MapSuggestion;

/**
 * Description of MapWish
 *
 * @author Petri
 */
class MapWish extends Window
{

    protected $frame;

    /** @var string $mxid */
    protected $mxid = "";
    /** @var  Inputbox */
    public $inputbox_mxid;
    /** @var  TextEdit */
    protected $inputbox_description;
    protected $button_send;
    protected $button_cancel;
    protected $fromText = "";

    /**
     * @var MapSuggestion
     */
    protected $plugin;

    protected function onConstruct()
    {
        parent::onConstruct();

        $login = $this->getRecipient();
        $player = Storage::getInstance()->getPlayerObject($login);
        $this->fromText = $player->nickName . '$z$s$fff (' . $login . ')';
        $this->setName("MapSuggestion window");
        $this->setTitle(__("Wish a map", $login));
        $this->setSize(90, 60);

        $this->frame = new Frame(2, -6);
        $this->frame->setLayout(new Column(90, 60));
        $this->addComponent($this->frame);


        $lbl_login = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("from", 60, false);
        $lbl_login->setLabel(__('From', $login));
        $lbl_login->setText($this->fromText);
        $this->frame->addComponent($lbl_login);


        $this->inputbox_mxid = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("mxid", 60);
        $this->inputbox_mxid->setLabel(__("Mania-Exchange ID-number for map wish", $login));
        $this->inputbox_mxid->setText($this->mxid);
        $this->frame->addComponent($this->inputbox_mxid);

        $lbl = new Label(60, 4);
        $lbl->setText(__("Why you would like this map to be added ?", $login));
        $this->frame->addComponent($lbl);

        $input = new Inputbox("description");
        $input->setPosition(900, 900);
        $input->setId("descriptionTo");
        $this->addComponent($input);
        $this->registerScript(new Script("MapSuggestion\\Gui\\Scripts"));

        $this->inputbox_description = new \ManiaLivePlugins\eXpansion\Gui\Elements\TextEdit("description_", 80, 24);
        $this->inputbox_description->setId("descriptionFrom");
        $this->frame->addComponent($this->inputbox_description);

        // frame with line layout, used for row template;
        $row = new Frame(0, -3, new Line());
        $row->setSize(60, 10);

        $this->button_send = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->button_send->colorize("0d0");
        $this->button_send->setAction($this->createAction(array($this, "apply")));
        $this->button_send->setText(__("Send Suggestion", $login));
        $row->addComponent($this->button_send);

        $this->button_cancel = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->button_cancel->setAction($this->createAction(array($this, "openMxWindow")));
        $this->button_cancel->setText(__("Search for Map", $login));
        $row->addComponent($this->button_cancel);

        $this->frame->addComponent($row);
    }

    /**
     * @param MapSuggestion $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }

    public function apply($login, $entries)
    {
        $mxid = $entries['mxid'];
        $this->plugin->addMapToWish($login, $mxid, $entries['description']);
    }

    public function openMxWindow($login)
    {
        $this->plugin->openMxWindow($login);
    }

    public function setMXid($mxid)
    {
        if (is_numeric($mxid)) {
            $this->mxid = "" . $mxid;
            $this->inputbox_mxid->setText($this->mxid);
        }
    }
}
