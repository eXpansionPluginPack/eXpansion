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

namespace ManiaLivePlugins\eXpansion\MXKarma\Structures;

/**
 * Description of MXRating
 *
 * votecount number - Amount of votes made so far.
 * voteaverage number - Average vote. If no votes are made, this value equals 50. Not per see integeral.
 * modevotecount number - Amount of votes made so far in the current mode/title group. If getvotesonly, this value
 * equals -1. modevoteaverage number - Average vote in the current mode/title group. If no votes are made, this value
 * equals 50. If getvotesonly, this value equals -1. Not per see integeral. votes array - Array of objects containing
 * votes of the requested logins. login string - Player login. vote number - Player vote (integer ranging from 0 to
 * 100). Player logins provided who have not voted are not included here.
 *
 * @author Reaby
 */
class MXRating extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    /** @var int */
    public $votecount;

    /** @var int */
    public $voteaverage;

    /** @var int */
    public $modevotecount;

    /** @var int */
    public $modevoteaverage;

    /** @var string[login, number] */
    public $votes = array();

    public function append($object)
    {
        if (!is_object($object))
            throw new \Exception("MXVote consturctor got non object", 1, null);
        $this->votecount = $object->votecount;
        $this->voteaverage = $object->voteaverage;
        if ($object->modevotecount != -1) {
            $this->modevotecount = $object->modevotecount;
        }
        if ($object->modevoteaverage != -1) {
            $this->modevoteaverage = $object->modevoteaverage;
        }
        if (is_array($object->votes)) {
            foreach ($object->votes as $vote) {
                $this->votes[] = $vote;
            }
        }
    }

}
