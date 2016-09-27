<?php

namespace ManiaLivePlugins\eXpansion\Minigame1;

class Config extends \ManiaLib\Utils\Singleton
{
    public $mg1_displayIntervalMin = "2:00";

    public $mg1_displayIntervalMax = "3:00";

    public $mg1_displayDurationMin = 1500;

    public $mg1_displayDurationMax = 2500;

    public $mg1_giftMin = 5;

    public $mg1_giftMax = 20;

    public $mg1_serverPlanetsMin = 1000;

    public $mg1_messageColor = '$fff';

    public $mg1_connectionMessage = "Welcome to play minigame also, Just click the appearing image to win!";

    public $mg1_imageUrl = "http://koti.mbnet.fi/reaby/xaseco/images/santa.png";
    public $mg1_imageFocusUrl = "http://koti.mbnet.fi/reaby/xaseco/images/santa.png";
    public $mg1_imageSize = 20;
}
