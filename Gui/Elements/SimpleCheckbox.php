<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLive\Gui\ActionHandler;

class SimpleCheckbox extends \ManiaLive\Gui\Control {

    private $button;
    private $active = false;
    private $action;

    function __construct($sizeX = 4, $sizeY = 4) {
        $this->action = $this->createAction(array($this, 'toggleActive'));
        $config = Config::getInstance();
        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('left', 'center2');
        $this->button->setImage($config->checkbox);
        $this->button->setAction($this->action);
        $this->button->setScriptEvents(true);
        $this->addComponent($this->button); 
        
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->button->setSize($this->sizeX, $this->sizeY);
        $this->button->setPosition(0, -0.5);
    }

    function onDraw() {
        $config = Config::getInstance();

        if ($this->active) {
            $this->button->setImage($config->checkboxActive);
        } else {
           $this->button->setImage($config->checkbox);
        }
    }

    function setStatus($boolean) {
        $this->active = $boolean;
    }

    function getStatus() {
        return $this->active;
    }

    function toggleActive($login) {
        $this->active = !$this->active;
        $this->redraw();
    }

    function setAction($action) {
        $this->button->setAction($action);
    }
}

?><?php

/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

/**
 * Description of SimpleCheckbox
 *
 * @author De Cramer Oliver
 */
class SimpleCheckbox {
    //put your code here
}

?>
