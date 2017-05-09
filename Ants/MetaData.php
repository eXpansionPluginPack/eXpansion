<?php

namespace ManiaLivePlugins\eXpansion\Ants;

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
        $this->setName(" Ants");
        $this->setDescription("Seasonal widget, creates ants at podium!");
        $this->setGroups(array('Widgets'));

        $config = Config::getInstance();

        $var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt(
            "spriteCount",
            "Ants count",
            $config,
            false,
            false
        );
        $var->setMin(1);
        $var->setMax(50);
        $var->setDefaultValue(20);


        $this->registerVariable($var);
    }
}
