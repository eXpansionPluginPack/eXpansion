<?php

namespace ManiaLivePlugins\eXpansion\Helpers;
use ManiaLive\Utilities\Console;
/**
 * Description of Timer
 *
 * @author Petri
 */
class Timer {

    public static $time;

    static function set() {
        self::$time = -microtime(true);
        Console::println("Profiler timer started.");
    }

    static function get() {
        if (empty(self::$time)) {
            self::set();
        } else {
            Console::println("Profiler ended: " . (self::$time + microtime(true)) . "ms");           
            return (self::$time + microtime(true));
        }
    }

}
