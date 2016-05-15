<?php

namespace ManiaLivePlugins\eXpansion\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of RecItem
 *
 * @author oliverde8
 */
class Item extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $labels;
    private $bg;
    private $widths;

    function __construct($indexNumber, $login, $datas, $widths, $keys, $formaters)
    {
        $this->widths = $widths;
        $this->sizeY = 4;
        $this->bg = new ListBackGround($indexNumber, 100, 4);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize(100, 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / .8) - 5);
        $i = 0;
        foreach ($keys as $dataKey) {
            $label = new \ManiaLib\Gui\Elements\Label($scaledSizes[$i], 4);
            $label->setAlign('left', 'center');
            $label->setScale(0.8);
            $text = "";
            if ($dataKey == null) {
                $text = $indexNumber + 1;
            } else {
                if (isset($datas[$dataKey])) {
                    if (isset($formaters[$i]) && $formaters[$i] != null)
                        $text = $formaters[$i]->format($datas[$dataKey]);
                    else
                        $text = $datas[$dataKey];
                }
            }
            $label->setText($text);
            $this->frame->addComponent($label);
            $this->labels[$i] = $label;
            $i++;
        }
    }

    public function onResize($oldX, $oldY)
    {
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / .8) - 5);
        $this->bg->setSizeX($this->getSizeX() - 5);
        $i = 0;
        foreach ($scaledSizes as $sizeX) {
            $this->labels[$i]->setSizeX($sizeX);
            $i++;
        }
        $this->frame->setSizeX($this->getSizeX());
    }

    // manialive 3.1 override to do nothing.
    function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    function erase()
    {
        $this->labels = null;
        parent::destroy();
    }

}

?>
