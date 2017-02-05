<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Controls;

/**
 * Description of InfoLine
 *
 * @author De Cramer Oliver
 */
class InfoLine extends \ManiaLivePlugins\eXpansion\Gui\Control
{


    public function __construct($sizeY, $title, $data, $i, $sizeX = 60, $autoNewLine = true)
    {
        $posX = 33;

        $label = new \ManiaLib\Gui\Elements\Label(32, 5);
        $label->setPosY(-0.5);
        $label->setText($title);
        $this->addComponent($label);

        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($i + 1, 32, 5);
        $bg->setPosY(-2);
        $this->addComponent($bg);

        $content = new \ManiaLib\Gui\Elements\Label($sizeX, 25);
        if ($autoNewLine) {
            $content->enableAutonewline();
        }
        $content->setText($data);
        $content->setPosY(-0.5);
        $content->setPosX($posX);

        $this->addComponent($content);

        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($i, $sizeX, $sizeY);
        $bg->setPosX($posX);
        $bg->setPosY((int)($sizeY / 2 * -1));
        $this->addComponent($bg);

        $this->setSizeX(25 + $sizeX);
        $this->setSizeY($sizeY + 1);
    }
}
