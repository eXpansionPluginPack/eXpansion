<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class TitleBackGround extends \ManiaLive\Gui\Control {

    private $bg;
    private $config;

    public function __construct($sizeX, $sizeY) {
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        $this->config = $config;

        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');


        if ($config->style_title_bgStyle != "") {
            $this->bg->setStyle($config->style_title_bgStyle);
            $this->bg->setSubStyle($config->style_title_bgSubStyle);
        } else {
            $this->bg->setBgcolor($config->style_title_bgColor);
        }
        $this->bg->setPosition($config->style_title_posXOffset, $config->style_title_posYOffset);

        $this->addComponent($this->bg);
        $this->setSize($sizeX, $sizeY);
    }

    public function onResize($oldX, $oldY) {
        $config = $this->config;
        $this->bg->setSize($this->getSizeX() + $config->style_title_sizeXOffset, $this->getSizeY() + $config->style_title_sizeYOffset);
        $this->bg->setPosX(-2);
    }

    public function destroy() {
        $this->config = null;
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

}

?>
