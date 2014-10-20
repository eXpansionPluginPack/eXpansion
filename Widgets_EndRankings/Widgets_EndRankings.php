<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings;

use ManiaLive\PluginHandler\Dependency;
use ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Widgets\RanksPanel;

class Widgets_EndRankings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	private $wasWarmup = false;

	function exp_onInit()
	{
		$this->addDependency(new Dependency('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords'));
	}

	function exp_onReady()
	{
		$this->enableDedicatedEvents();
	}

	/**
	 * displayWidget(string $login)
	 * @param string $login
	 */
	function displayWidget($login = null)
	{
		$info = Gui\Widgets\RanksPanel::Create(null);
		$info->setData($this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRanks"));
		$info->setSize(38, 95);
		$info->setPosition(-160, 60);
		$info->show();
	}

	public function onBeginMap($map, $warmUp, $matchContinuation)
	{
		Gui\Widgets\RanksPanel::EraseAll();
	}

	public function onBeginMatch()
	{
		Gui\Widgets\RanksPanel::EraseAll();
	}

	public function onBeginRound()
	{
		$this->wasWarmup = $this->connection->getWarmUp();
	}

	public function onEndMatch($rankings, $winnerTeamOrMap)
	{
		if ($this->wasWarmup)
			return;
		$this->displayWidget();
	}

	function exp_onUnload()
	{
		RanksPanel::EraseAll();
	}

}
?>

