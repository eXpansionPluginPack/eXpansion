<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

class AdminPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget {

    protected $_windowFrame;
    protected $_mainWindow;
    protected $_minButton;
    protected $servername;
    protected $btnEndRound;
    protected $btnCancelVote;
    protected $btnSkip;
    protected $btnRestart;
    private $actionEndRound;
    private $actionCancelVote;
    private $actionSkip;
    private $actionRestart;
    public static $mainPlugin;

    protected function exp_onBeginConstruct() {
	parent::exp_onBeginConstruct();
	$this->setName("Admin Panel");
    }

    protected function exp_onSettingsLoaded() {
	parent::exp_onSettingsLoaded();
	$script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
	$script->setParam('isMinimized', 'True');
	$script->setParam('autoCloseTimeout', $this->getParameter('autoCloseTimeout'));
	$script->setParam('posXMin', -32);
	$script->setParam('posX', -32);
	$script->setParam('posXMax', -6);
	$this->registerScript($script);

	$this->actionEndRound = $this->createAction(array($this, 'actions'), "forceEndRound");
	$this->actionCancelVote = $this->createAction(array($this, 'actions'), "cancelVote");
	$this->actionSkip = $this->createAction(array($this, 'actions'), "nextMap");
	$this->actionRestart = $this->createAction(array($this, 'actions'), "restartMap");

	$this->setScriptEvents(true);

	$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
	$this->_windowFrame->setId("Frame");
	$this->_windowFrame->setScriptEvents(true);
	$this->_windowFrame->setPosY(-3);
	$this->addComponent($this->_windowFrame);

	$this->_mainWindow = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(60, 10);
	$this->_mainWindow->setId("MainWindow");
	$this->_windowFrame->addComponent($this->_mainWindow);

	$frame = new \ManiaLive\Gui\Controls\Frame();
	$frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$frame->setPosition(6, 0);

	$this->btnEndRound = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(7, 7);
	$this->btnEndRound->setAction($this->actionEndRound);
	$this->btnEndRound->setIcon("Icons128x32_1", "RT_Rounds");
	$this->btnEndRound->setDescription("Force end round");
	$frame->addComponent($this->btnEndRound);


	$this->btnCancelVote = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(7, 7);
	$this->btnCancelVote->setAction($this->actionCancelVote);
	$this->btnCancelVote->setIcon("UIConstructionSimple_Buttons", "Add");
	$this->btnCancelVote->setDescription('Cancel the vote');
	$frame->addComponent($this->btnCancelVote);

	$this->btnRestart = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(7, 7);
	$this->btnRestart->setAction($this->actionRestart);
	$this->btnRestart->setIcon("Icons128x32_1", "RT_Laps");
	$this->btnRestart->setDescription('Restarts the map');
	$frame->addComponent($this->btnRestart);

	$this->btnSkip = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(7, 7);
	$this->btnSkip->setAction($this->actionSkip);
	$this->btnSkip->setIcon("UIConstructionSimple_Buttons", "Right");
	$this->btnSkip->setDescription("Skips the map");
	$frame->addComponent($this->btnSkip);

	$this->_windowFrame->addComponent($frame);

	$this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$this->_minButton->setAlign("left", "center");
	$this->_minButton->setId("minimizeButton");
	$this->_minButton->setStyle("Icons128x128_1");
	$this->_minButton->setSubStyle("ProfileAdvanced");
	$this->_minButton->setScriptEvents(true);


	$this->_windowFrame->addComponent($this->_minButton);

	$this->setDisableAxis("x");
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->_windowFrame->setSize($this->getSizeX(), 12);
	$this->_mainWindow->setSize($this->getSizeX(), 7);
	$this->_minButton->setPosition($this->getSizeX() - 4, 0);
    }

    function actions($login, $action) {
	try {
	    switch ($action) {
		case "forceEndRound":
		    self::$mainPlugin->endRound($login);
		    break;
		case "cancelVote":
		    self::$mainPlugin->cancelVote($login);
		    break;
		case "nextMap":
		    self::$mainPlugin->skipMap($login);
		    break;
		case "restartMap":
		    self::$mainPlugin->restartMap($login);
		    break;
	    }
	} catch (\Exception $e) {
	    echo 'Notice: ' . $e->getMessage();
	}
    }

    function onDraw() {
	parent::onDraw();
	$this->btnEndRound->setVisibility(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($this->getRecipient(), 'map_endRound'));
	$this->btnCancelVote->setVisibility(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($this->getRecipient(), 'cancel_vote'));
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
