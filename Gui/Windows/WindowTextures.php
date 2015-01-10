<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
abstract class WindowTextures extends \ManiaLive\Gui\Window
{

	protected $_mainWindow;

	/**
	 * @var \ManiaLive\Gui\Controls\Frame
	 */
	protected $mainFrame;

	protected $_closebutton;

	protected $_closeAction;

	protected $_showCoords = 'False';

	protected $_windowFrame;

	private $_titlebar, $_bg, $_topleft, $_topright, $_left, $_right, $_bottomleft, $_bottomcenter, $_bottomright;

	private $script;

	private $_scripts = array();

	private $dDeclares = "";

	private $scriptLib = "";

	private $wLoop = "";

	private $dIndex = 0;

	private $_name = "window";

	private $dicoMessages = array();

	private $calledScripts = array();

	private $xml;

	private $element = 6;

	protected function onConstruct()
	{
		parent::onConstruct();
		$config = Config::getInstance();
		$baseUrl = trim($config->uiTextureBase, "/");
		$windowUrl = $baseUrl . '/window/';
		$closeUrl = $baseUrl . '/closebutton/';

		$this->_closeAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'closeWindow'));

		$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WindowScript");

		//$lib = new \ManiaLivePlugins\eXpansion\Gui\Script_libraries\Animation();
		//$this->registerScript($lib);

		$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
		$this->_windowFrame->setPosZ(-0.1);
		$this->_windowFrame->setId("windowFrame");
		$this->_windowFrame->setScriptEvents(true);
		$this->_windowFrame->setAlign("left", "top");

		$this->_bg = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
		$this->_bg->setAlign("left", "top");
		$this->_bg->setId("MainWindow");
		$this->_bg->setImage($windowUrl . "bg.png", true);
		$this->_bg->setScriptEvents(true);
		$this->_bg->setColorize($config->windowBackgroundColor);
		$this->_windowFrame->addComponent($this->_bg);

		$this->_topleft = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
		$this->_topleft->setAlign("right", "bottom");
		$this->_topleft->setScriptEvents(true);
		$this->_topleft->setId("MainWindow");
		$this->_topleft->setImage($windowUrl . "top_left.png", true);
		$this->_topleft->setColorize($config->windowTitleBackgroundColor);
		$this->_windowFrame->addComponent($this->_topleft);

		$this->_titlebar = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->element);
		$this->_titlebar->setId("Titlebar");
		$this->_titlebar->setAlign("left", "bottom");
		$this->_titlebar->setImage($windowUrl . "top_center.png", true);
		$this->_titlebar->setScriptEvents(true);
		$this->_titlebar->setColorize($config->windowTitleBackgroundColor);
		$this->_windowFrame->addComponent($this->_titlebar);

		$this->_title = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(60, 4);
		$this->_title->setId("TitlebarText");
		$this->_title->setAlign("center", "bottom");
		$this->_title->setStyle("TextRaceMessageBig");
		$this->_title->setTextColor($config->windowTitleColor);
		$this->_title->setTextSize(1);
		//$this->_title->setTextEmboss();
		$this->_windowFrame->addComponent($this->_title);


		$this->_topright = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
		$this->_topright->setAlign("left", "bottom");
		$this->_topright->setImage($windowUrl . "top_right.png", true);
		$this->_topright->setScriptEvents(true);
		$this->_topright->setColorize($config->windowTitleBackgroundColor);
		$this->_windowFrame->addComponent($this->_topright);

// center		
		$this->_right = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
		$this->_right->setAlign("left", "top");
		$this->_right->setImage($windowUrl . "right.png", true);
		$this->_right->setScriptEvents(true);
		$this->_right->setColorize($config->windowBackgroundColor);
		$this->_right->setId("MainWindow");

		$this->_windowFrame->addComponent($this->_right);


		$this->_left = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
		$this->_left->setAlign("right", "top");
		$this->_left->setImage($windowUrl . "left.png", true);
		$this->_left->setScriptEvents(true);
		$this->_left->setId("MainWindow");
		$this->_left->setColorize($config->windowBackgroundColor);
		$this->_windowFrame->addComponent($this->_left);
// bottom



		$this->_bottomleft = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
		$this->_bottomleft->setAlign("right", "top");
		$this->_bottomleft->setImage($windowUrl . "bottom_left.png", true);
		$this->_bottomleft->setScriptEvents(true);
		$this->_bottomleft->setColorize($config->windowBackgroundColor);
		$this->_windowFrame->addComponent($this->_bottomleft);

		$this->_bottomcenter = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
		$this->_bottomcenter->setAlign("left", "top");
		$this->_bottomcenter->setImage($windowUrl . "bottom_center.png", true);
		$this->_bottomcenter->setScriptEvents(true);
		$this->_bottomcenter->setId("MainWindow");
		$this->_bottomcenter->setColorize($config->windowBackgroundColor);
		$this->_windowFrame->addComponent($this->_bottomcenter);

		$this->_bottomright = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
		$this->_bottomright->setAlign("left", "top");
		$this->_bottomright->setImage($windowUrl . "bottom_right.png", true);
		$this->_bottomright->setScriptEvents(true);
		$this->_bottomright->setColorize($config->windowBackgroundColor);
		$this->_windowFrame->addComponent($this->_bottomright);


		$this->_closebutton = new \ManiaLib\Gui\Elements\Quad(4, 4);
		$this->_closebutton->setId("Close");
		$this->_closebutton->setAlign('right', 'bottom');
		$this->_closebutton->setImage($closeUrl . "normal.png", true);
		$this->_closebutton->setImageFocus($closeUrl . "focus.png", true);
		$this->_closebutton->setScriptEvents(true);
		$this->_windowFrame->addComponent($this->_closebutton);


		$this->mainFrame = new \ManiaLive\Gui\Controls\Frame();
		$this->mainFrame->setPosY(-7);
		$this->_windowFrame->addComponent($this->mainFrame);

		$this->addComponent($this->_windowFrame);


		$this->xml = new \ManiaLive\Gui\Elements\Xml();
	}

	function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$element = $this->element;
		$x = $this->sizeX;
		$y = $this->sizeY;

		$this->_windowFrame->setSize($x, $y);

		$this->_bg->setSize($x, $y);
		$this->_bg->setPosition(0, 0);

		$this->_titlebar->setSize($x, $element);
		$this->_titlebar->setPosition(0, 0);

		$this->_topleft->setSize($element, $element);
		$this->_topleft->setPosition(0, 0);

		$this->_topright->setSize($element, $element);
		$this->_topright->setPosition($x, 0);

		$this->_right->setSize($element, $y);
		$this->_right->setPosition($x, 0);

		$this->_left->setSize($element, $y);
		$this->_left->setPosition(0, 0);

		$this->_bottomleft->setSize($element, $element);
		$this->_bottomleft->setPosition(0, -$y);

		$this->_bottomcenter->setSize($x, $element);
		$this->_bottomcenter->setPosition(0, -$y);

		$this->_bottomright->setSize($element, $element);
		$this->_bottomright->setPosition($x, -$y);

		$this->_title->setPosition($x / 2, 1);
		$this->_closebutton->setPosition($x + 3, 0.5);
	}

	private function detectElements($components)
	{
		$buttonScript = null;
		foreach ($components as $index => $component) {
			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel) {
				$this->dicoMessages[$component->getTextid()] = $component->getMessages();
			}

			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
				$this->addScriptToMain($component->getScript());
			}

			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer) {
				$script = $component->getScript();

				$isset = !array_key_exists($script->getRelPath(), $this->calledScripts);

				if ($isset) {
					$this->addScriptToLib($script->getlibScript($this, $component));
				}

				if ($isset || $script->multiply()) {
					$this->calledScripts[$script->getRelPath()] = $script;

					$dec = $script->getDeclarationScript($this, $component);
					$this->addScriptToMain($dec);
					$this->addScriptToWhile($script->getWhileLoopScript($this, $component));
				}
			}

			if ($component instanceof \ManiaLive\Gui\Container) {
				$this->detectElements($component->getComponents());
			}
		}
	}

	protected function onDraw()
	{
		parent::onDraw();

		$this->nbButton = 0;
		$this->dIndex = 0;
		$this->dDeclares = "";
		$this->scriptLib = "";
		$this->wLoop = "";
		$this->calledScripts = array();

		$this->detectElements($this->getComponents());

		foreach ($this->calledScripts as $script) {
			$this->addScriptToMain($script->getEndScript($this));
			$script->reset();
		}

		foreach ($this->_scripts as $script) {
			$this->addScriptToMain($script->getDeclarationScript($this, $this));
			$this->addScriptToLib($script->getlibScript($this, $this));
			$this->addScriptToWhile($script->getWhileLoopScript($this, $this));
		}

		$this->calledScripts = array();

		$this->script->setParam("name", $this->_name);
		$this->script->setParam("dDeclares", $this->dDeclares);
		$this->script->setParam("scriptLib", $this->scriptLib);
		$this->script->setParam("wLoop", $this->wLoop);
		$this->script->setParam("closeAction", $this->_closeAction);
		$this->script->setParam("disableAnimations", Config::getInstance()->disableAnimations ? "True" : "False");
		$this->script->setParam("version", \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
		$reset = "False";
		if (DEBUG)
			$reset = "True";

		$this->script->setParam("forceReset", $reset);


		$this->removeComponent($this->xml);
		$this->xml->setContent($this->script->getDeclarationScript($this, $this->xml));

		$this->addComponent($this->xml);

		$dico = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dico($this->dicoMessages);
		\ManiaLive\Gui\Manialinks::appendXML($dico->getXml());
	}

	function setTitle($text, $parameter = "")
	{
		$this->_name = $text;
		$this->_title->setText($text . " " . $parameter);
		// $this->_title2->setText($text . " " . $parameter);
	}

	function closeWindow()
	{
		$this->erase($this->getRecipient());
	}

	function addScriptToMain($script)
	{
		$this->dDeclares .= $script;
	}

	function addScriptToWhile($script)
	{
		$this->wLoop .= $script;
	}

	function addScriptToLib($script)
	{
		$this->scriptLib .= $script;
	}

	function destroy()
	{
		\ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->_closeAction);
		$this->_windowFrame->clearComponents();
		$this->_windowFrame->destroy();
		$this->mainFrame->destroy();

		$this->destroyComponents();
		$this->_closeAction = null;

		parent::destroy();
		
// echo "window: #";
		foreach ($this as $index => $value) {
			if (\is_object($value)) {

				if ($value instanceof \ManiaLive\Gui\Containable || $value instanceof \ManiaLive\Gui\Container) {
	//			echo "!";
					$value->destroyComponents();
					$value->destroy();
					unset($this->$index);
					continue;
				}
				if ($value instanceof \ManiaLive\Gui\Control) {
	//				echo "*";
					$value->destroy();
					unset($this->$index);
					continue;
				}

				unset($this->$index);
			}
			else {
	//			echo ".";
				unset($this->$index);
			}
		}
	//	echo "\n";
	}

	/**
	 * Registers a script to widget instance
	 * @param \ManiaLivePlugins\eXpansion\Gui\Structures\Script $script
	 */
	public function registerScript(\ManiaLivePlugins\eXpansion\Gui\Structures\Script $script)
	{
		$this->_scripts[] = $script;
	}

}

?>
