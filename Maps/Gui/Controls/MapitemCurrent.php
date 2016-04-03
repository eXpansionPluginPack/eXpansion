<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use \ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class MapitemCurrent extends Mapitem
{

    function __construct($indexNumber, $login, \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap $sortableMap, $controller, $isAdmin, $isHistory, $widths, $sizeX)
    {
        parent::__construct($indexNumber, $login, $sortableMap, $controller, $isAdmin, $isHistory, $widths, $sizeX);
        $style = "TextTitle2Blink";
        $color = '$0af';
        $size = "2";

        $this->label_author->setStyle($style);
        $this->label_author->setTextPrefix($color);
        $this->label_author->setTextSize($size);

        $this->label_map->setStyle($style);
        $this->label_map->setTextPrefix($color);
        $this->label_map->setTextSize($size);

        $this->label_rating->setStyle($style);
        $this->label_rating->setTextPrefix($color);
        $this->label_rating->setTextSize($size);

        $this->label_authortime->setStyle($style);
        $this->label_authortime->setTextPrefix($color);
        $this->label_authortime->setTextSize($size);

        $this->label_localrec->setStyle($style);
        $this->label_localrec->setTextPrefix($color);
        $this->label_localrec->setTextSize($size);
    }

}

?>

