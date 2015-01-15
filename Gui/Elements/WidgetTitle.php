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

	protected $bg, $bg_left, $bg_right, $lbl_title;

	protected $config;

	public function __construct($sizeX, $sizeY)
	{
		/** @var Config $config */
		$config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

		$quad = new \ManiaLib\Gui\Elements\Quad();
	
		$quad->setColorize($config->style_widget_title_bgColorize);
		$quad->setOpacity($config->style_widget_title_bgOpacity);
		$quad->setPosition($config->style_widget_title_bgXOffset, $config->style_widget_title_bgYOffset);

		$this->bg_left = clone $quad;
		$this->bg_left->setImage($config->getImage("widgets", "header_left.png"), true);
		$this->addComponent($this->bg_left);

		$this->bg = clone $quad;
		$this->bg->setImage($config->getImage("widgets", "header_center.png"), true);
		$this->addComponent($this->bg);

		$this->bg_right = clone $quad;
		$this->bg_right->setImage($config->getImage("widgets", "header_right.png"), true);
		$this->bg_right->setAlign("right");
		$this->addComponent($this->bg_right);


		$this->lbl_title = new DicoLabel($sizeX, $sizeY);
		$this->lbl_title->setTextSize($config->style_widget_title_lbSize);
		$this->lbl_title->setTextColor($config->style_widget_title_lbColor);
		$this->lbl_title->setAlign("center", "center");
		$this->addComponent($this->lbl_title);

		$this->setSize($sizeX, $sizeY);
	}

	public function onResize($oldX, $oldY)
	{
		$this->bg_left->setSize(2, $this->sizeY + 1);
		$this->bg_left->setPosX(0);

		$this->bg->setSize($this->sizeX - 4, $this->sizeY + 1);
		$this->bg->setPosX(1.99);

		$this->bg_right->setSize(2, $this->sizeY + 1);
		$this->bg_right->setPosX($this->sizeX);
		
		$this->lbl_title->setSizeX($this->sizeX - 4);
		$this->lbl_title->setPosition(($this->sizeX / 2), -1.5);
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
		$this->bg_left->setOpacity($opacity);
		$this->bg_right->setOpacity($opacity);
	}

}

?>
