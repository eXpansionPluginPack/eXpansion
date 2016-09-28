<?php

namespace ManiaLivePlugins\eXpansion\Chatlog;

use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;

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
        $this->setName("Chat: Chatlog & History viewer");
        $this->setDescription("Logs chat and provides ingame command /chatlog for viewing chat history");
        $this->setGroups(array('Chat'));

        $config = Config::getInstance();
        $var = new TypeInt("historyLenght", "Chatlog history lenght", $config, false, false);
        $var->setCanBeNull(false)
            ->setDefaultValue(100);
        $this->registerVariable($var);
    }
}
