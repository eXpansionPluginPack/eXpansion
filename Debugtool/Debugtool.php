<?php
/*
 * Copyright (C) Error: on line 4, column 33 in Templates/Licenses/license-gpl20.txt
  The string doesn't match the expected date/time format. The string to parse was: "7.2.2014". The expected format was: "dd-MMM-yyyy". Petri
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace ManiaLivePlugins\eXpansion\Debugtool;

use ManiaLive\Event\Dispatcher;

/**
 * Description of Debugtool
 *
 * @author Petri
 */
class Debugtool extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    private $ticker = 0;
    private $testActive = false;
    private $fakelogin = "";

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->enableDedicatedEvents();
        $this->registerChatCommand(
            "connect",
            "connect",
            1,
            true,
            \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::get()
        );
        $this->registerChatCommand(
            "disconnect",
            "disconnect",
            1,
            true,
            \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::get()
        );
        $this->registerChatCommand(
            "faketest",
            "test",
            0,
            true,
            \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::get()
        );
        $this->mem(null);
        $this->registerChatCommand("te", "team", 0, false);

    }

    public function team()
    {
        $outScores = [];
        $outScores[] = array("PlayerId" => 0, "Score" => 1);
        $outScores[] = array("PlayerId" => 1, "Score" => 2);

        $this->connection->forceScores($outScores, false);
    }

    public function eXpOnUnload()
    {
        $this->disableTickerEvent();
        parent::eXpOnUnload();
    }

    public function onTick()
    {
        if ($this->testActive) {
            if ($this->ticker == 1) {

                $this->connection->disconnectFakePlayer($this->fakeLogin);
                $this->ticker = 0;
            } else {
                $this->fakeLogin = $this->connection->connectFakePlayer();
                $this->ticker = 1;
            }
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
    }

    public function testWin($login)
    {
        $win = Gui\testWindow::create($login);
        $win->setSize(120, 60);
        $win->show($login);
    }

    public function connect($login, $playercount)
    {
        for ($x = 0; $x < $playercount; $x++) {
            $this->connection->connectFakePlayer();
        }
    }

    public function disconnect($login, $amount)
    {
        try {
            if (is_numeric($amount)) {
                $x = 0;
                $players = array_merge($this->storage->players, $this->storage->spectators);

                foreach ($players as $login => $player) {
                    echo "$login, $x,  $amount\n";
                    if (strstr($login, "fakeplayer") !== false && $x < $amount) {
                        $this->connection->disconnectFakePlayer($login);
                        $x++;
                    }
                }
            } else {
                $this->connection->disconnectFakePlayer("*");
            }
        } catch (\Exception $e) {
            echo "error disconnecting;";
        }
    }

    public function onStatusChanged($statusCode, $statusName)
    {
        echo $statusCode . ": " . $statusName . " \n";
    }

    public function profilere()
    {
        Dispatcher::setApplicationListener(new Profiler());
    }

    public function LibXmlRpc_OnWayPoint($login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd)
    {
    }

    public function test($login)
    {
        $this->testActive = !$this->testActive;
    }

    public function logMemory()
    {
        $mem = "Memory Usage: " . round(memory_get_usage() / 1024 / 1024) . "Kb";
        print "\n" . $mem . "\n";
        $this->connection->chatSend($mem);
    }

    public function mem($login)
    {
        Gui\debugWidget::EraseAll();
        $widget = Gui\debugWidget::Create(null);
        $widget->setPosition(155, -45);
        $widget->show();
    }

    public function crash()
    {
        throw new \Exception("Crash Test");
    }
}
