<?php


namespace ManiaLivePlugins\eXpansion\Core\types;

use ManiaLivePlugins\eXpansion\Core\Analytics;
use ManiaLivePlugins\eXpansion\Core\types\Profiler\Profile;


/**
 * Class Profiler
 *
 * @author    de Cramer Oliver<oldec@smile.fr>
 * @copyright 2017
 * @package ManiaLivePlugins\eXpansion\Core\types
 */
class Profiler
{
    /** @var Profiler|null  */
    protected static $_instance = null;

    /** @var  Analytics */
    protected $analytics;

    /**
     * Profiler constructor.
     */
    public function __construct(Analytics $analytics)
    {
        self::$_instance = $this;
    }

    /**
     * @param $name
     * @param string $description
     * @param int $numberOfElements
     * @param bool $sendData
     *
     * @return Profile
     */
    public static function startProfile($name, $description = '', $numberOfElements = 0, $sendData = True) {
        if ($sendData) {
            return new Profile($name, $description, $numberOfElements, self::$_instance->analytics);
        }
        return new Profile($name, $description, $numberOfElements);
    }

}