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


use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\BeginMap;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\BeginMatch;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\BeginTurn;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\BeginWarmup;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\EndMap;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\EndMatch;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\EndTurn;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\EndWarmup;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\OnArmorEmpty;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\OnCapture;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\OnHit;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\OnNearMiss;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\OnShoot;
use ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks\Score;

/**
 * Events sent on the shootmania elite game mode only.
 *
 * @package ManiaLivePlugins\eXpansion\Core\Events
 */
interface EliteEventListener
{
	/**
	 * @param BeginMatch $beginMatch
	 *
	 * @return void
	 */
	public function elite_onBeginMatch(BeginMatch $beginMatch);

	/**
	 * @param BeginMap $beginMap
	 *
	 * @return void
	 */
	public function elite_onBeginMap(BeginMap $beginMap);

	/**
	 * @param BeginWarmup $beginWarmup
	 *
	 * @return void
	 */
	public function elite_onBeginWarmup(BeginWarmup $beginWarmup);

	/**
	 * @param EndWarmup $endWarmup
	 *
	 * @return void
	 */
	public function elite_onEndWarmup(EndWarmup $endWarmup);

	/**
	 * @param BeginTurn $beginTurn
	 *
	 * @return void
	 */
	public function elite_onBeginTurn(BeginTurn $beginTurn);

	/**
	 * @param OnShoot $onShoot
	 *
	 * @return void
	 */
	public function elite_onShoot(OnShoot $onShoot);

	/**
	 * @param OnHit $onHit
	 *
	 * @return void
	 */
	public function elite_onHit(OnHit$onHit);

	/**
	 * @param OnCapture $onCapture
	 *
	 * @return void
	 */
	public function elite_onCapture(OnCapture $onCapture);

	/**
	 * @param OnArmorEmpty $onArmorEmpty
	 *
	 * @return void
	 */
	public function elite_onArmoryEmpty(OnArmorEmpty $onArmorEmpty);

	/**
	 * @param OnNearMiss $onNearMiss
	 *
	 * @return void
	 */
	public function elite_onNearMiss(OnNearMiss $onNearMiss);

	/**
	 * @param EndTurn $endTurn
	 *
	 * @return void
	 */
	public function elite_onEndTurn(EndTurn $endTurn);

	/**
	 * @param EndMatch $endMatch
	 *
	 * @return void
	 */
	public function elite_onEndMatch(EndMatch $endMatch);

	/**
	 * @param EndMap $endMap
	 *
	 * @return void
	 */
	public function elite_onEndMap(EndMap $endMap);

	/**
	 * @param Score $score
	 *
	 * @return void
	 */
	public function elite_onScores(Score $score);
} 