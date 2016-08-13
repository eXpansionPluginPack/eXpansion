<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ReadyState\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

/**
 * Description of Countdown
 *
 * @author Petri
 */
class ReadyState extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{
    protected $label;
    protected $myScript;

    protected $parent;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->setName("Readystate Updater");
        $action = $this->createAction(array($this, "ready"));

        $this->myScript = new Script("Widgets_ReadyState\Gui\Script");
        $this->myScript->setParam("action", $action);
        $this->registerScript($this->myScript);
    }


    public function ready($login)
    {
        $this->parent->setReady($login);
    }

    public function setParent($plugin)
    {
        $this->parent = $plugin;
    }


}