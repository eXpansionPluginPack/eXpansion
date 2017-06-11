<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;
use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class WidgetBackGround extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $bgborder;

    /** @var Config */
    protected $config;

    public function __construct($sizeX, $sizeY)
    {
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        $this->config = $config;

        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setOpacity($config->style_widget_bgOpacity);
        $this->bg->setBgcolor($config->style_widget_bgColorize);

        $this->bg->setPosition($config->style_widget_bgXOffset, $config->style_widget_bgYOffset);
        $this->addComponent($this->bg);


        $this->setSize($sizeX, $sizeY);
    }

    public function onResize($oldX, $oldY)
    {
        $this->bg->setSize(
            $this->getSizeX(),
            $this->getSizeY()
        );

    }

    public function setAction($action)
    {
        $this->bg->setAction($action);
        $this->bg->setBgcolorFocus($this->config->style_widget_title_bgColorize);
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy()
    {
        $this->config = null;
    }

    public function setOpacity($opacity)
    {
        $this->bg->setOpacity($opacity);
    }

    public function setHidden($hidden)
    {
        $this->bg->setHidden($hidden);
    }

    public function setId($id)
    {
        $this->bg->setId($id);
    }
}
