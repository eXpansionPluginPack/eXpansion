<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Flow;
use ManiaLive\Gui\Controls\Frame;
use ManiaLive\Gui\Window;
use ManiaLivePlugins\eXpansion\Gui\Config;

class Preloader extends Window
{

	protected $frame;

	private static $cache = array();

	protected function onConstruct()
	{
		parent::onConstruct();

		$this->frame = new Frame(0, 0);
		$this->frame->setAlign("center", "center");
		$this->frame->setSize(120, 40);
		$this->frame->setLayout(new Flow(240, 40));
		$this->addComponent($this->frame);

		$this->setSize(120, 40);
		$this->setPosition(0, 200);
		$this->setAlign("center", "center");
	}

	protected function onDraw()
	{
		$this->frame->clearComponents();
		foreach (self::$cache as $url) {
			$elem = new \ManiaLib\Gui\Elements\Quad(6, 6);
			$elem->setImage($url, true);
			$this->frame->addComponent($elem);
		}
		parent::onDraw();
	}

	public static function add($url)
	{
		self::$cache[md5($url)] = $url;
	}

	public static function remove($url)
	{
		$md5 = md5($url);
		if (array_key_exists($md5, self::$cache)) {
			unset(self::$cache[$md5]);
		}
	}

	function destroy()
	{
		$this->frame->clearComponents();
		$this->frame->destroy();
		$this->clearComponents();
		parent::destroy();
	}

}

?>
