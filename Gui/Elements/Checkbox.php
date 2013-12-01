<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLive\Gui\ActionHandler;

class Checkbox extends \ManiaLive\Gui\Control {

    private $label;
    private $button;
    private $active = false;
    private $textWidth;
    private $action;
    private $toToggle = null;

    function __construct($sizeX = 4, $sizeY = 4, $textWidth = 25, Checkbox $toToggle = null) {
        $this->textWidth = $textWidth;
        $this->action = $this->createAction(array($this, 'toggleActive'));
        $this->toToggle = $toToggle;
        
        $config = Config::getInstance();
        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('left', 'center2');
        $this->button->setImage($config->checkbox);
        $this->button->setAction($this->action);
        $this->button->setScriptEvents(true);
        $this->addComponent($this->button); 
        
        /* $this->button = new \ManiaLib\Gui\Elements\Label(4,4);
        $this->button->setAlign('center', 'center');       
        $this->button->setBgcolor('ddd');
        $this->button->setText(' ');
        $this->button->setSize(0.8);
        $this->button->setAction($this->action);
        $this->button->setScriptEvents(true);
        $this->addComponent($this->button); */
        //「×」

        $this->label = new \ManiaLib\Gui\Elements\Label($textWidth, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        //$this->label->setStyle("TextCardInfoSmall");		                
        $this->addComponent($this->label);

        $this->setSize($sizeX + $textWidth, $sizeY);
    }
    
    public function SetIsWorking($state){
        if($state){
            if($this->button->getAction() == -1){
                $this->button->setAction($this->action);
            }
        }else{
            $this->button->setAction(-1);
        }
    }
    
    public function ToogleIsWorking(){
        if($this->button->getAction() == -1){
            $this->button->setAction($this->action);
        }else{
            $this->button->setAction(-1);
        }
    }

    protected function onResize($oldX, $oldY) {
        $this->button->setSize(3,3);
        $this->button->setPosition(0, -0.5);
        $this->label->setSize($this->textWidth, $this->sizeY);
        $this->label->setPosition($this->sizeX - $this->textWidth, 0);
    }

    function onDraw() {
        $config = Config::getInstance();

        if($this->button->getAction() == -1){
            if ($this->active) {
                $this->button->setImage($config->checkboxDisabledActive);
            } else {
               $this->button->setImage($config->checkboxDisabled);
            }
        }else{
            if ($this->active) {
                $this->button->setImage($config->checkboxActive);
            } else {
               $this->button->setImage($config->checkbox);
            }
        }
    }

    function setStatus($boolean) {
        $this->active = $boolean;
    }

    function getStatus() {
        return $this->active;
    }

    function getText() {
        return $this->label->getText();
    }

    function setText($text) {
        $this->label->setText('$222' . $text);
    }

    function toggleActive($login) {
        $this->active = !$this->active;
        if($this->toToggle != null)$this->toToggle->ToogleIsWorking($login);
        $this->redraw();
    }

    function setAction($action) {
        $this->button->setAction($action);
    }
    
    public function destroy() {
        $this->button->setAction($this->action);
        parent::destroy();
    }
    
}

?>