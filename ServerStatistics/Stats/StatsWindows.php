<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Stats;

class StatsWindows implements AbstractStat
{

    private $refresher;
    private $wmi;
    private $cpuValue;

    public function __construct()
    {
        $this->refresher = new \COM("WbemScripting.SWbemRefresher");
        $this->wmi = new \COM("winmgmts:{impersonationLevel=impersonate}//./root/cimv2");
        $this->cpuValue = $this->refresher->AddEnum($this->wmi, "Win32_PerfFormattedData_PerfOS_Processor");
    }

    public function getAvgLoad()
    {
        // testing query for perfmon data.. works but can't get load-average
        $loadArray = array();
        $this->refresher->Refresh();
        // ProcessorQueueLength
        foreach ($this->cpuValue->ObjectSet as $key => $set) {
            $loadArray = $set->PercentProcessorTime;
            // var_dump($set->Timestamp_Sys100NS);
        }
        return $loadArray[0];
    }

    public function getFreeMemory()
    {
        $total = 0;
        $free = 0;

        foreach ($this->wmi->ExecQuery("SELECT TotalPhysicalMemory FROM Win32_ComputerSystem") as $cs) {
            $total = $cs->TotalPhysicalMemory;
            break;
        }

        foreach ($this->wmi->ExecQuery("SELECT FreePhysicalMemory FROM Win32_OperatingSystem") as $os) {
            $free = $os->FreePhysicalMemory;
            break;
        }
        return new \ManiaLivePlugins\eXpansion\ServerStatistics\Structures\MemoryInfo($total, $free);
    }

    public function getUptime()
    {
        $boot = "";
        $ostime = "";
        foreach ($this->wmi->ExecQuery("SELECT LastBootUpTime FROM Win32_OperatingSystem") as $os) {
            $boot = floor($os->LastBootUpTime);
            break;
        }

        $booted = array(
            'year' => substr($boot, 0, 4),
            'month' => substr($boot, 4, 2),
            'day' => substr($boot, 6, 2),
            'hour' => substr($boot, 8, 2),
            'minute' => substr($boot, 10, 2),
            'second' => substr($boot, 12, 2)
        );

        $bootTime = mktime($booted['hour'], $booted['minute'], $booted['second'], $booted['month'], $booted['day'], $booted['year']);

        $uptime = (time() - $bootTime);
        return $uptime;
    }

}
