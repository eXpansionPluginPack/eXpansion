<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Dedimania;
use \ManiaLivePlugins\eXpansion\Core\types\config\types\String;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {
    
    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Dedimania");
	$this->setDescription("Dedimania, Global world records system integration");

        $this->addTitleSupport("TM");
        $this->addTitleSupport("Trackmania");
        $this->setEnviAsTitle(false);

        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
	$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
	$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
	$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
	$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	$this->setScriptCompatibilityMode(false);	
	
	$config	= Config::getInstance();
	
	$var = new String("login", "Dedimania server login (use this server login)", $config, false);
	$var->setDefaultValue("");
	$this->registerVariable($var);
	
	$var = new String("code", 'Dedimania $l[http://dedimania.net/tm2stats/?do=register]server code$l', $config, false);	
	$var->setDefaultValue("");
	$this->registerVariable($var);
    }
}
