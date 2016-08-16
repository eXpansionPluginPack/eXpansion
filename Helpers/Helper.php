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

use ManiaLib\Utils\Formatting;
use ManiaLive\Data\Storage as MlStorage;
use ManiaLive\Utilities\Console;
use ManiaLive\Utilities\Logger;
use ManiaLivePlugins\eXpansion\Core\Config;
use ManiaLivePlugins\eXpansion\Helpers\Console as Con;

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
        if (self::$paths == null) {
            self::$paths = new Paths();
        }

        return self::$paths;
    }

    /**
     * Returns instance of singleton instance.
     *
     * @return Singletons
     */
    public static function getSingletons()
    {
        if (self::$singletons == null) {
            self::$singletons = Singletons::getInstance();
        }

        return self::$singletons;
    }

    public static function log($message, $tags = array('eXpansion', 'AdminPanel'))
    {
        $logFile = MlStorage::getInstance()->serverLogin . ".console.log";
        $message = ($tags ? '[' . implode('][', $tags) . '] ' : '') . print_r($message, true);
        Logger::log(Formatting::stripStyles($message), true, $logFile);
        Console::println(Con::outTm($message, true).Con::white);
    }

    public static function logInfo($message, $tags = array('eXpansion'))
    {
        $message = ($tags ? '[' . implode('][', $tags) . '] ' : '') . print_r($message, true);
        Logger::info(Formatting::stripStyles($message));
        Console::println(Con::outTm($message, true).Con::white);
    }

    public static function logError($message, $tags = array('eXpansion'))
    {
        $message = ($tags ? '[' . implode('][', $tags) . '] ' : '') . print_r($message, true);
        Logger::error(Formatting::stripStyles($message));
        Console::println(Con::outTm($message, true).Con::white);
    }

    public static function logDebug($message, $tags = array('eXpansion'))
    {
        /** @var Config $coreConfig */
        $coreConfig = Config::getInstance();

        if ($coreConfig->debug) {
            $message = ($tags ? '[' . implode('][', $tags) . '] ' : '') . print_r($message, true);
            Logger::debug('[DEBUG]' . Formatting::stripStyles($message));
            Console::println('[DEBUG]' . Con::outTm($message, true));
        }
    }

    /**
     * Format a message to print nice.
     *
     * @param $message
     *
     * @return mixed
     */
    public static function formatMessage($message)
    {
        if (is_array($message)) {
            return print_r($message, true);
        } else {
            if (is_object($message)) {
                return var_export($message, true);
            } else {
                return $message;
            }
        }
    }

    public static function getBuildDate()
    {
        if (self::$buildData == null) {
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

            if ($newDate > $maxDate) {
                $maxDate = $newDate;
            }
        }

        return $maxDate;
    }

    public static function formatPastTime($time, $nbDetails)
    {
        $info = array();

        $totalMinutes = ((int)($time / 60));

        //Number of seconds
        $info[] = $time - ($totalMinutes * 60) . ' sec';

        if ($totalMinutes > 0) {
            $totalHours = ((int)($totalMinutes / 60));

            //Number of minutes
            $info[] = $totalMinutes - ($totalHours * 60) . ' min';

            if ($totalHours > 0) {
                $totalDays = ((int)($totalHours / 24));

                //number of hours
                $info[] = $totalHours - ($totalDays * 24) . ' hours';

                if ($totalDays > 0) {

                    $info[] = $totalDays . ' days';
                }
            }
        }

        $start = sizeof($info) - 1;;

        $stop = $start - $nbDetails;
        if ($stop < 0) {
            $stop = 0;
        }
        $stop++;

        $content = '';


        for ($i = $start; $i >= $stop; $i--) {
            $content .= $info[$i] . ' ';
        }

        return $content;
    }
}
