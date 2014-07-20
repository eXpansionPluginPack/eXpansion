<?php

namespace ManiaLivePlugins\eXpansion\MapSuggestion;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\ManiaExchange\Hooks\ListButtons;
use ManiaLivePlugins\eXpansion\ManiaExchange\Hooks\ListButtons_Event;

class MapSuggestion extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements ListButtons_Event
{

	public function exp_onReady()
	{
		$this->registerChatCommand("mapwish", "mapSuggestChatCommand", 0, true);

		Dispatcher::register(ListButtons::getClass(), $this);
	}



	public function mapSuggestChatCommand($login)
	{
		$window = Gui\Windows\MapWish::Create($login);
		$window->show();
	}

	/**
	 *
	 * @param $buttons
	 * @param $login
	 *
	 * @return mixed
	 */
	public function hook_ManiaExchangeListButtons($buttons, $login)
	{
		if(isset($buttons->data['queue'])){
			echo "Removing \n";
			unset($buttons->data['queue']);
		}
	}
}

?>