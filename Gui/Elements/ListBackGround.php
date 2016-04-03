<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class ListBackGround extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $config;

    public function __construct($indexNumber, $sizeX, $sizeY)
    {
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        $this->config = $config;

        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        //   $this->bg->setBgcolor($config->style_list_bgColor[$indexNumber % sizeof($config->style_list_bgColor)]);
        $this->bg->setOpacity(0.8);

        if (sizeof($config->style_list_bgStyle) == sizeof($config->style_list_bgSubStyle) && sizeof($config->style_list_bgStyle) > 0) {
            $this->bg->setStyle($config->style_list_bgStyle[$indexNumber % sizeof($config->style_list_bgStyle)]);
            $this->bg->setSubStyle($config->style_list_bgSubStyle[$indexNumber % sizeof($config->style_list_bgSubStyle)]);
            $this->bg->setModulateColor($config->style_list_bgColor[$indexNumber % sizeof($config->style_list_bgColor)]);
        }

        $image = "even";
        if ($indexNumber % 2 == 1) $image = "odd";

        //	$this->bg->setImage($config->getImage("listitem", $image."_center.png"), true);
        $this->bg->setPosition($config->style_list_posXOffset, $config->style_list_posYOffset);

        $this->addComponent($this->bg);
        $this->setSize($sizeX, $sizeY);
    }

    public function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->getSizeX() + (float)$this->config->style_list_sizeXOffset, $this->getSizeY() + (float)$this->config->style_list_sizeYOffset);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy()
    {
        $this->config = null;
    }
}

?>
