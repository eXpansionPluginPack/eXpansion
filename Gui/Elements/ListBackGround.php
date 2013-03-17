<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class ListBackGround extends \ManiaLive\Gui\Control {
    
    private $bg;
    
    public function __construct($indexNumber, $sizeX, $sizeY) {
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        
        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        $this->bg->setBgcolor($config->style_list_bgColor[$indexNumber % sizeof($config->style_list_bgColor)]);
        
        if(sizeof($config->style_list_bgStyle) == sizeof($config->style_list_bgSubStyle) && sizeof($config->style_list_bgStyle)>0){
            $this->bg->setStyle($config->style_list_bgStyle[$indexNumber % sizeof($config->style_list_bgStyle)]);
            $this->bg->setSubStyle($config->style_list_bgSubStyle[$indexNumber % sizeof($config->style_list_bgSubStyle)]);
        }
        $this->bg->setPosX(-2);

        $this->addComponent($this->bg);
        $this->setSize($sizeX, $sizeY);
    }
    
    public function onResize($oldX, $oldY) {
        $this->bg->setSize($this->getSizeX()+4, $this->getSizeY());
        $this->bg->setPosX(-2);
    }
}

?>
