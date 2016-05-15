<?php

namespace ManiaLivePlugins\eXpansion\Minigame1;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\ColorCode;
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
        $this->setName("Games: Minigame 1");
        $this->setDescription("An image appears on screen at random position every now and then, to win just click");
        $this->setGroups(array('Games'));

        $config = Config::getInstance();

        $var = new ColorCode("mg1_messageColor", "Color for message", $config, false, false);
        $var->setDefaultValue('$fff');
        $this->registerVariable($var);

        $var = new TypeString("mg1_imageUrl", "Image url, normal", $config, false, false);
        $var->setDefaultValue("http://koti.mbnet.fi/reaby/xaseco/images/santa.png");
        $this->registerVariable($var);

        $var = new TypeString("mg1_imageFocusUrl", "Image url, hover", $config, false, false);
        $var->setDefaultValue("http://koti.mbnet.fi/reaby/xaseco/images/santa.png");
        $this->registerVariable($var);

        $var = new BoundedTypeInt("mg1_imageSize", "Size of the image", $config, false, false);
        $var->setMin(5);
        $var->setDefaultValue(20);
        $this->registerVariable($var);

        $var = new TypeString("mg1_connectionMessage", "Message on player connect", $config, false, false);
        $var->setDefaultValue("Welcome to play minigame also, Just click the appearing image to win!");
        $this->registerVariable($var);

        $var = new TypeString("mg1_displayIntervalMin", "Min interval in mm:ss", $config, false, false);
        $var->setDefaultValue("2:00");
        $this->registerVariable($var);

        $var = new TypeString("mg1_displayIntervalMax", "Max interval in mm:ss", $config, false, false);
        $var->setDefaultValue("2:00");
        $this->registerVariable($var);

        $var = new BoundedTypeInt("mg1_displayDurationMin", "Min display duration in milliseconds", $config, false, false);
        $var->setDescription("Min value 500, no bound for Max");
        $var->setMin(500);
        $var->setDefaultValue(1500);
        $this->registerVariable($var);

        $var = new BoundedTypeInt("mg1_displayDurationMax", "Max display duration in milliseconds", $config, false, false);
        $var->setDescription("Min value 1000, no bound for Max");
        $var->setMin(1000);
        $var->setDefaultValue(2500);
        $this->registerVariable($var);

        $var = new BoundedTypeInt("mg1_giftMin", "Min planets to gift for success", $config, false, false);
        $var->setDescription("Min value 5, no bound for Max");
        $var->setMin(5);
        $var->setDefaultValue(5);
        $this->registerVariable($var);

        $var = new BoundedTypeInt("mg1_giftMax", "Max planets to gift for success", $config, false, false);
        $var->setDescription("Min value 25, no bound for Max");
        $var->setMin(25);
        $var->setDefaultValue(25);
        $this->registerVariable($var);

        $var = new BoundedTypeInt("mg1_serverPlanetsMin", "Minimum server planets to run plugin", $config, false, false);
        $var->setDescription("Treshold to disable plugin if server planets are low, minimum value 100");
        $var->setMin(100);
        $var->setDefaultValue(1000);
        $this->registerVariable($var);
    }

}
