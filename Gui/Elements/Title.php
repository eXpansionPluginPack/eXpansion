<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class Title extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $config;

    public function __construct($sizeX, $sizeY) {
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        $this->config = $config;

        $this->bg = new TitleBackGround($sizeX, $sizeY);
        $this->bg->setPosY(-2);
        
        $this->addComponent($this->bg);
        
        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->label->setAlign("Left", "buttom");
        $this->addComponent($this->label);
        
        $this->setSize($sizeX, $sizeY);
    }

    public function onResize($oldX, $oldY) {
        $this->bg->setSize($this->getSizeX() , $this->getSizeY());
        $this->label->setSize($this->getSizeX() , $this->getSizeY());
    }

    public function destroy() {
        $this->config = null;
    }
    
    public function setText($text){
        $this->label->setText($text);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

}

?>
