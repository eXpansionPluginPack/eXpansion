<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class MatchSettingsFile extends \ManiaLive\Gui\Control {

    private $bg;
    private $mapNick;
    private $saveButton;
    private $loadButton;
    private $label;
    private $time;
    private $saveAction;
    private $loadAction;
    private $deleteButton;
    private $deleteAction;
    private $frame;

    function __construct($indexNumber, $filename, $controller, $login, $sizeX) {
        $sizeY = 4;
        $this->saveAction = $this->createAction(array($controller, 'saveSettings'), $filename);
        $this->loadAction = $this->createAction(array($controller, 'loadSettings'), $filename);
        $this->deleteAction = $this->createAction(array($controller, 'deleteSetting'), $filename);

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

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 4);
        $this->label->setAlign('left', 'center');
        $file = explode('/', $filename);
        $text = utf8_encode(end($file));
        $text = str_replace(".txt", "", $text);
        $this->label->setText($text);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);


        $this->loadButton = new MyButton(26, 5);
        $this->loadButton->setText(__("Load",$login));
        $this->loadButton->setAction($this->loadAction);
        $this->loadButton->setScale(0.5);
        $this->loadButton->colorize("2a2");
        $this->frame->addComponent($this->loadButton);

        $this->saveButton = new MyButton(26, 5);
        $this->saveButton->setText('$fff' . __("Save",$login));
        $this->saveButton->colorize("a22");
        $this->saveButton->setAction($this->saveAction);
        $this->saveButton->setScale(0.5);
        $this->frame->addComponent($this->saveButton);
        
        $this->deleteButton = new MyButton(26, 5);
        $this->deleteButton->setText('$ff0' . __("Delete",$login));
        $this->deleteButton->colorize("222");
        $this->deleteButton->setAction($this->deleteAction);
        $this->deleteButton->setScale(0.5);
        $this->frame->addComponent($this->deleteButton);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function onDraw() {
        
    }
// manialive 3.1 override to do nothing.
    function destroy() {
        
        
    }
    /*
     * custom function to remove contents.
     */
    function erase() {
        $this->saveButton->destroy();
        $this->loadButton->destroy();
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

