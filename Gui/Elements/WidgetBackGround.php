<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class WidgetBackGround extends \ManiaLive\Gui\Control {
    
    private $bg;
    private $config;
    
    public function __construct($sizeX, $sizeY) {
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        $this->config = $config;
        
        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        $this->bg->setBgcolor($config->style_widget_bgColor);
        
        if(!empty($config->style_widget_bgSubStyle)){
            $this->bg->setStyle($config->style_widget_bgStyle);
            $this->bg->setSubStyle($config->style_widget_bgSubStyle);
        }else{
            $this->bg->setImage($config->style_widget_bgStyle);
        }
                    
        $this->bg->setPosition($config->style_widget_bgXOffset, $config->style_widget_bgYOffset);

        $this->addComponent($this->bg);
        $this->setSize($sizeX, $sizeY);
    }
    
    public function onResize($oldX, $oldY) {
        $this->bg->setSize($this->getSizeX()+(float)$this->config->style_list_sizeXOffset, $this->getSizeY()+(float)$this->config->style_list_sizeYOffset);
    }
    
    public function setAction($action) {
        $this->bg->setAction($action);        
    }
    
    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }
    public function destroy() {
        $this->config = null;
    }
}

?>
