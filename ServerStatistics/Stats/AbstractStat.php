<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Stats;

/**
 * Description of AbstractStat
 *
 * @author Reaby
 */
interface AbstractStat
{

    public function getAvgLoad();

    public function getFreeMemory();

    public function getUptime();
}
