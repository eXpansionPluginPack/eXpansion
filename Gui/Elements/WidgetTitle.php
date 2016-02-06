<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * Description of ListBackGround
 *
 * @author oliverde8
 */
class WidgetTitle extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $bg, $bgStr, $lbl_title;
    protected $config;
    protected $direction = "top";

    public function __construct($sizeX, $sizeY)
    {
        /** @var Config $config */
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

        $quad = new \ManiaLib\Gui\Elements\Quad();

        $quad->setColorize($config->style_widget_title_bgColorize);
        $quad->setOpacity($config->style_widget_title_bgOpacity);
        $quad->setPosition($config->style_widget_title_bgXOffset, $config->style_widget_title_bgYOffset);

        $this->bg = clone $quad;
        $this->bg->setStyle('Bgs1InRace');
        $this->bg->setSubStyle('BgWindow4');
        $this->bg->setAlign("center", "center");
        $this->addComponent($this->bg);

        //$this->bgStr = new \ManiaLib\Gui\Elements\Quad();
        //$this->bgStr->setAlign("right", "center");
        //$this->bgStr->setImage('file://Media/Manialinks/Common/WarmUp/Structure.dds', true);
        // $this->addComponent($this->bgStr);

        $this->lbl_title = new DicoLabel($sizeX, $sizeY);
        $this->lbl_title->setTextSize($config->style_widget_title_lbSize);
        $this->lbl_title->setTextColor($config->style_widget_title_lbColor);
        $this->lbl_title->setAlign("center", "center");
        $this->lbl_title->setStyle("TextCardScores2");
        $this->addComponent($this->lbl_title);

        $this->setSize($sizeX, $sizeY);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        switch ($this->direction) {
            case "left":
                $this->bg->setSize(4, $this->sizeY);
                $this->bg->setPosition(-2, -($this->sizeY / 2));
                $this->lbl_title->setSizeY($this->sizeY - 2);
                $this->lbl_title->setPosition(-2, -($this->sizeY / 2));
                $this->lbl_title->setAttribute("rot", -90);
                break;
            case "right":
                $this->bg->setSize(4, $this->sizeY);
                $this->bg->setPosition($this->sizeX + 2, -($this->sizeY / 2));
                $this->lbl_title->setSizeY($this->sizeY - 2);
                $this->lbl_title->setPosition($this->sizeX + 2, -($this->sizeY / 2));
                $this->lbl_title->setAttribute("rot", 90);
                break;
            default:
                $this->bg->setSize($this->sizeX, 4);
                $this->bg->setPosition(($this->sizeX / 2), -1.5);
                $this->lbl_title->setSizeX($this->sizeX - 2);
                $this->lbl_title->setPosition(($this->sizeX / 2), -1.5);
                $this->lbl_title->setAttribute("rot", 0);
                break;
        }
    }

    /**
     *
     * @param string $direction, possible values "top", "left", "right", "bottom"
     */
    public function setDirection($direction = "top")
    {
        $this->direction = $direction;
        $this->onResize(0, 0);
    }

    public function setId($id)
    {
        $this->bg->setId($id);
        $this->bg->setScriptEvents();
    }

    public function setAction($action)
    {
        $this->bg->setAction($action);
    }

    public function setText($text)
    {
        $this->lbl_title->setText($text);
    }

    public function setOpacity($opacity)
    {
        $this->bg->setOpacity($opacity);
    }
}
?>
