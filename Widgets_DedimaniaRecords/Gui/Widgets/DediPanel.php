<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Script_libraries\Animation;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

class DediPanel extends PlainPanel
{
    protected $trayWidget;

    function exp_onBeginConstruct()
    {
        parent::exp_onBeginConstruct();
        /*
          $this->animation = new Animation();
          $this->registerScript($this->animation);
         */
        $this->trayWidget = new Script("Gui/Scripts/NewTray");
        $this->registerScript($this->trayWidget);
    }
}
?>
