<?php

namespace ManiaLivePlugins\eXpansion\Minigame1;

use Exception;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Helpers\TimeConversion;
use ManiaLivePlugins\eXpansion\Minigame1\Config;
use ManiaLivePlugins\eXpansion\Minigame1\Gui\Widgets\MinigameWidget;

class Minigame1 extends ExpPlugin
{

	/** @var Config */
	private $config;

	private $timerGap = 30;

	private $active = true;

	private $tick = 0;

	public function exp_onReady()
	{

		$this->enableTickerEvent();

		$this->config = Config::getInstance();
		$this->timerGap = $this->getTimerGap();
		$ah = ActionHandler::getInstance();
		MinigameWidget::$action = $ah->createAction(array($this, "onClick"));

		Gui::preloadImage($this->config->mg1_imageUrl);
		Gui::preloadImage($this->config->mg1_imageFocusUrl);
		Gui::preloadUpdate();

		$this->colorParser->registerCode("game1", $this->config, "mg1_messageColor");
		
	}

	public function onClick($login)
	{
		try {
			$player = $this->storage->getPlayerObject($login);
			$amount = intval(rand($this->config->mg1_giftMin, $this->config->mg1_giftMax));
			$message = 'You won ' . $amount . 'p from ' . $this->storage->server->name . '$z$s minigame!' . "\n" . ' Congratulations! ';

			$this->connection->pay($login, $amount, $message);
			$this->exp_chatSendServerMessage('%1$s $z$s#game1# wins #variable#%2$s #game1#planets', null, array($player->nickName, $amount));
		} catch (Exception $e) {
			$ac = AdminGroups::getInstance();
			$ac->announceToPermission(Permission::server_admin, "Minigame1 Error: " . $e->getMessage());
			$this->console("[Minigame1] Error:" . $e->getMessage());
		}
	}

	public function onTick()
	{
		$this->tick++;

		if ($this->tick % 15 == 0) {
			$this->config = Config::getInstance();
		}

		if ($this->tick > $this->timerGap) {
			if ($this->connection->getServerPlanets() < $this->config->mg1_serverPlanetsMin) {
				$msg = exp_getMessage("#game1#The server has not enough #variable#Planets #game1#to run minigame, please #variable#Donate #game1#!");
				$this->exp_chatSendServerMessage($msg);
			}
			else {
				MinigameWidget::EraseAll();
				$widget = MinigameWidget::Create(null);
				$widget->setDisplayDuration($this->getDuration());
				$widget->show();
				$this->timerGap = $this->getTimerGap();
			}
			$this->tick = 0;
		}
	}

	private function getTimerGap()
	{
		$min = TimeConversion::MStoTM($this->config->mg1_displayIntervalMin);
		$max = TimeConversion::MStoTM($this->config->mg1_displayIntervalMax);
		return intval(rand($min, $max) / 1000);
	}

	private function getDuration()
	{
		$min = $this->config->mg1_displayDurationMin;
		$max = $this->config->mg1_displayDurationMax;
		return intval(rand($min, $max));
	}

	public function exp_onUnload()
	{
		$ah = ActionHandler::getInstance();
		$ah->deleteAction(MinigameWidget::$action);
		MinigameWidget::$action = -1;
		Gui::preloadRemove($this->config->mg1_imageUrl);
		Gui::preloadRemove($this->config->mg1_imageFocusUrl);
		Gui::preloadUpdate();

		parent::exp_onUnload();
	}

}

?>