<?php

namespace ManiaLivePlugins\eXpansion\AutoLoad;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("AutoLoad");
	$this->setDescription('Autoloader, all-in-one solution for loading eXpansion easily');
	$config = Config::getInstance();
	$type = new String("","",null);

	$var = new BasicList('plugins', "Plugins to autoload", $config);
	$var->setType($type);
	$var->setDefaultValue(
		array('\ManiaLivePlugins\eXpansion\Core\Core'
		    , '\ManiaLivePlugins\eXpansion\Gui\Gui'
		    , '\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups'
		    , '\ManiaLivePlugins\eXpansion\Menu\Menu'
		    , '\ManiaLivePlugins\eXpansion\Adm\Adm'		   
		    , '\ManiaLivePlugins\eXpansion\AutoUpdate\AutoUpdate'
		    , '\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin'
		    , '\ManiaLivePlugins\eXpansion\Chatlog\Chatlog'
		    , '\ManiaLivePlugins\eXpansion\Database\Database'
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
	));
	$this->registerVariable($var);
    }

}
