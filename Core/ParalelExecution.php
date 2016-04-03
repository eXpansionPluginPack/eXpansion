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

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;

/**
 * This class allows you to create executions that will run in paralel with the rest of the application
 *
 * @package ManiaLivePlugins\eXpansion\Core
 */
class ParalelExecution implements \ManiaLive\Features\Tick\Listener
{

    private $pid;
    private $id;

    private $results = array();

    private $callback;

    private $lastCheck;

    private $return = 0;

    private $cmds = array();

    private $values;

    private $executionName;

    private $fileName;


    function __construct($cmds, $callback, $executionName = "")
    {
        $this->id = time() . '.' . rand(0, 100000);
        $this->callback = $callback;

        if (!file_exists('tmp'))
            mkdir('tmp/');

        $this->cmds = $cmds;

        $this->executionName = $executionName;

        if (empty($this->executionName)) {
            $this->fileName = $this->id . 'log';
        } else {
            $this->fileName = $this->executionName . '.' . time() . '.log';
        }
    }

    public function start()
    {
        Dispatcher::register(TickEvent::getClass(), $this);
        $this->run();
    }

    private function run()
    {
        $cmd = array_shift($this->cmds);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            Dispatcher::unregister(TickEvent::getClass(), $this);
            $command = $cmd . ' > tmp/' . $this->fileName . ' 2>&1';
            exec($command, $results, $return);

            $this->return = $return;

            $this->results = array_merge($this->results, $results);
            if (empty($this->cmds) || $this->return != 0) {
                $this->call($this->results);
            } else {
                $this->run();
            }
            return;
        } else {
            $command = 'nohup ' . $cmd . ' >> tmp/' . $this->fileName . ' 2>&1 & echo $!';
            exec($command, $results, $return);
            $this->pid = $results[0];
        }


        if ($this->pid == "") {
            Dispatcher::unregister(TickEvent::getClass(), $this);
            $this->call();
        }

        $this->lastCheck = time();
    }

    public function call($results = array())
    {
        if (empty($results) && file_exists('tmp/' . $this->fileName)) {
            $results = explode("\n", file_get_contents('tmp/' . $this->fileName));
        }
        call_user_func($this->callback, $this, $results, $this->return);
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return String[]
     */
    public function getResults()
    {
        return $this->results;
    }

    public function PsExists()
    {
        exec("ps ax | grep " . $this->pid . " 2>&1", $output);

        if (!$output)
            return false;

        while (list(, $row) = each($output)) {

            $row_array = explode(" ", $row);
            $check_pid = $row_array[0];

            if ($this->pid == $check_pid) {
                return true;
            }

        }

        return false;
    }

    /**
     * Event launch every seconds
     */
    function onTick()
    {
        if (!$this->PsExists()) {
            if (empty($this->cmds) || $this->return != 0) {
                $this->call();
                Dispatcher::unregister(TickEvent::getClass(), $this);
            } else {
                $this->run();
            }
        }
    }

    public function setValue($key, $val)
    {
        $this->values[$key] = $val;
    }

    public function getValue($key)
    {
        return $this->values[$key];
    }
}