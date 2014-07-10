<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Netlost;

use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Widgets_Netlost\Gui\Widgets\Netlost;

class Widgets_Netlost extends ExpPlugin
{

    private $buffer = "";

    function exp_onLoad()
    {
	$this->enableDedicatedEvents();
    }

    function exp_onReady()
    {
	$this->displayWidget(null);
    }

    public function netlostTest($login)
    {
	$info = new \Maniaplanet\DedicatedServer\Structures\PlayerNetInfo();
	$info->login = "reaby";
	$this->onPlayerNetLost(array($info));
    }

    /**
     * @param \Maniaplanet\DedicatedServer\Structures\PlayerNetInfo[] $players
     */
    public function onPlayerNetLost($players)
    {
	$out = "";
	$comma = "";

	foreach ($players as $player) {
	    $pla = $this->storage->getPlayerObject($player->login);
	    $out .= $comma . '"' . \ManiaLivePlugins\eXpansion\Gui\Gui::fixString($pla->nickName) . ' $z$s('.\ManiaLivePlugins\eXpansion\Gui\Gui::fixString($player->login).') "';
	    $comma = ", ";
	}

	if (empty($out)) {
	    $out = "Text[]";
	} else {
	    $out = "[" . $out . "]";
	}
	
	if ($this->buffer !== $out) {	    
	    $update = Gui\Widgets\NetlostUpdate::Create();
	    $update->setPlayer($out);
	    $update->setTimeout(2);
	    $update->show();	   
	    $this->buffer = $out;
	}
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login)
    {
	$info = Netlost::Create(null);
	$info->setSize(200, 12);
	$info->setPosition(-115, -50);
	$info->show();
    }

    function exp_onUnload()
    {
	Netlost::EraseAll();
    }

}
?>

