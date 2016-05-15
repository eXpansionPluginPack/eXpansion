<?php

namespace ManiaLivePlugins\eXpansion\SM_CheckpointCount\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class CPPanel extends Widget
{

    private $label;

    private $cplabel;

    private $bg;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Checkpoint counter (storm)");
    }

    protected function eXpOnEndConstruct()
    {

        $this->bg = new \ManiaLib\Gui\Elements\Quad(30, 10);
        $this->bg->setStyle("EnergyBar");
        $this->bg->setSubStyle("EnergyBar");
        $this->addComponent($this->bg);
        /*
          $this->cplabel = new DicoLabel();
          $this->cplabel->setText(exp_getMessage('Checkpoints'));
          $this->cplabel->setAlign("center", "top");
          $this->addComponent($this->cplabel); */

        $this->label = new Label(16, 4);
        $this->label->setAlign("center", "top");
        $this->label->setStyle("TextRaceMessageBig");
        $this->label->setTextSize(2);
        $this->addComponent($this->label);
    }

    function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        //$this->cplabel->setPosition(($this->sizeX / 2), -1);
        $this->label->setPosition(($this->sizeX / 2), -1.5);
        $this->bg->setSize($this->sizeX, $this->sizeY);
    }

    /**
     * SetText(string $text)
     * Sets the text used in the widget
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->label->setText($text);
    }

    function destroy()
    {
        parent::destroy();
    }

}

?>
