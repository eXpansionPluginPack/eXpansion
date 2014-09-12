<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Widgets;

class Submenu extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

	private $menu, $debug, $bg;

	public $myscript;

	private $item = array();

	private $submenu = array();

	private $bgs = array();

	private $storage;

	public function addItem(&$menu, $text, $action = null, $submenuNb = false)
	{
		$nb = count($this->item);
		$this->item[$nb] = new \ManiaLivePlugins\eXpansion\Menu\Gui\Controls\PanelItem();

		if (!empty($action)) {
			$this->item[$nb]->setAction($action);
		}

		$this->item[$nb]->setText("  " . $text);

		if ($submenuNb !== false) {
			$this->item[$nb]->setId("sub_" . $submenuNb);
			$this->item[$nb]->setClass("menuitem");
			// $this->item[$nb]->setFocusAreaColor2("3afb");
		}
		else {

			$snb = false;
			foreach ($this->submenu as $subNb => $sub) {
				if ($sub === $menu) {
					$snb = $subNb;
					break;
				}
			}
			if ($snb) {
				$this->item[$nb]->setId("sub_" . $snb . "_item_" . $nb);
				$this->item[$nb]->setAction($action);
				$this->item[$nb]->setClass("subitem");
			}
			else {
				$this->item[$nb]->setId("item_" . $nb);
				$this->item[$nb]->setClass("menuitem");
			}
		}

		$menu->addComponent($this->item[$nb]);
	}

	public function addSubMenu(&$menu, $text)
	{
		$mb = count($this->submenu) + 1;
		$this->submenu[$mb] = new \ManiaLive\Gui\Controls\Frame(29.5, 5.5);
		$this->submenu[$mb]->setLayout(new \ManiaLib\Gui\Layouts\Column());
		$this->submenu[$mb]->setId("submenu_" . $mb);
		$this->submenu[$mb]->setScriptEvents();
		// add item to menu
		$this->addItem($menu, $text . " » ", null, $mb);
		// add component to menu
		$menu->addComponent($this->submenu[$mb]);

		return $this->submenu[$mb];
	}

	public function getMenu()
	{
		return $this->menu;
	}

	protected function onConstruct()
	{
		parent::onConstruct();
		$this->storage = \ManiaLive\Data\Storage::getInstance();
		$this->menu = new \ManiaLive\Gui\Controls\Frame();
		$this->menu->setLayout(new \ManiaLib\Gui\Layouts\Column());
		$this->menu->setId("Submenu");
		$this->menu->setScriptEvents();
		$this->addComponent($this->menu);

		$inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("widgetStatus");
		$inputbox->setPosition(900, 900);
		$inputbox->setScriptEvents();
		$this->addComponent($inputbox);

		$lib = new \ManiaLivePlugins\eXpansion\Gui\Script_libraries\Animation();
		$this->registerScript($lib);

		$this->myscript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Menu\Gui\Scripts");
		$this->registerScript($this->myscript);
	}

	protected function onDraw()
	{
		$storage = \ManiaLive\Data\Storage::getInstance();

		$this->item[0]->setTop();
		$this->item[count($this->item) - 1]->setBottom();

		foreach ($this->submenu as &$item) {
			$comp = $item->getComponents();
			if (empty($comp))
				continue;
			reset($comp);
			current($comp)->setTop();
			end($comp)->setBottom();
		}

		$count = count($this->submenu);
		$version = \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION;
		$this->myscript->setParam("version", $version);
		$this->myscript->setParam("name", "Submenu");

		if ($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT) {
			$this->myscript->setParam("gameMode", \ManiaLivePlugins\eXpansion\Gui\Gui::fixString($this->storage->gameInfos->scriptName));
		}
		else {
			$this->myscript->setParam("gameMode", $this->storage->gameInfos->gameMode);
		}
		$this->myscript->setParam("forceReset", $this->getBoolean(DEBUG));
		$this->myscript->setParam("count", $count);

		parent::onDraw();
	}

	protected function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
	}

	public function destroy()
	{
		unset($this->item);
		unset($this->submenu);
		unset($this->bgs);
		parent::destroy();
	}

}

?>
