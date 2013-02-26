<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

require_once(__DIR__ . "/gbxdatafetcher.inc.php");

class Additem extends \ManiaLive\Gui\Control {

    private $bg;
    private $mapNick;
    private $addButton;
    private $label;
    private $time;
    private $addMapAction;
    private $frame;

    function __construct($indexNumber, $filename, $controller) {
        $sizeX = 120;
        $sizeY = 4;
        $this->addMapAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'addMap'), $filename);

        $gbx = new \GBXChallMapFetcher(true, false, false);
        try {
            $gbx->processFile($filename);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($gbx->name);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $this->mapNick = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->mapNick->setAlign('left', 'center');
        $this->mapNick->setText($gbx->authorNick);
        $this->mapNick->setScale(0.8);
        $this->frame->addComponent($this->mapNick);

        $this->time = new \ManiaLib\Gui\Elements\Label(16, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);
        $this->time->setText(\ManiaLive\Utilities\Time::fromTM($gbx->authorTime));
        $this->frame->addComponent($this->time);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);


        $this->addButton = new MyButton(16, 6);
        $this->addButton->setText(__("Add map"));
        $this->addButton->setAction($this->addMapAction);
        $this->addButton->setScale(0.6);
        $this->frame->addComponent($this->addButton);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY) {
        //  $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->frame->setSize($this->sizeX, $this->sizeY);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }

    function onDraw() {
        
    }

    function destroy() {
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->chooseNextMap);
        parent::destroy();
    }   
}
?>

