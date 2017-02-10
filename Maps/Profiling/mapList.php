<?php


namespace ManialivePlugins\eXpansion\Maps\Profiling;

use ManiaLivePlugins\eXpansion\Core\types\Profiler;
use ManiaLivePlugins\eXpansion\Core\types\Profiler\ProfileInterface;

/**
 * Class mapList
 *
 * @author    de Cramer Oliver<oldec@smile.fr>
 * @copyright 2017 Smile
 * @package ManialivePlugins\eXpansion\Maps\Profiling
 */
class mapList implements ProfileInterface
{
    /** @var Profiler\Profile */
    protected static $currentProfile;

    /**
     * Start a new rank calculation profile.
     *
     * @return Profiler\Profile
     */
    public static function start()
    {
        self::$currentProfile = Profiler::startProfile('eXpansion.maps.map_list');

        return self::$currentProfile;
    }
}