<?php

namespace ManiaLivePlugins\eXpansion\AutoLoad;

class Config extends \ManiaLib\Utils\Singleton {

    public $disable = array();
    public $plugins = array('\ManiaLivePlugins\eXpansion\Chatlog\Chatlog'
	, '\ManiaLivePlugins\eXpansion\Emotes\Emotes'
	, '\ManiaLivePlugins\eXpansion\DonatePanel\DonatePanel'
	, '\ManiaLivePlugins\eXpansion\Faq\Faq'
	, '\ManiaLivePlugins\eXpansion\JoinLeaveMessage\JoinLeaveMessage'
	, '\ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords'
	, '\ManiaLivePlugins\eXpansion\ManiaExchange\ManiaExchange'
	, '\ManiaLivePlugins\eXpansion\MapRatings\MapRatings'
	, '\ManiaLivePlugins\eXpansion\Maps\Maps'
	, '\ManiaLivePlugins\eXpansion\PersonalMessages\PersonalMessages'
	, '\ManiaLivePlugins\eXpansion\Players\Players'
	, '\ManiaLivePlugins\eXpansion\Statistics\Statistics'
	, '\ManiaLivePlugins\eXpansion\Votes\Votes'
	, '\ManiaLivePlugins\eXpansion\Widgets_Clock\Widgets_Clock'
	, '\ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Widgets_BestCheckpoints'
	, '\ManiaLivePlugins\eXpansion\Widgets_EndRankings\Widgets_EndRankings'
	, '\ManiaLivePlugins\eXpansion\Widgets_PersonalBest\Widgets_PersonalBest'
	, '\ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide'
	, '\ManiaLivePlugins\eXpansion\Widgets_Times\Widgets_Times'
	, '\ManiaLivePlugins\eXpansion\Widgets_ResSkip\Widgets_ResSkip'
	, '\ManiaLivePlugins\eXpansion\Widgets_ServerInfo\Widgets_ServerInfo'
	, '\ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Widgets_LocalRecords'
	, '\ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Widgets_LiveRankings'
	, '\ManiaLivePlugins\eXpansion\Widgets_Dedimania\Widgets_Dedimania'
    );

    public $pluginPaths = array(
		'libraries/ManiaLivePlugins' => 2,
		'vendor' => 4,
	);

}

?>