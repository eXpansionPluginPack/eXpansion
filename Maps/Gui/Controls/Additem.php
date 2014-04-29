<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;
use \ManiaLive\Gui\ActionHandler;

class Additem extends \ManiaLive\Gui\Control {

    private $bg;
    private $mapNick;
    private $addButton;
    private $deleteButton;
    private $label;
    private $time;
    private $addMapAction;
    private $deleteActionf;
    private $deleteAction;
    private $frame;

    function __construct($indexNumber, $filename, $controller, $gbx, $login, $sizeX) {
        $sizeY = 6;
        $this->addMapAction = $this->createAction(array($controller, 'addMap'), $filename);
	$this->deleteActionf =  ActionHandler::getInstance()->createAction(array($controller, 'deleteMap'), $filename);
        $this->deleteAction = \ManiaLivePlugins\eXpansion\Gui\Gui::createConfirm($this->deleteActionf);

        try {
            $gbx->processFile($filename);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

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

        $this->label = new \ManiaLib\Gui\Elements\Label(90, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(\ManiaLib\Utils\Formatting::stripColors($gbx->name, "fff"));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $this->mapNick = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->mapNick->setAlign('left', 'center');

        $this->mapNick->setText(\ManiaLib\Utils\Formatting::stripColors($gbx->authorNick, "fff"));
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


        $this->addButton = new MyButton(24, 5);
        $this->addButton->setText(__("Add", $login));
        $this->addButton->setAction($this->addMapAction);
        $this->addButton->setScale(0.5);
        $this->addButton->colorize("2a2");
        $this->frame->addComponent($this->addButton);

        $this->deleteButton = new MyButton(24, 5);
        $this->deleteButton->setAction($this->deleteAction);
        $this->deleteButton->setScale(0.5);
        $this->deleteButton->setText('$ff0' . __("Delete", $login));
        $this->deleteButton->colorize("222");
        $this->frame->addComponent($this->deleteButton);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        unset($gbx);
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

// manialive 3.1 override to do nothing.
    function destroy() {
        ActionHandler::getInstance()->deleteAction($this->addMapAction);
        ActionHandler::getInstance()->deleteAction($this->deleteAction);
        ActionHandler::getInstance()->deleteAction($this->deleteActionf);
    }

    /*
     * custom function to remove contents.
     */

    function erase() {
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->addButton->destroy();
        $this->deleteButton->destroy();

        $this->clearComponents();

	$this->destroy();
        parent::destroy();
    }

}
?>

