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

namespace ManiaLivePlugins\eXpansion\Bets\Classes;

/**
 * Description of betCounter
 *
 * @author Reaby
 */
class BetCounter
{

    private $endTimestamp = -1;

    private $callback = null;

    private $param = null;

    private $active = false;

    public function __construct($timeout, $callback = null, $param = null)
    {
        if (!is_numeric($timeout)) {
            return;
        }

        $this->callback = $callback;
        $this->endTimestamp = time() + intval($timeout);
        $this->active = true;
    }

    public function check()
    {
        if ($this->active == false) {
            return false;
        }


        if ($this->endTimestamp < time()) {
            $this->active = false;

            try {
                if ($this->callback) {
                    call_user_func($this->callback, $this->param);
                }
            } catch (\Exception $e) {
                \ManiaLivePlugins\eXpansion\Helpers\Helper::logError("invalid callback found at betcounter.php");

                return true;
            }

            return true;
        }

        return false;
    }
}
