<?php
namespace ManiaLivePlugins\eXpansion\Core\types;

use ManiaLive\Application\ErrorHandling;
use ManiaLive\Utilities\Logger;

/**
 * eXpansion ErrorHandler
 *
 * @author Reaby
 */
class ErrorHandler extends ErrorHandling
{

    public static $server = "generic";

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    public static function createExceptionFromError($errno, $errstr, $errfile, $errline)
    {
        parent::createExceptionFromError($errno, $errstr, $errfile, $errline);
    }

    /**
     * @param \Exception $e
     * @param string $type
     */
    public static function displayAndLogError(\Exception $e, $type = "")
    {
        $log = "";
        foreach (self::computeMessage($e) as $line) {
            $log .= $line . PHP_EOL;
        }
        Logger::log($log, false, self::$server . ".error.log");
        parent::displayAndLogError($e, $type);
    }
}
