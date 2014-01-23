<?php

namespace ManiaLivePlugins\eXpansion\Mumble;

use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Mumble\Config;

class Mumble extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $enabled = true;
  private $icon_mumble = "http://tmrankings.com/manialink/mv/icons/mumble.png";
	private $icon_channel = "http://tmrankings.com/manialink/mv/icons/channel.png";
	private $icon_user_on = "http://tmrankings.com/manialink/mv/icons/user_on.png";
	private $icon_user_off = "http://tmrankings.com/manialink/mv/icons/user_off.png";
	private $icon_authenticated = "http://tmrankings.com/manialink/mv/icons/authenticated.png";
	private $icon_muted_server = "http://tmrankings.com/manialink/mv/icons/muted_server.png";
	private $icon_deaf_self =  "http://tmrankings.com/manialink/mv/icons/deafened_self.png";
	private $icon_deaf_server =  "http://tmrankings.com/manialink/mv/icons/deafened_server.png";
	private $icon_selfmute = "http://tmrankings.com/manialink/mv/icons/muted_self.png";
	private $icon_active = "http://tmrankings.com/manialink/mv/icons/talking_alt.png";
	private $mumble_feed;
	private $channels;
	
	function exp_onInit() {
        //Important for all eXpansion plugins.
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
		$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT);
    }
	
    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function exp_onLoad() {
        $this->enableDedicatedEvents();
		$this->enableTickerEvent();
    }

    function onUnload() {
        parent::onUnload();
    }

    /*
     * onReady()
     * Function called when ManiaLive is ready loading.
     *
     * @return void
     */

    function exp_onReady() {
	$config = Config::getInstance();
	$this->connection->chatSendServerMessage("Mumble trying to load"); //debug
        try {
	$this->mumble_feed = json_decode(file_get_contents($config->url), true);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage('%server%Mumble Error: $fff»» %error%' . utf8_encode($e->getMessage()));
            echo $e->getMessage();
            $this->enabled = false;
        }	
	 $this->connection->chatSendServerMessage("Mumble Loaded"); //debug
        foreach ($this->storage->players as $login => $player) {
            $this->showWidget($login);
		$version = $this->connection->getVersion();
        $this->titleId = $version->titleId;
		if ($this->titleId == "SMStorm" || $this->titleId == "SMStormRoyal@nadeolabs") {
                $this->connection->TriggerModeScriptEvent("LibXmlRpc_DisableAltMenu", $login);
            }
        }
        foreach ($this->storage->spectators as $login => $player) {
            $this->showWidget($login);
        }
    }
	
	function onTick()
	{
	$time = time();
	$config = Config::getInstance();
	if ($time >= 30) 
		{         
        try {
	$this->mumble_feed = json_decode(file_get_contents($config->url), true);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage('%server%Mumble Error: $fff»» %error%' . utf8_encode($e->getMessage()));
            echo $e->getMessage();
            $this->enabled = false;
		}
        foreach ($this->storage->players as $login => $player) {
            $this->showWidget($login);
        }
        foreach ($this->storage->spectators as $login => $player) {
            $this->showWidget($login);
        }
		}
	}

	 /**
     * showWidget(string $login)
     * @param string $login
     */
    function showWidget($login = null) {
        $info = Gui\Widgets\MumbleWidget::Create(null);
        $info->setPosition(-155, -20, 1);
		$info->setScale(0.8);
        $info->setAlign("left", "top");
        $info->show();
    }
	
		 /**
     * showMumble(string $login)
     * @param string $login
     */
    function showMumble($login = null) {
        $info = Gui\Widgets\MumbleInfo::Create(null);
		$info->setPosition(-100, 32, 1);
		$info->setData($this->mumble_feed);
        $info->show();
    }
    
    function onPlayerManialinkPageAnswer($playerUid, $login, $answer,array $entries)
{
if($answer == "MumbleLogo"){
$this->showMumble($login);
}
if($answer == "0"){
	Gui\Widgets\MumbleInfo::EraseAll();
}
}
	
    /**
     * onPlayerConnect()
     * Function called when a player connects.
     *
     * @param mixed $login
     * @param mixed $isSpectator
     * @return void
     */
    function onPlayerConnect($login, $isSpec) {
        $this->showWidget($login);
    }

    function onPlayerDisconnect($login, $reason = null) {
	Gui\Widgets\MumbleInfo::Erase($login);
	Gui\Widgets\MumbleWidget::Erase($login);
    }


}

?>