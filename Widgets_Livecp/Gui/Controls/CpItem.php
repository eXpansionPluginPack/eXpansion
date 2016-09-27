<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Controls;


use ManiaLib\Gui\Elements\Label;
use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\Gui\Elements\Gauge;
use ManiaLivePlugins\eXpansion\Widgets_Livecp\Structures\CpInfo;

class CpItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $gauge;
    protected $nickName;
    protected $time;
    protected $cpIndex;

    /**
     * CpItem constructor.
     * @param int $index
     * @param CpInfo $data
     */
    public function __construct($index, $player, $data, $totalCps)
    {
        $colors = array("3AF", "3AF", "3BD", "3BD", "3CA", "3C8", "3D7", "3D5", "3E3", "3E2", "3F0");

        $this->cpIndex = new Label(5, 5);
        $this->cpIndex->setAlign("right", "top");
        $this->cpIndex->setStyle("TextRaceChat");
        $this->cpIndex->setTextSize(1);
        $this->cpIndex->setPosX(4.5);
        $this->cpIndex->setText(($index + 1) . ".");
        $this->addComponent($this->cpIndex);

        $this->cpIndex = new Label(5, 5);
        $this->cpIndex->setStyle("TextRaceChat");
        $this->cpIndex->setTextSize(1);
        $this->cpIndex->setPosX(5);
        $this->cpIndex->setText("CP-" . ($data->cpIndex + 1));
        $this->cpIndex->setTextColor("f90");
        $this->addComponent($this->cpIndex);

        $this->time = new Label(10, 5);
        $this->time->setPosX(12);
        $this->time->setStyle("TextRaceChat");
        $this->time->setTextSize(1);
        $this->time->setText(Time::fromTM($data->time));
        $this->time->setTextColor("3af");
        $this->addComponent($this->time);

        $this->nickName = new Label(20, 5);
        $this->nickName->setPosX(23);
        $this->nickName->setStyle("TextRaceChat");
        $this->nickName->setTextSize(1);
        $this->nickName->setTextColor("fff");
        $this->nickName->setText($player->nickName);

        $this->addComponent($this->nickName);

        // gauge
        $this->gauge = new Gauge(40, 6);
        $this->gauge->setPosition(3, 0);

        $divGrad = 1.0;
        if ($totalCps > 1) {
            $divGrad = 1.0 / $totalCps;
        }

        $ratio = ($data->cpIndex + 1) * $divGrad;
        if ($ratio >= 1.0) {
            $ratio = 1.0;
        }

        try {
            $index = intval($ratio * 10);
            $this->gauge->setColorize($colors[$index]);
        } catch (\Exception $e) {
            $this->gauge->setColorize("3af");
        }

        $this->gauge->setRatio($ratio);
        $this->gauge->setGrading(1);
        $this->addComponent($this->gauge);
        $this->sizeX = 54;
        $this->sizeY = 4.25;
    }

}


