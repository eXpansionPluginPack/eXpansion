<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Advertising\Gui\Widgets;

/**
 * Description of WidgetAd
 *
 * @author Petri
 */
class WidgetAd extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

	/** @var ManiaLib\Gui\Elements\Quad; */
	protected $quad;

	protected function onConstruct()
	{
		parent::onConstruct();
		$this->quad = new \ManiaLib\Gui\Elements\Quad();
		$this->addComponent($this->quad);
	}

	public function setImage($image, $imageFocus)
	{
		$this->quad->setImage($image, true);
		$this->quad->setImageFocus($imageFocus, true);
	}

	public function setUrl($url)
	{
		$this->quad->setUrl($url);
	}

	public function setManialink($url)
	{
		$this->quad->setManialink($url);
	}

	public function setImageSize($sizeX, $sizeY, $preferredSize)
	{
		$wantedAspectRatio = $sizeX / $sizeY;
		$currentAspectRatio = 16 / 9;
		$x = $preferredSize * ($currentAspectRatio / $wantedAspectRatio) * $wantedAspectRatio;
		$y = $preferredSize * ($currentAspectRatio / $wantedAspectRatio);
		$this->setSize($x, $y);
		$this->quad->setSize($x, $y);
	}

}
