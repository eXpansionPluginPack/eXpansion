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
    private $frame;

    function __construct($indexNumber, $filename, $controller) {
        $sizeX = 120;
        $sizeY = 4;
        $this->saveAction = $this->createAction(array($controller, 'saveSettings'), $filename);
        $this->loadAction = $this->createAction(array($controller, 'loadSettings'), $filename);
        
        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        if ($indexNumber % 2 == 0) {
            $this->bg->setBgcolor('fff4');
        } else {
            $this->bg->setBgcolor('7774');
        }
        $this->bg->setScriptEvents(true);
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

        $this->label = new \ManiaLib\Gui\Elements\Label(90, 4);
        $this->label->setAlign('left', 'center');
        $file = explode('/', $filename);
        $this->label->setText(utf8_encode(end($file)));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);


        $this->loadButton = new MyButton(16, 6);
        $this->loadButton->setText(__("Load"));
        $this->loadButton->setAction($this->loadAction);
        $this->loadButton->setScale(0.6);
        $this->frame->addComponent($this->loadButton);

        $this->saveButton = new MyButton(16, 6);
        $this->saveButton->setText(__("Save"));
        $this->saveButton->setAction($this->saveAction);
        $this->saveButton->setScale(0.6);
        $this->frame->addComponent($this->saveButton);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function onDraw() {
        
    }

    function destroy() {
        $this->saveButton->destroy();
        $this->loadButton->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

