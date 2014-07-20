<?php

namespace ManiaLivePlugins\eXpansion\MapSuggestion;

use ManiaLivePlugins\eXpansion\AdminGroups\Permission;

class MapSuggestion extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public function exp_onReady()
    {
	$this->registerChatCommand("mapwish", "mapSuggestChatCommand", 0, true);
    }

    public function mapSuggestChatCommand($login)
    {
	$window = Gui\Windows\MapWish::Create($login);
	$window->show();
    }

}

?>