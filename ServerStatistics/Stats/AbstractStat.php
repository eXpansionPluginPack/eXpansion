<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Stats;

/**
 * Description of AbstractStat
 *
 * @author Reaby
 */
interface AbstractStat
{

    function getAvgLoad();

    function getFreeMemory();

    function getUptime();
}
