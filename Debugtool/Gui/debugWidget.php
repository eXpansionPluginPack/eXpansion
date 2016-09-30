<?php

namespace ManiaLivePlugins\eXpansion\Debugtool\Gui;

use ManiaLib\Gui\Elements\Label;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;

/**
 * Description of widget_netstat
 *
 * @author Petri
 */
class debugWidget extends \ManiaLive\Gui\Window
{
    public $label;
    public $lastUpdate;
    public $lastValue = 0;
    private $nextLoop;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->lastUpdate = 0;
        Dispatcher::register(
            \ManiaLive\Application\Event::getClass(),
            $this,
            \ManiaLive\Application\Event::ON_POST_LOOP
        );

        $this->setName("Debug widget");


        $this->label = new Label(60);
        $this->label->setAlign("right", "top");
        $this->label->setPosX(0);
        $this->addComponent($this->label);
    }

    public function onPostLoop()
    {
        $startTime = microtime(true);
        if ($startTime < $this->nextLoop) {
            return;
        }
        $value = "mem usage: " . round((memory_get_usage() / 1024 / 1024), 3) . " Mb";
        if ($value == $this->lastValue) {
            $this->label->setTextColor("fff");
        } else {
            if ($value > $this->lastValue) {
                $this->label->setTextColor('d22');
            } else {
                $this->label->setTextColor('2d2');
            }
        }
        $this->label->setText($value);
        $this->RedrawAll();
        $this->lastUpdate = time();
        $this->lastValue = $value;

        $endTime = microtime(true);
        $this->nextLoop = $endTime + 0.95;
    }

    public function destroy()
    {
        parent::destroy();
        Dispatcher::unregister(TickEvent::getClass(), $this);
    }
}
