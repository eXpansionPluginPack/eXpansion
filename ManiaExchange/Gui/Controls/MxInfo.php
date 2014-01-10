<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class MxInfo extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $time;
    private $frame;

    function __construct($indexNumber, $message, $sizeX) {
        $sizeY = 5.5;


        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        if ($indexNumber % 2 == 0) {
            $this->bg->setBgcolor('aaa4');
        } else {
            $this->bg->setBgcolor('7774');
        }
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
     
        $this->addComponent($this->frame);

        $info = new \ManiaLib\Gui\Elements\Label(120, 4);
        $info->setAlign('left', 'center');
        $info->setText('$000' . $message);
        $info->setStyle("TextCardSmallScores2");
        $info->setScriptEvents(true);
        $this->frame->addComponent($info);

        
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY + 1);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }

    // override destroy method not to destroy its contents on manialive 3.1 
    function destroy() {
        
    }

    /**
     * custom function to destroy contents when needed.
     */
    function erase() {
        if (is_object($this->queueButton)) {
            $this->queueButton->destroy();
        }
        if ($this->isAdmin) {
            $this->addButton->destroy();
        }
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

