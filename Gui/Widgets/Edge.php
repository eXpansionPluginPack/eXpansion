<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Container;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

/**
 *
 * @author reaby
 */
class Edge extends PlainWidget
{

	private $bg;

	private $label;

	private $orientation;

	private $frame;

	private $sscript;

	private $widgetSize;

	public function onConstruct()
	{
		parent::onConstruct();
		
		$this->sscript = new Script("Gui\Scripts\EdgeScript");
		$this->sscript->setParam("orientation", "right");
		$this->registerScript($this->sscript);
		
		$sizeX = 2;
		$sizeY = 90;
		
		$this->bg = new Quad($sizeX, $sizeY);
		$this->bg->setBgcolor("ff0");
		$this->bg->setId("Edge");
		$this->bg->setAlign("center", "center");
		$this->bg->setScriptEvents();
		$this->addComponent($this->bg);
		
		$this->setSize($sizeX, $sizeY);
	}

	public function onResize($oldX, $oldY)
	{
		$this->bg->setSize($this->getSizeX(), $this->getSizeY());
		
	}

	public function destroy()
	{
		
	}

	public function setText($text)
	{
		
	}

	function onIsRemoved(Container $target)
	{
		parent::onIsRemoved($target);
		$this->destroy();
	}

	
}

?>
