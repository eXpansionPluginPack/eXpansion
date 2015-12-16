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
use Maniaplanet\DedicatedServer\Structures\GameInfos;

/**
 * Description of Debugtool
 *
 * @author Petri
 */
class Debugtool extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    private $ticker     = 0;
    private $testActive = false;

    public function exp_onReady()
    {
        $this->enableTickerEvent();
        $this->enableDedicatedEvents();
        //if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
        //	$this->enableScriptEvents();
        //$this->registerChatCommand("crash", "crash", 1, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("connect", "connect", 1, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("disconnect", "disconnect", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("profiler_enable", "profilere", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("gofaketest", "test", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("test", "testWin", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("mem", "mem", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->mem(null);
    }

    public function exp_onUnload()
    {
        $this->disableTickerEvent();
        parent::exp_onUnload();
    }

    function onTick()
    {
        if ($this->testActive) {
            if ($this->ticker < 30) {
                $this->connection->connectFakePlayer();
            } else {
                $this->ticker = 0;
                $this->connection->disconnectFakePlayer("*");
            }
            $this->ticker++;
        }
    }

    function testWin($login)
    {
        $win = Gui\testWindow::create($login);
        $win->setSize(120, 60);
        $win->show($login);
    }

    function connect($login, $playercount)
    {
        for ($x = 0; $x < $playercount; $x++) {
            $this->connection->connectFakePlayer();
        }
    }

    function disconnect($login)
    {
        try {
            $this->connection->disconnectFakePlayer("*");
        } catch (\Exception $e) {
            echo "error disconnecting;";
        }
    }

    public function profilere()
    {
        Dispatcher::setApplicationListener(new Profiler());
    }

    function LibXmlRpc_OnWayPoint($login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd)
    {
        //	echo "$login: cpindex: $cpIndex with $time\n";
    }

    function test($login)
    {
        $this->testActive = true;
    }

    function logMemory()
    {
        $mem = "Memory Usage: ".round(memory_get_usage() / 1024 / 1024)."Kb";
        //\ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
        print "\n".$mem."\n";
        $this->connection->chatSend($mem);
    }

    function mem($login)
    {
        Gui\debugWidget::EraseAll();
        $widget = Gui\debugWidget::Create(null);
        $widget->setPosition(155, -45);
        $widget->show();
    }

    function crash()
    {
        throw new \Exception("Crash Test");
    }
}