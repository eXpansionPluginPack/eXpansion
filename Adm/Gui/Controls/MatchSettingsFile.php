<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class MatchSettingsFile extends \ManiaLive\Gui\Control {

    private $bg;
    private $mapNick;
    private $addButton;    
    private $label;
    private $time;
    private $saveAction;
    private $loadAction;
    private $frame;

    function __construct($indexNumber, $filename, $controller) {
        $sizeX = 120;
        $sizeY = 4;
        $this->saveAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'saveSettings'), $filename);
        $this->loadAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'loadSettings'), $filename);        
      
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
       
        
        $this->addButton = new MyButton(16, 6);
        $this->addButton->setText(_("Load"));
        $this->addButton->setAction($this->loadAction);
        $this->addButton->setScale(0.6);
        $this->frame->addComponent($this->addButton);
        
        $this->addButton = new MyButton(16, 6);
        $this->addButton->setText(_("Save"));
        $this->addButton->setAction($this->saveAction);
        $this->addButton->setScale(0.6);
        $this->frame->addComponent($this->addButton);
     
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
    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        ActionHandler::getInstance()->deleteAction($this->saveAction);
        ActionHandler::getInstance()->deleteAction($this->loadAction);        
        parent::onIsRemoved($target);
    }
    
    function destroy() {
        ActionHandler::getInstance()->deleteAction($this->saveAction);
        ActionHandler::getInstance()->deleteAction($this->loadAction);  
        parent::destroy();
    }
    function __destruct() {
        
    }

}
?>

