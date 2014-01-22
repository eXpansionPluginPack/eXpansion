<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

class AdminPanel extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

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
    
    protected function onConstruct() {
        parent::onConstruct();

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
        $script->setParam('isMinimized', 'True');
        $script->setParam('autoCloseTimeout', '7500');
        $script->setParam('posXMin',-50);
        $script->setParam('posX', -50);
        $script->setParam('posXMax', -4);
        $this->registerScript($script);
        
        $this->actionEndRound = $this->createAction(array($this, 'actions'), "forceEndRound");
        $this->actionCancelVote = $this->createAction(array($this, 'actions'), "cancelVote");
        $this->actionSkip = $this->createAction(array($this, 'actions'), "nextMap");
        $this->actionRestart = $this->createAction(array($this, 'actions'), "restartMap");
        
        $this->setScriptEvents(true);
        $this->setAlign("left", "top");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad(60, 10);
        $this->_mainWindow->setId("MainWindow");
        $this->_mainWindow->setStyle("Bgs1InRace");
        $this->_mainWindow->setSubStyle("BgList");
        $this->_mainWindow->setAlign("left", "center");
        $this->_windowFrame->addComponent($this->_mainWindow);

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setAlign("left", "top");
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
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("Icons128x128_1");
        $this->_minButton->setSubStyle("ProfileAdvanced");
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setAlign("left", "bottom");

        $this->_windowFrame->addComponent($this->_minButton);

        $this->setName("Admin Panel");
        $this->setDisableAxis("x");
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(60, 12);
        $this->_mainWindow->setSize(60, 6);
        $this->_minButton->setPosition(60 - 6, -2.5);
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

    function onShow() {
        parent::onShow();
        $this->btnEndRound->setVisibility(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($this->getRecipient(), 'map_endRound'));
        $this->btnCancelVote->setVisibility(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($this->getRecipient(), 'cancel_vote'));   
    }

    function destroy() {   
        $this->clearComponents();
        parent::destroy();
    }
}

?>
