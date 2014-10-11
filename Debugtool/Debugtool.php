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

use Maniaplanet\DedicatedServer\Structures\GameInfos;

/**
 * Description of Debugtool
 *
 * @author Petri
 */
class Debugtool extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	private $counter = 0;

	private $ticker = 0;

	private $login = null;

	private $testActive = false;

	public function exp_onReady()
	{
		$this->enableTickerEvent();
		$this->enableDedicatedEvents();
		//if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
		//	$this->enableScriptEvents();

		$this->registerChatCommand("connect", "connect", 1, true, \ManiaLive\Features\Admin\AdminGroup::get());
		$this->registerChatCommand("disconnect", "disconnect", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
		//$this->registerChatCommand("starttest", "test", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
		//\ManiaLive\Event\Dispatcher::register(\ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent::getClass(), $this);

	//	$window = Gui\testWindow::Create("reaby");
	//	$window->show();
	}

	public function exp_onUnload()
	{
		$this->disableTickerEvent();
		\ManiaLivePlugins\eXpansion\DebugTool\Gui\testWidget::EraseAll();
		parent::exp_onUnload();
	}

	function onTick()
	{

		if ($this->ticker > 5) {
			$this->ticker = 0;
			\ManiaLivePlugins\eXpansion\DebugTool\Gui\testWidget::EraseAll();
			$widget = \ManiaLivePlugins\eXpansion\DebugTool\Gui\testWidget::Create();
			$widget->setPosition(60, 0);
			$widget->setData($this->connection->getPlayerList(20, 0));
			$widget->show();
			return;
		}

		if ($this->testActive) {
			echo $this->counter . "\n";
			\ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::EraseAll();
			\ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist::EraseAll();
			\ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\Records::EraseAll();
			\ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWindow::EraseAll();
			\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\StatsWindow::EraseAll();

			if ($this->counter > 50) {
				$this->counter = 0;
				$this->testActive = false;
				return;
			}
			$login = $this->login;
			$this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\Players\\Players", "showPlayerList", $login);
			$this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\Maps\\Maps", "showMapList", $login);
			$this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "showRecsWindow", $login, Null);
			$this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\Faq\\Faq", "showFaq", $login, "toc", null);
			$this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\Statistics\\Statistics", "showTopWinners", $login);
			$this->logMemory();
			$this->counter++;
		}
		$this->ticker++;
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

	function LibXmlRpc_OnWayPoint($login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd)
	{
	//	echo "$login: cpindex: $cpIndex with $time\n";
	}

	function test($login)
	{
		$this->login = $login;
		$this->counter = 0;
		$this->testActive = true;
	}

	function logMemory()
	{
		$mem = "Memory Usage: " . round(memory_get_usage() / 1024 / 1024) . "Kb";
		//\ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
		print "\n" . $mem . "\n";
		$this->connection->chatSend($mem);
	}

}
