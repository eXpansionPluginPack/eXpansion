<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Controls;


use ManiaLib\Gui\Elements\Label;
use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\Gui\Elements\Gauge;

class CpItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $gauge;
    protected $nickName;
    protected $time;
    protected $cpIndex;

    /**
     * CpItem constructor.
     * @param int $index
     * @param int[] $time
     */
    public function __construct($index, $player, $time, $totalCps)
    {
        $colors = array("3AF", "3AF", "3BD", "3BD", "3CA", "3C8", "3D7", "3D5", "3E3", "3E2", "3F0");

        $this->cpIndex = new Label(5, 5);
        $this->cpIndex->setPosX(-1);
        $this->cpIndex->setText(($index + 1) . ".");
        $this->addComponent($this->cpIndex);

        $this->cpIndex = new Label(5, 5);
        $this->cpIndex->setPosX(3);
        end($time);
        $cpIndex = key($time);

        $this->cpIndex->setText("CP-" . ($cpIndex + 1));
        $this->cpIndex->setTextColor("f90");
        $this->addComponent($this->cpIndex);

        $this->time = new Label(10, 5);
        $this->time->setPosX(10);
        $this->time->setText(Time::fromTM(end($time)));
        $this->time->setTextColor("3af");
        $this->addComponent($this->time);

        $this->nickName = new Label(20, 5);
        $this->nickName->setPosX(22);
        $this->nickName->setText($player->nickName);
        $this->addComponent($this->nickName);

        // gauge
        $this->gauge = new Gauge(40, 6);
        $this->gauge->setPosY(0);

        $divGrad = 1.0;
        if ($totalCps > 1) {
            $divGrad = 1.0 / $totalCps;
        }

        $ratio = ($cpIndex + 1) * $divGrad;
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
        $this->sizeY = 5;

    }

}


