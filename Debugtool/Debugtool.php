<?php

/*
 * Copyright (C) Error: on line 4, column 33 in Templates/Licenses/license-gpl20.txt
  The string doesn't match the expected date/time format. The string to parse was: "7.2.2014". The expected format was: "dd-MMM-yyyy". Petri
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

namespace ManiaLivePlugins\eXpansion\Debugtool;

/**
 * Description of Debugtool
 *
 * @author Petri
 */
class Debugtool extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onReady() {
	$this->registerChatCommand("connect", "connect", 1, true, \ManiaLive\Features\Admin\AdminGroup::get());
	$this->registerChatCommand("disconnect", "disconnect", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
    }

    function connect($login, $playercount) {
	for ($x = 0; $x < $playercount; $x++) {
	    $this->connection->connectFakePlayer();
	}
    }

    function disconnect($login) {
	try {
	    $this->connection->disconnectFakePlayer("*");
	} catch (\Exception $e) {
	    echo "error disconnecting;";
	}
    }

}
