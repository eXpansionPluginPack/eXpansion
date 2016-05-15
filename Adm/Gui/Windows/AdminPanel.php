<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use Exception;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class AdminPanel extends Widget
{
    protected $_windowFrame;
    protected $_mainWindow;
    protected $_minButton;
    protected $servername;
    protected $btnEndRound;
    protected $btnCancelVote;
    protected $btnSkip;
    protected $btnRestart;
    protected $actionEndRound;
    protected $actionCancelVote;
    protected $actionSkip;
    protected $actionRestart;
    protected $actionBalance;
    public static $mainPlugin;

    protected function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();
        $this->setName("Admin Panel");

        $this->actionEndRound = $this->createAction(array($this, 'actions'), "forceEndRound");
        $this->actionCancelVote = $this->createAction(array($this, 'actions'), "cancelVote");
        $this->actionSkip = $this->createAction(array($this, 'actions'), "nextMap");
        $this->actionRestart = $this->createAction(array($this, 'actions'), "restartMap");
        $this->actionBalance = $this->createAction(array($this, 'actions'), "balanceTeams");

        $this->setScriptEvents(true);

        $this->_windowFrame = new Frame();
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->_mainWindow = new WidgetBackGround(42, 7);
        $this->_mainWindow->setId("MainWindow");
        $this->_windowFrame->addComponent($this->_mainWindow);

        $frame = new Frame();
        $frame->setAlign("left", "top");
        $frame->setLayout(new Line());
        $frame->setPosition(3, -3);

        $this->btnEndRound = new Button(7, 7);
        $this->btnEndRound->setAction($this->actionEndRound);
        $this->btnEndRound->setIcon("Icons128x32_1", "RT_Rounds");
        $this->btnEndRound->setDescription("Force end round");
        $frame->addComponent($this->btnEndRound);


        $this->btnCancelVote = new Button(7, 7);
        $this->btnCancelVote->setAction($this->actionCancelVote);
        $this->btnCancelVote->setIcon("UIConstructionSimple_Buttons", "Add");
        $this->btnCancelVote->setDescription('Cancel the vote');
        $frame->addComponent($this->btnCancelVote);

        $this->btnRestart = new Button(7, 7);
        $this->btnRestart->setAction($this->actionRestart);
        $this->btnRestart->setIcon("Icons128x32_1", "RT_Laps");
        $this->btnRestart->setDescription('Restarts the map');
        $frame->addComponent($this->btnRestart);

        $this->btnSkip = new Button(7, 7);
        $this->btnSkip->setAction($this->actionSkip);
        $this->btnSkip->setIcon("UIConstructionSimple_Buttons", "Right");
        $this->btnSkip->setDescription("Skips the map");
        $frame->addComponent($this->btnSkip);

        $this->btnBalance = new Button(7, 7);
        $this->btnBalance->setAction($this->actionBalance);
        $this->btnBalance->setIcon("BgRaceScore2", "LadderRank");
        $this->btnBalance->setDescription("Balance Teams");
        $frame->addComponent($this->btnBalance);


        $this->_windowFrame->addComponent($frame);

        $this->_minButton = new Quad(5, 5);
        $this->_minButton->setAlign("left", "top");
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("Icons128x128_1");
        $this->_minButton->setSubStyle("ProfileAdvanced");
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setPosition(40 - 4, -1);
        $this->_windowFrame->addComponent($this->_minButton);
    }

    protected function eXpOnSettingsLoaded()
    {
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
        $script->setParam('isMinimized', 'True');
        $script->setParam('autoCloseTimeout', $this->getParameter('autoCloseTimeout'));
        $script->setParam('posXMin', -32);
        $script->setParam('posX', -32);
        $script->setParam('posXMax', -4);
        $this->registerScript($script);
    }

    public function actions($login, $action)
    {
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
                case "balanceTeams":
                    AdminGroups::getInstance()->adminCmd($login, "setTeamBalance");
                    break;
            }
        } catch (Exception $e) {
            Helper::log('[Adm/AdminPanel]' . $e->getMessage());
        }
    }

    protected function onDraw()
    {
        parent::onDraw();
        $this->btnRestart->setVisibility(AdminGroups::hasPermission($this->getRecipient(), Permission::MAP_RES));
        $this->btnSkip->setVisibility(AdminGroups::hasPermission($this->getRecipient(), Permission::MAP_SKIP));
        $this->btnEndRound->setVisibility(AdminGroups::hasPermission($this->getRecipient(), Permission::MAP_END_ROUND));
        $this->btnCancelVote->setVisibility(AdminGroups::hasPermission($this->getRecipient(), Permission::SERVER_VOTES));
        $this->btnBalance->setVisibility(AdminGroups::hasPermission($this->getRecipient(), Permission::TEAM_BALANCE));
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}
