<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Netlost\Gui\Widgets;

class NetlostUpdate extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $clockBg;
    protected $frame, $players, $specs, $map, $author;
    protected $script;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Netlost\Gui\Scripts_NetAnnounce");
        $this->registerScript($this->script);
        $this->setName("NetLost Messaging");
    }

    public function setPlayer($string)
    {
        $this->script->setParam("players", $string);
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }

}

?>
