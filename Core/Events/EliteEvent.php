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

namespace ManiaLivePlugins\eXpansion\Core\Events;

/**
 * Dispatches the elite events to all listeners listening
 *
 * @package ManiaLivePlugins\eXpansion\Core\Events
 */
class EliteEvent extends \ManiaLive\Event\Event
{

	const ON_BEGIN_MATCH = 0x1;
	const ON_BEGIN_MAP = 0x2;
	const ON_BEGIN_WARMUP = 0x3;
	const ON_END_WARMUP = 0x4;
	const ON_BEGIN_TURN = 0x5;
	const ON_SHOOT = 0x6;
	const ON_HIT = 0x7;
	const ON_CAPTURE = 0x8;
	const ON_ARMORY_EMPTY = 0x9;
	const ON_NEAR_MISSS = 0x10;
	const ON_END_TURN = 0x11;
	const ON_END_MATCH = 0x12;
	const ON_END_MAP = 0x13;
	const ON_SCORES = 0x14;


	protected $params;

	function __construct($method, $params = array())
	{
		parent::__construct(self::getOnWhat($method));
		$this->params = $params;
	}

	function fireDo($listener)
	{
		$p = $this->params;
		// Explicit calls are always *a lot* faster than using call_user_func() even if longer to write
		switch ($this->onWhat) {
			case self::ON_BEGIN_MATCH:
				$listener->elite_onBeginMatch($p[0]);
				break;
			case self::ON_BEGIN_MAP:
				$listener->elite_onBeginMap($p[0]);
				break;
			case self::ON_BEGIN_WARMUP:
				$listener->elite_onBeginWarmup($p[0]);
				break;
			case self::ON_END_WARMUP:
				$listener->elite_onEndWarmup($p[0]);
				break;
			case self::ON_BEGIN_TURN:
				$listener->elite_onBeginTurn($p[0]);
				break;
			case self::ON_SHOOT:
				$listener->elite_onShoot($p[0]);
				break;
			case self::ON_HIT:
				$listener->elite_onHit($p[0]);
				break;
			case self::ON_CAPTURE:
				$listener->elite_onCapture($p[0]);
				break;
			case self::ON_ARMORY_EMPTY:
				$listener->elite_onArmoryEmpty($p[0]);
				break;
			case self::ON_NEAR_MISSS:
				$listener->elite_onNearMiss($p[0]);
				break;
			case self::ON_END_TURN:
				$listener->elite_onEndTurn($p[0]);
				break;
			case self::ON_END_MATCH:
				$listener->elite_onEndMatch($p[0]);
				break;
			case self::ON_END_MAP:
				$listener->elite_onEndMap($p[0]);
				break;
			case self::ON_SCORES:
				$listener->elite_onScores($p[0]);
				break;
		}
	}
} 