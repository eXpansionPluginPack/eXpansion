<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Controls;

class GenericItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $bg;
    protected $nick;
    protected $label, $label1;
    protected $time;
    protected $frame;

    function __construct($index, $item)
    {
        $sizeX = 36;
        $sizeY = 3;

        $this->label1 = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->label1->setAlign('right', 'center');
        $this->label1->setPosition(0, 0);
        $this->label1->setStyle("TextRaceChat");
        $this->label1->setText($index + 1);
        $this->label1->setTextColor('fff');
        $this->label1->setScale(0.75);
        $this->addComponent($this->label1);

        $this->nick = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->nick->setPosition(8, 0);
        $this->nick->setAlign('left', 'center');
        $this->nick->setStyle("TextRaceChat");
        $this->nick->setTextColor('fff');
        $this->nick->setScale(0.75);
        $this->nick->setText($item->nickname);
        $this->addComponent($this->nick);

        $this->label = new \ManiaLib\Gui\Elements\Label(15, 5);
        $this->label->setAlign('left', 'center');
        $this->label->setStyle("TextRaceChat");
        $this->label->setScale(0.75);
        if (property_exists($item, 'longDate')) {
            $formatter = \ManiaLivePlugins\eXpansion\Gui\Formaters\LongDate::getInstance();
            $this->label->setText($formatter->format($item->longDate));
            $this->label->setPosX(-3);
            $this->label->setSizeX(25);
            $this->removeComponent($this->label1);
            $this->nick->setSizeX(20);
            $this->nick->setPosX(12);
        } elseif (property_exists($item, 'timeData')) {
            $this->label->setText(\ManiaLive\Utilities\Time::fromTM($item->timeData));
        } else {
            $this->label->setText($item->data);
        }
        $this->label->setTextColor('ff0');
        $this->addComponent($this->label);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}

