<?php

namespace ManiaLivePlugins\eXpansion\Core\types;

/**
 * eXpansion ErrorHandler
 *
 * @author Reaby
 */
class ErrorHandler extends \ManiaLive\Application\ErrorHandling {

    public static $server = "generic";

    public static function createExceptionFromError($errno, $errstr, $errfile, $errline) {
        parent::createExceptionFromError($errno, $errstr, $errfile, $errline);
    }

    public static function displayAndLogError(\Exception $e) {
        $log = "";
        foreach (self::computeMessage($e) as $line) {
            $log .= $line . PHP_EOL;
        }
        \ManiaLive\Utilities\Logger::log($log, false, self::$server . ".error.log");
        parent::displayAndLogError($e);
    }

}
