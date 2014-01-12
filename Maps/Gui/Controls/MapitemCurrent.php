<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use \ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class MapitemCurrent extends Mapitem {

    function __construct($indexNumber, $login, \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap $sortableMap, $controller, $isAdmin, $isHistory, $widths, $sizeX) {
        parent::__construct($indexNumber, $login, $sortableMap, $controller, $isAdmin, $isHistory, $widths, $sizeX);
        $this->label_author->setStyle("TextTitle2Blink");
        $this->label_author->setTextPrefix('$000');
        $this->label_author->setTextSize(2);
        
        $this->label_map->setStyle("TextTitle2Blink");
        $this->label_map->setTextPrefix('$000');
        $this->label_map->setTextSize(2);
        
        $this->label_rating->setStyle("TextTitle2Blink");
        $this->label_rating->setTextPrefix('$000');
        $this->label_rating->setTextSize(2);
        
        $this->label_authortime->setStyle("TextTitle2Blink");
        $this->label_authortime->setTextPrefix('$000');
        $this->label_authortime->setTextSize(2);
        
        $this->label_localrec->setStyle("TextTitle2Blink");
        $this->label_localrec->setTextPrefix('$000');
        $this->label_localrec->setTextSize(2);
    }

}
?>

