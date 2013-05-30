<?php
namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLive\Gui\ActionHandler;

class Ratiobutton extends \ManiaLive\Gui\Control
{
	private $label;
	private $button;
        private $active = false;
        private $textWidth;
        private $action;
        private $buttonac;
        
        function __construct($sizeX=3, $sizeY=3, $textWidth=25)
	{
                $this->textWidth = $textWidth;
                $this->action = $this->createAction(array($this, 'toggleActive'));
                $config = Config::getInstance();
		// $this->button= new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
                $this->buttonac = new \ManiaLib\Gui\Elements\Label(4,4);                
                $this->buttonac->setAlign('center', 'center');                                             
                $this->addComponent($this->buttonac);
                
                $this->button = new \ManiaLib\Gui\Elements\Label(4,4);
                $this->button->setAlign('center', 'center');                
                $this->button->setAction($this->action);
                $this->button->setScriptEvents(true);                
                $this->button->setText("$000〇");
                $this->addComponent($this->button);
                
                
                $this->label = new \ManiaLib\Gui\Elements\Label($textWidth, 4);
		$this->label->setAlign('left', 'center');
                $this->label->setTextSize(1);
		//$this->label->setStyle("TextCardInfoSmall");		                
		$this->addComponent($this->label);
                
		$this->setSize($sizeX+$textWidth, $sizeY);
	}
	
	protected function onResize($oldX, $oldY)
	{            
                $this->button->setSize($this->sizeX - $this->textWidth, $this->sizeY);
                $this->button->setPosition(0,-0.5);
		$this->label->setSize($this->textWidth, $this->sizeY);
                $this->label->setPosition($this->sizeX-$this->textWidth+1, 0);
	}
	
	function onDraw()
	{
            $config = Config::getInstance();
        
            if ($this->active) {
                //$this->button->setImage($config->ratiobuttonActive);                
                $this->buttonac->setText("$000๏");
            } else {
            //    $this->button->setImage($config->ratiobutton);                
                $this->buttonac->setText(" ");
            }
	}
	
        function setStatus($boolean) {
            $this->active = $boolean;            
        }
        
        function getStatus() {
            return $this->active;
        }
        
	function getText()
	{
		return $this->label->getText();
	}
	
	function setText($text)
	{
		$this->label->setText('$222'.$text);
	}
	
        function toggleActive($login) {
            $this->active = !$this->active;            
            $this->redraw();
        }
        
	function setAction($action)
	{
		$this->button->setAction($action);           
	}

        
}

?>