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

namespace ManiaLivePlugins\eXpansion\Helpers;

use ManiaLive\Utilities\Console;
use ManiaLive\Utilities\Logger;

class Helper
{

	private static $buildData = null;
	private static $singletons;
	private static $paths;

	/**
	 * Returns helper that allows to get paths
	 *
	 * @return Paths
	 */
	public static function getPaths()
	{
		if (self::$paths == null)
			self::$paths = new Paths();
		return self::$paths;
	}

	/**
	 * Returns instance of singleton instance.
	 *
	 * @return Singletons
	 */
	public static function getSingletons()
	{
		if (self::$singletons == null)
			self::$singletons = Singletons::getInstance();
		return self::$singletons;
	}

	public static function log($message)
	{
		Logger::info('[eXpansion][Adm/AdminPanel]' . $message);
		Console::println('[eXpansion][Adm/AdminPanel]' . $message);
	}

	public static function logInfo($message)
	{
		Logger::info('[eXpansion]' . $message);
		Console::println('[eXpansion]' . $message);
	}

	public static function logError($message)
	{
		Logger::error('[eXpansion]' . $message);
		Console::println('[eXpansion]' . $message);
	}

	public static function getBuildDate()
	{
		if (self::$buildData == null) {
			echo "\n" . dirname(__DIR__) . "\n";
			self::$buildData = self::rBuildDate(dirname(__DIR__) . '/');
		}
		return self::$buildData;
	}

	protected static function rBuildDate($base = '')
	{

		$array = array_diff(scandir($base), array('.', '..', '.git'));

		$maxDate = 0;

		foreach ($array as $value) {

			$newDate = 0;
			if (is_dir($base . $value)) {
				$newDate = self::rBuildDate($base . $value . '/');
			} elseif ($base . $value) {
				$newDate = filemtime($base . $value);
			}

			if ($newDate > $maxDate)
				$maxDate = $newDate;
		}

		return $maxDate;
	}

	protected static function formatPastTime($time, $nbDetails)
	{
		$info = array();

		$totalMinutes = ((int)($time/60));

		//Number of seconds
		$info[] = $time - ($totalMinutes*60).' sec';

		if($totalMinutes > 0){
			$totalHours = ((int)($totalMinutes/60));

			//Number of minutes
			$info[] = $totalMinutes - ($totalHours*60).' min';

			if($totalHours > 0){
				$totalDays = ((int)($totalHours/24));

				//number of days
				$info[] = $totalHours - ($totalDays*24).' hours';
			}
		}

		$start = $nbDetails;
		$size = sizeof($info)-1;
		if($start > $size){
			$start = $size;
		}

		$content = '';
		for($i = $start; $i >= 0; $i--){
			$content .= $info[$i].' ';
		}
		return $content;
	}
}