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

namespace ManiaLivePlugins\eXpansion\Communication\Gui\Widgets;

use ManiaLib\Gui\Elements\Icons128x128_1;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Script_libraries\Animation;
use ManiaLivePlugins\eXpansion\Gui\Script_libraries\Tray;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

/**
 * Description of CommunicationWidget
 *
 * @author Reaby
 */
class CommunicationWidget extends Widget {

    private $script, $frame, $bg, $_mainWindow, $tabs, $inputbox, $replyTo, $sendAction;
    public static $action;

    protected function exp_onBeginConstruct() {
	$this->setName("Messaging Widget");
	
	$this->_mainWindow = new Frame();
	$this->_mainWindow->setAlign("left", "center");
	$this->_mainWindow->setId("Frame");
	$this->_mainWindow->setScriptEvents(true);
	$this->addComponent($this->_mainWindow);

	$this->bg = new WidgetBackGround(120, 42);
	$this->_mainWindow->addComponent($this->bg);

	$this->icon_title = new \ManiaLib\Gui\Elements\Icons64x64_1(6, 6);
	$this->icon_title->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::NewMessage);
	$this->icon_title->setId("minimizeButton");
	$this->icon_title->setScriptEvents(1);
	$this->_mainWindow->addComponent($this->icon_title);

	$this->tabs = new Frame();
	$this->tabs->setLayout(new \ManiaLib\Gui\Layouts\Line(22,5));
	for ($x = 0; $x < 5; $x++) {
	    $tab = new \ManiaLivePlugins\eXpansion\Communication\Gui\Controls\Tab($x);
	    $this->tabs->addComponent($tab);
	}
	$this->_mainWindow->addComponent($this->tabs);

	$this->frame = new Frame(0,-6);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());


	for ($x = 0; $x < 5; $x++) {
	    $label = new \ManiaLib\Gui\Elements\Label(120,6);
	    //$label->setText("line".$x);
	    $label->setId("line_" . $x);
	    $label->setTextColor("fff");
	    //   $label->setScriptEvents();
	    $this->frame->addComponent($label);
	}
	
	$this->inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("chatEntry", 90);
	$this->inputbox->setId("chatEntry");	
	$this->inputbox->setScriptEvents();
	$this->frame->addComponent($this->inputbox);
	
	$this->_mainWindow->addComponent($this->frame);
	
	// this is used to create a controller logger
	$quad = new \ManiaLib\Gui\Elements\Quad(5,5);
	$quad->setBgcolor("000");
	$quad->setPosition(0,600);
	$quad->setAction(self::$action);
	$this->addComponent($quad);
    
	$reply = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("replyTo", 30);
	$reply->setPosition(0,600);
	$reply->setScriptEvents();
	$this->addComponent($reply);
	
	
	$lib = new Animation();
	$this->registerScript($lib);

	$this->script = new Script("Communication\Gui\Script");
	$this->script->setParam("sendAction", self::$action);
	$this->registerScript($this->script);
    }

    function exp_onEndConstruct() {

	$this->setSize(120, 42);
	$this->setScale(1);
	$this->setPosition(-278, 60);
	$this->setDisableAxis("x");
	$this->script->setParam("winid", $this->getId());
	$this->script->setParam("posY", $this->getPosY());

	$this->icon_title->setPosition($this->getSizeX() - 2, 0);
    }

}
