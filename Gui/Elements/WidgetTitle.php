<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;
use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class WidgetTitle extends \ManiaLive\Gui\Control {

    private $bg, $lbl_title;
    private $config;

    public function __construct($sizeX, $sizeY) {
        /** @var Config $config */
	$config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
	$this->config = $config;


        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY + 2);
        $this->bg->setColorize($this->config->style_widget_title_bgColorize);
        $this->bg->setOpacity($this->config->style_widget_title_bgOpacity);
        if (!empty($config->style_widget_title_bgStyle)) {
            $this->bg->setStyle($config->style_widget_title_bgStyle);
            $this->bg->setSubStyle($config->style_widget_title_bgSubStyle);
        } else {
            $this->bg->setImage($config->style_widget_bgStyle);
        }
        $this->bg->setPosition($config->style_widget_title_bgXOffset, $config->style_widget_title_bgYOffset);
	
	$this->addComponent($this->bg);


        $this->lbl_title = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->lbl_title->setTextSize($config->style_widget_title_lbSize);
        $this->lbl_title->setTextColor($config->style_widget_title_lbColor);
        $this->lbl_title->setStyle($config->style_widget_title_bgStyle);

        $this->lbl_title->setAlign("center", "center");

        $this->addComponent($this->lbl_title);
	
	$this->setSize($sizeX, $sizeY);
    }

    public function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->lbl_title->setPosition(($this->sizeX / 2), -1);
    }

    public function setAction($action) {
	$this->bg->setAction($action);
    }

    public function setText($text){
        $this->lbl_title->setText($text);
    }

    public function setOpacity($opacity){
        $this->bg->setOpacity($opacity);
    }

}

?>
