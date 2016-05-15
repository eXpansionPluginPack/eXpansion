<?php

namespace ManiaLivePlugins\eXpansion\Irc;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

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

        $var = new TypeString("hostname", "hostname", $config, true, false);
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $var = new TypeString("server", "irc server to connect", $config, true, false);
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $var = new TypeInt("port", "connection port for the server", $config, true, false);
        $var->setDefaultValue(6667);
        $this->registerVariable($var);

        $var = new TypeString("serverPass", "password for server", $config, true, false);
        $var->setDefaultValue("");

        $this->registerVariable($var);

        $var = new TypeString("realname", "Ircbot realname", $config, false, false);
        $var->setDefaultValue("ManiaPlanet bot");
        $this->registerVariable($var);

        $var = new TypeString("nickname", "Ircbot nickname", $config, false, false);
        $var->setDefaultValue("maniaplanet_bot");
        $this->registerVariable($var);

        $var = new TypeString("ident", "ident for bot", $config, false, false);
        $var->setDefaultValue("maniaplanet_bot");
        $this->registerVariable($var);

        $var = new TypeString("channel", "irc channel to join", $config, false, false);
        $var->setDefaultValue("#bots");
        $this->registerVariable($var);

        $var = new TypeString("channelKey", "irc channel key", $config, false, false);
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $var = new BasicList("allowedIrcLogins", "Allowed Irc nicknames to use admin commands", $config, false, false);
        $var->setType(new TypeString(""));
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
