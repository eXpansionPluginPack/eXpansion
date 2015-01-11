<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * Description of Pager
 *
 * @author Petri
 */
class Pager extends \ManiaLivePlugins\eXpansion\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer
{

	protected $pager;

	protected $items = array();

	protected $scroll;

	protected $scrollBg, $scrollUp, $scrollDown, $barFrame;

	protected $itemSizeY = 6;

	protected $myScript;

	public function __construct()
	{
		$config = Config::getInstance();

		$this->pager = new \ManiaLive\Gui\Controls\Frame();
		$this->pager->setId("Pager");
		$this->pager->setScriptEvents();
		$this->addComponent($this->pager);

		$this->barFrame = new \ManiaLive\Gui\Controls\Frame(0, -5);
		$this->addComponent($this->barFrame);

		$this->scrollBg = new \ManiaLib\Gui\Elements\Quad(4, 40);
		$this->scrollBg->setAlign("center", "top");
		$this->scrollBg->setImage($config->getImage("scrollbar", "background.png"), true);
		$this->scrollBg->setId("ScrollBg");
		$this->scrollBg->setOpacity(0.9);
		//$this->scrollBg->setScriptEvents();
		$this->barFrame->addComponent($this->scrollBg);

		$this->scroll = new \ManiaLib\Gui\Elements\Quad(3, 15);
		$this->scroll->setAlign("center", "top");
		$this->scroll->setImage($config->getImage("scrollbar", "scrollbar.png"), true);
		$this->scroll->setId("ScrollBar");
		$this->scroll->setScriptEvents();
		$this->barFrame->addComponent($this->scroll);

		$this->scrollDown = new \ManiaLib\Gui\Elements\Quad(4, 4);
		$this->scrollDown->setAlign("left", "top");
		$this->scrollDown->setImage($config->getImage("scrollbar", "downButton_on.png"), true);
		$this->scrollDown->setImageFocus($config->getImage("scrollbar", "downButton_focus.png"), true);
		$this->scrollDown->setId("ScrollDown");
		$this->scrollDown->setScriptEvents();
		$this->barFrame->addComponent($this->scrollDown);

		$this->scrollUp = new \ManiaLib\Gui\Elements\Quad(4, 4);
		$this->scrollUp->setAlign("left", "bottom");
		$this->scrollUp->setImage($config->getImage("scrollbar", "upButton_on.png"), true);
		$this->scrollUp->setImageFocus($config->getImage("scrollbar", "upButton_focus.png"), true);
		$this->scrollUp->setId("ScrollUp");
		$this->scrollUp->setScriptEvents();
		$this->barFrame->addComponent($this->scrollUp);

		$this->myScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\Pager");
	}

	public function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);

		$this->pager->setSize($this->sizeX - 6, $this->sizeY);

		$this->myScript->setParam("pagerSizeY", $this->sizeY);

		$this->scroll->setPosition($this->sizeX - 3, 0);
		$this->scrollBg->setPosition($this->sizeX - 3, -0);
		$this->scrollBg->setSizeY($this->sizeY - 9);

		$this->scrollDown->setPosition($this->sizeX - 5, -($this->sizeY - 9));
		$this->scrollUp->setPosition($this->sizeX - 5, 0);

		foreach ($this->items as $item) {
			$scale = $item->getScale();
			if ($scale == "")
				$scale = 1;

			$item->setSizeX($this->sizeX / $scale - 4);
		}
	}

	public function setStretchContentX($value)
	{
		// do nothing xD 
	}

	public function addItem(\ManiaLib\Gui\Component $component)
	{
		$scale = $component->getScale();
		if ($scale == "")
			$scale = 1;
		$component->setSizeX($this->sizeX / $scale - 8);
		$component->setAlign("left", "top");
		if ($component->getSizeY() > 0) {
			$this->itemSizeY = $component->getSizeY();
		}
		$item = new \ManiaLive\Gui\Controls\Frame();
		$item->setAlign("left", "top");
		$item->setScriptEvents();
		$item->addComponent($component);
		$hash = spl_object_hash($item);
		$this->items[$hash] = $item;
		$this->pager->addComponent($this->items[$hash]);
	}

	public function clearItems()
	{
		if (isset($this->pager) && $this->pager != null) {
			$this->pager->destroyComponents();
			$this->items = array();
		}
	}

	public function removeItem(\ManiaLib\Gui\Component $item)
	{
		$hash = spl_object_hash($item);
		$this->pager->removeComponent($this->items[$hash]);
		$this->items[$hash]->destroy();
		unset($this->items[$hash]);
	}

	public function destroy()
	{
		if (isset($this->pager) && $this->pager != null) {
			$this->pager->destroyComponents();
			$this->pager->destroy();
			$this->items = array();
		}
		parent::destroy();
	}

	function onIsRemoved(\ManiaLive\Gui\Container $target)
	{
		parent::onIsRemoved($target);
		$this->destroy();
	}

	public function getScript()
	{
		$this->myScript->setParam("sizeY", $this->itemSizeY);

		return $this->myScript;
	}

}
