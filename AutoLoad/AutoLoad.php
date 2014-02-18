<?php

namespace ManiaLivePlugins\eXpansion\AutoLoad;

use ManiaLive\Utilities\Console;

class AutoLoad extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $plugins = array('\ManiaLivePlugins\eXpansion\Core\Core'
	, '\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups'
	, '\ManiaLivePlugins\eXpansion\Menu\Menu'
	, '\ManiaLivePlugins\eXpansion\Adm\Adm'
	, '\ManiaLivePlugins\eXpansion\Chat\Chat'
	, '\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin'
	, '\ManiaLivePlugins\eXpansion\Chatlog\Chatlog'
	, '\ManiaLivePlugins\eXpansion\Database\Database'
	, '\ManiaLivePlugins\eXpansion\Emotes\Emotes'
	, '\ManiaLivePlugins\eXpansion\DonatePanel\DonatePanel'
	, '\ManiaLivePlugins\eXpansion\Faq\Faq'
	, '\ManiaLivePlugins\eXpansion\Gui\Gui'
	, '\ManiaLivePlugins\eXpansion\JoinLeaveMessage\JoinLeaveMessage'
	, '\ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords'
	, '\ManiaLivePlugins\eXpansion\ManiaExchange\ManiaExchange'
	, '\ManiaLivePlugins\eXpansion\MapRatings\MapRatings'
	, '\ManiaLivePlugins\eXpansion\Maps\Maps'
	, '\ManiaLivePlugins\eXpansion\PersonalMessages\PersonalMessages'
	, '\ManiaLivePlugins\eXpansion\Players\Players'
	, '\ManiaLivePlugins\eXpansion\Statistics\Statistics'
	, '\ManiaLivePlugins\eXpansion\Votes\Votes'
	, '\ManiaLivePlugins\eXpansion\Overlay_TeamScores\Overlay_TeamScores'
	, '\ManiaLivePlugins\eXpansion\Overlay_Positions\Overlay_Positions'
	, '\ManiaLivePlugins\eXpansion\Widgets_Clock\Widgets_Clock'
// , '\ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints'
	, '\ManiaLivePlugins\eXpansion\Widgets_EndRankings\Widgets_EndRankings'
	, '\ManiaLivePlugins\eXpansion\Widgets_PersonalBest\Widgets_PersonalBest'
	, '\ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide'
	, '\ManiaLivePlugins\eXpansion\Widgets_Times\Widgets_Times'
	, '\ManiaLivePlugins\eXpansion\Tutorial\Tutorial'
    );

    public function exp_onLoad() {

	$this->console("[eXpansion] AutoLoading eXpansion pack ... ");

//We Need the plugin Handler
	$pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

	$recheck = array();
	$lastSize = 0;

	$recheck = $this->loadPlugins($this->plugins, $pHandler);

	do {
	    $lastSize = sizeof($recheck);
	    $recheck = $this->loadPlugins($this->plugins, $pHandler);
	} while (!empty($recheck) && $lastSize != sizeof($recheck));

	if (!empty($recheck)) {
	    $this->dumpException("Couldn't Autoload all required plugins", new \Maniaplanet\WebServices\Exception("Autoload failed."));
	    $this->connection->chatSendServerMessage("couldn't Autoload all required plugins, see console log for more details.");
	    $this->console("Not all required plugins were loaded, list of not loaded plugins: ");
	    foreach ($recheck as $pname) {
		$this->console($pname);
	    }
	}
    }

    public function loadPlugins($list, \ManiaLive\PluginHandler\PluginHandler $pHandler) {
	$recheck = array();
	$disabled = Config::getInstance()->disable;
	if (!is_array($disabled))
	    $disabled = array($disabled);


	foreach ($list as $pname) {
	    try {
		if (!$pHandler->isLoaded($pname)) {
//$this->console("\n[eXpansion Pack]AutoLoading : Trying to Load $pname ... ");

		    if (in_array($pname, $disabled)) {
			$this->console("[" . $pname . "]..............................Disabled -> not loading");
			continue;
		    }
		    if (!$pHandler->load($pname)) {
			$this->console("[" . $pname . "]..............................FAIL -> will retry");

			$this->connection->chatSendServerMessage('Starting ' . $pname . '........$f00 Failure');
			$recheck[] = $pname;
		    } else {
			$this->debug("[" . $pname . "]..............................SUCCESS");
			//   $this->connection->chatSendServerMessage('Starting ' . $pname . '........$0f0 Success');
		    }
		}
	    } catch (\Exception $ex) {
		print_r($ex->getMessage());
		\ManiaLivePlugins\eXpansion\Core\types\ErrorHandler::displayAndLogError($ex);
		$this->connection->chatSendServerMessage("Error has occurred while loading plugins pack, see console for details.");
		$recheck[] = $pname;
	    }
	}
	return $recheck;
    }

}

?>
