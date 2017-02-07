<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLivePlugins\eXpansion\Core\types\Profiler;
use ManiaLivePlugins\eXpansion\Core\types\Profiler\ProfileInterface;


/**
 * Class rankCalculation
 *
 * @author    de Cramer Oliver<oldec@smile.fr>
 * @copyright 2017
 * @package ${NAMESPACE}
 */
class rankCalculation implements ProfileInterface
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
        self::$currentProfile = Profiler::startProfile('eXpansion.local_records.rank_calculation');

        return  self::$currentProfile;
    }
}