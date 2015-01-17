<?php

namespace ManiaLivePlugins\eXpansion\Irc;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{
		parent::onBeginLoad();
		$this->setName("Tools: IRC chat link and remote admin");
		$this->setDescription("Provides IRC link for chatting and remote administration, needs php_sockets extension.");
		$this->setGroups(array('Connectivity', 'Chat', 'Tools'));
		$config = Config::getInstance();

		$var = new String("hostname", "hostname", $config, true, false);
		$var->setDefaultValue("");
		$this->registerVariable($var);

		$var = new String("server", "irc server to connect", $config, true, false);
		$var->setDefaultValue("");
		$this->registerVariable($var);

		$var = new Int("port", "connection port for the server", $config, true, false);
		$var->setDefaultValue(6667);
		$this->registerVariable($var);

		$var = new String("serverPass", "password for server", $config, true, false);
		$var->setDefaultValue("");

		$this->registerVariable($var);

		$var = new String("realname", "Ircbot realname", $config, false, false);
		$var->setDefaultValue("ManiaPlanet bot");
		$this->registerVariable($var);

		$var = new String("nickname", "Ircbot nickname", $config, false, false);
		$var->setDefaultValue("maniaplanet_bot");
		$this->registerVariable($var);

		$var = new String("ident", "ident for bot", $config, false, false);
		$var->setDefaultValue("maniaplanet_bot");
		$this->registerVariable($var);

		$var = new String("channel", "irc channel to join", $config, false, false);
		$var->setDefaultValue("#bots");
		$this->registerVariable($var);

		$var = new String("channelKey", "irc channel key", $config, false, false);
		$var->setDefaultValue("");
		$this->registerVariable($var);

		$var = new BasicList("allowedIrcLogins", "Allowed Irc nicknames to use admin commands", $config, false, false);
		$var->setType(new String(""));
		$var->setDefaultValue(array());
		$this->registerVariable($var);
	}

	public function checkOtherCompatibility()
	{
		
		if (!extension_loaded("sockets"))
			return array('You need extension "php_sockets" enabled for php!');
		return array();
	}

}
