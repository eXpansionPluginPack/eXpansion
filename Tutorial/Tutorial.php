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

namespace ManiaLivePlugins\eXpansion\Tutorial;

class Tutorial extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onReady() {
	foreach ($this->storage->players as $login => $player)
	    $this->onPlayerConnect($login, false);
	foreach ($this->storage->spectators as $login => $player)
	    $this->onPlayerConnect($login, true);
	$this->enableDedicatedEvents();
	
    }

    public function onPlayerConnect($login, $isSpectator) {	
	$window = Gui\Windows\TutorialWindow::Create($login);
	$window->setSize(160, 80);
	$window->show();
    }

}
