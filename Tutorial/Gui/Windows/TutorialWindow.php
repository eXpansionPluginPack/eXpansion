<?php

/*
 * Copyright (C) 2014 eXpansion Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace ManiaLivePlugins\eXpansion\Tutorial\Gui\Windows;

use ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Header;
use ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Line;

/**
 * Description of TutorialWindow
 *
 * @author Petri
 */
class TutorialWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $button, $frame;
    private $mScript;

    protected function onConstruct() {
	parent::onConstruct();
	$this->setTitle("eXpansion Plugin Pack - Tutorial");

	$help = new \ManiaLib\Gui\Elements\Quad(64, 64);
	$help->setAlign("left", "top");
	$help->setImage("http://reaby.kapsi.fi/ml/help.png", true);
	$this->quad_help = $help;
	$this->addComponent($this->quad_help);

	$frame = new \ManiaLive\Gui\Controls\Frame(66, -6);
	$frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
	$this->frame = $frame;
	$this->addComponent($this->frame);

	$lines = array();
	$line = new Line("If you see gray box on left, please press Backspace.");
	$line->setBlock(2);

	$lines[] = $line;
	$lines[] = new Line("");
	$lines[] = new Header("Quick Start");

	$line = new Line("Windows can be moved with drag'n'drop from title bar.");
	$line->setBlock(1);
	$lines[] = $line;

	$line = new Line("90% of chat commands you already are familiar works here too!");
	$line->setBlock(1);
	$lines[] = $line;
	
	$lines[] = new Line("");
	$lines[] = new Header("Customize HUD");

	$line = new Line("Menu -> Hud -> Move Postitions");
	$line->setBlock(1);	
	$lines[] = $line;
	
	$line = new Line("all widgets what blinks can now be moved, after you are done use");
	$line->setBlock(1);
	$lines[] = $line;
	
	$line = new Line("Menu -> Hud -> Lock Positions");
	$line->setBlock(1);
	$lines[] = $line;
	
	foreach ($lines as $line) {
	    $this->frame->addComponent($line);
	}
	
	$button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(45,6);
	$button->setText("Close - don't show again");
	$button->setId("CloseNotAgain");
	$button->setScriptEvents();
	$this->button = $button;
	//$this->button->setAction($this->createAction(array($this, "close")));
	$this->addComponent($this->button);

	$this->mScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Tutorial\Gui\Scripts");	
	$this->registerScript($this->mScript);
    }

    public function close($login) {
	$this->Erase($login);	
    }
    
    public function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->button->setPosition($this->sizeX - 40, -$this->sizeY + 6);
		
    }

}
