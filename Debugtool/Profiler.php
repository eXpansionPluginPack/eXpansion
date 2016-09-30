<?php
/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace ManiaLivePlugins\eXpansion\Debugtool;

use ManiaLive\Application\ApplicationListener;
use ManiaLive\Data\Storage;

class Profiler implements ApplicationListener
{

    protected $totalMemoryPerClass = array();
    protected $lastMemory = 0;

    private $client;
    /** @var  Storage */
    private $storage;

    public function __construct()
    {
        $this->client = new \Elasticsearch\Client();
        $this->storage = Storage::getInstance();
    }

    public function beforeFireDo($listener, $event)
    {
        $this->lastMemory = memory_get_usage();
    }


    public function afterFireDo($listener, $event)
    {
        $newMemoryUsage = memory_get_usage();

        $class = get_class($listener);

        if (!isset($this->totalMemoryPerClass[$class])) {
            $this->totalMemoryPerClass[$class] = 0;
        }

        $diff = $newMemoryUsage - $this->lastMemory;

        if (abs($diff) > 10) {
            $this->totalMemoryPerClass[$class] += $diff;
            echo "Mem diff for : $class : " . $diff . " | TOTAL : $newMemoryUsage\n";

            $index = "expansion-profiler";

            $params['body'] = array(
                'nbPlayers' => count($this->storage->players),
                '@timestamp' => $this->getCurrentTimeStamp(time()),
                'class' => $this->getClassName($class),
                'memory_total' => $newMemoryUsage,
                'memory_usage' => $this->totalMemoryPerClass[$class],
                'memory_diff' => $diff,
                'event_class' => $this->getClassName(get_class($event)),
                'event_id' => $event->getMethod(),
            );
            $params['index'] = $index;
            $params['type'] = 'memory_log';

            $this->client->index($params);
        }
    }

    function getClassName($class)
    {
        return str_replace('\\', '', $class);
    }

    function getCurrentTimeStamp($time)
    {
        return date('Y-m-d', ($time)) . 'T' . date('H:i:s', ($time)) . 'Z';
    }
}
