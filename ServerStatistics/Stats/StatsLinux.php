<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Stats;

class StatsLinux implements AbstractStat
{

    public $previousLoad = array();

    public function __construct()
    {
        $handle = fopen("/proc/stat", "r");
        $load = fscanf($handle, "%*s %Lf %Lf %Lf %Lf");
        fclose($handle);
        $this->previousLoad = $load;
    }

    public function getAvgLoad()
    {
        // get cpu usage
        $handle = fopen("/proc/stat", "r");
        // get the contents to array
        $a = fscanf($handle, "%*s %Lf %Lf %Lf %Lf");
        fclose($handle);

        $b = $this->previousLoad;
        // do magic with the values
        $loadAvg = 100
            * (($b[0] + $b[1] + $b[2]) - ($a[0] + $a[1] + $a[2]))
            / (($b[0] + $b[1] + $b[2] + $b[3]) - ($a[0] + $a[1] + $a[2] + $a[3]));
        $this->previousLoad = $a;

        return $loadAvg;
    }

    public function getFreeMemory()
    {
        $matches = array();
        $memVals = array();
        @preg_match_all(
            '/^([^:]+)\:\s+(\d+)\s*(?:k[bB])?\s*/m',
            file_get_contents('/proc/meminfo'),
            $matches,
            PREG_SET_ORDER
        );
        foreach ((array)$matches as $memInfo) {
            $memVals[$memInfo[1]] = $memInfo[2];
        }

        $total = $memVals['MemTotal'] * 1024;
        $free = $memVals['MemFree'] * 1024 + $memVals['Cached'] * 1024 + $memVals['Buffers'] * 1024;

        return new \ManiaLivePlugins\eXpansion\ServerStatistics\Structures\MemoryInfo($total, $free);
    }

    public function getUptime()
    {
        $contents = file_get_contents('/proc/uptime', false);
        list($seconds) = explode(' ', $contents, 1);

        return $seconds;
    }
}
