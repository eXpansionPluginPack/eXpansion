<?php

namespace ManiaLivePlugins\eXpansion\NetStat\Gui\Widgets;

/**
 * Description of widget_netstat
 *
 * @author Petri
 */
class Helper_PingAnswer extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $script;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setName('Netstat PingAnswer');

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("NetStat/Gui/Scripts/PingAnswer");
        $this->registerScript($this->script);
    }

    public function setStamp($value)
    {
        $this->script->setParam('stamp', $value);
    }
}
