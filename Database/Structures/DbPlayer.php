<?php
/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
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

namespace ManiaLivePlugins\eXpansion\Database\Structures;

/**
 * Database information about the player
 *
 * @package ManiaLivePlugins\eXpansion\Database\Structures
 */
class DbPlayer
{
    /**
     * @var String
     */
    private $login;

    /**
     * @var int
     */
    private $playerUpdated;

    /**
     * @var int
     */
    private $playerWins;

    /**
     * @var int
     */
    private $lastPlayTime;

    /**
     * @var int
     */
    private $objectCreationTime;

    /**
     * @param $login
     * @param $lastPlayTime
     * @param $playerUpdated
     * @param $playerWins
     */
    function __construct($login, $lastPlayTime, $playerUpdated, $playerWins)
    {
        $this->lastPlayTime = $lastPlayTime;
        $this->login = $login;
        $this->playerUpdated = $playerUpdated;
        $this->playerWins = $playerWins;

        $this->objectCreationTime = time();
    }

    /**
     * @return int
     */
    public function getLastPlayTime()
    {
        return $this->lastPlayTime;
    }

    /**
     * @return int
     */
    public function getPlayTime()
    {
        return (time() - $this->objectCreationTime) + $this->lastPlayTime;
    }

    /**
     * @return String
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return int
     */
    public function getPlayerUpdated()
    {
        return $this->playerUpdated;
    }

    /**
     * @return int
     */
    public function getPlayerWins()
    {
        return $this->playerWins;
    }

    /**
     * @return int
     */
    public function getObjectCreationTime()
    {
        return $this->objectCreationTime;
    }
}
