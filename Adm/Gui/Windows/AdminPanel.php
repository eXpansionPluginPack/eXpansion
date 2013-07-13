<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

class AdminPanel extends \ManiaLive\Gui\Window {

    /**
     * @var \DedicatedApi\Connection
     */
    private $connection;
    private $storage;
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $servername;
    private $btnEndRound;
    private $btnCancelVote;
    private $btnSkip;
    private $btnRestart;
    private $actionEndRound;
    private $actionCancelVote;
    private $actionSkip;
    private $actionRestart;

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();

        $dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->actionEndRound = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "forceEndRound");
        $this->actionCancelVote = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "cancelVote");
        $this->actionSkip = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "nextMap");
        $this->actionRestart = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "restartMap");


        $this->setScriptEvents(true);
        $this->setAlign("left", "top");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad(60, 10);
        $this->_mainWindow->setId("MainWindow");
        $this->_mainWindow->setStyle("Bgs1InRace");
        $this->_mainWindow->setSubStyle("BgList");
        $this->_mainWindow->setAlign("left", "center");
        $this->_windowFrame->addComponent($this->_mainWindow);

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setAlign("left", "top");
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $frame->setPosition(6, 4);

        $this->btnEndRound = new \ManiaLib\Gui\Elements\Quad(7, 7);
        $this->btnEndRound->setAction($this->actionEndRound);
        $this->btnEndRound->setStyle("UIConstructionSimple_Buttons");
        $this->btnEndRound->setSubStyle("Validate");

        $frame->addComponent($this->btnEndRound);


        $this->btnCancelVote = new \ManiaLib\Gui\Elements\Quad(7, 7);
        $this->btnCancelVote->setAction($this->actionCancelVote);
        $this->btnCancelVote->setStyle("Icons64x64_1");
        $this->btnCancelVote->setSubStyle("Check");
        $frame->addComponent($this->btnCancelVote);

        $this->btnRestart = new \ManiaLib\Gui\Elements\Quad(7, 7);
        $this->btnRestart->setAction($this->actionRestart);
        $this->btnRestart->setStyle("Icons128x128_1");
        $this->btnRestart->setSubStyle("Default");
        $frame->addComponent($this->btnRestart);

        $this->btnSkip = new \ManiaLib\Gui\Elements\Quad(7, 7);
        $this->btnSkip->setAction($this->actionSkip);
        $this->btnSkip->setStyle("UIConstructionSimple_Buttons");
        $this->btnSkip->setSubStyle("Right");
        $frame->addComponent($this->btnSkip);

        $this->_windowFrame->addComponent($frame);

        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("Icons128x128_1");
        $this->_minButton->setSubStyle("ProfileAdvanced");
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setAlign("left", "bottom");

        $this->_windowFrame->addComponent($this->_minButton);

        $this->addComponent($this->_windowFrame);

        $xml = new \ManiaLive\Gui\Elements\Xml();
        $xml->setContent('
        <timeout>0</timeout>            
        <script><!--
                     main () {
                       
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");
                        declare mainWindow <=> Page.GetFirstChild("Frame");
                        declare isMinimized = True;                                          
                        declare lastAction = Now;
                        declare autoCloseTimeout = 7500;
                        declare positionMin = -50.0;
                        declare positionMax = -4.0;
                        mainWindow.PosnX = -50.0;                        
                                              
                        while(True) {
                                
                                if (isMinimized)
                                {
                                     if (mainWindow.PosnX >= positionMin) {                                          
                                          mainWindow.PosnX -= 4;                                          
                                    }
                                }

                                if (!isMinimized)
                                {         
                                    if (Now-lastAction > autoCloseTimeout) {                                          
                                        if (mainWindow.PosnX <= positionMin) {                                                 
                                                mainWindow.PosnX -= 4;                                      
                                        } 
                                        if (mainWindow.PosnX >= positionMin)  {
                                                isMinimized = True;
                                        }
                                    }
                                    
                                    else {
                                        if ( mainWindow.PosnX <= positionMax) {                                                      
                                                  mainWindow.PosnX += 4;
                                        }                                                                                                                                             
                                    }
                                }
                                    
                                foreach (Event in PendingEvents) {                                                
                                    if (Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                           isMinimized = !isMinimized;    
                                           lastAction = Now;                                           
                                    }                                       
                                }
                                yield;                        
                        }  
                        
                }
                --></script>');
        $this->addComponent($xml);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(60, 12);
        $this->_mainWindow->setSize(60, 6);
        $this->_minButton->setPosition(60 - 6, -2.5);
    }

    function actions($login, $action) {
        try {
            $player = $this->storage->getPlayerObject($login);
            switch ($action) {
                case "forceEndRound":
                    $this->connection->$action();
                    $this->connection->chatSendServerMessage("Admin " . $player->nickName . '$z$s$fff forced the round to end');
                    break;
                case "cancelVote":
                    $this->connection->$action();
                    $this->connection->chatSendServerMessage("Admin " . $player->nickName . '$z$s$fff cancels the vote');
                    break;
                case "nextMap":
                    $this->connection->$action();
                    $this->connection->chatSendServerMessage("Admin " . $player->nickName . '$z$s$fff skipped to next map!');
                    break;
                case "restartMap":
                    $this->connection->$action();
                    $this->connection->chatSendServerMessage("Admin " . $player->nickName . '$z$s$fff restarted the map');
                    break;
            }
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage('$f00$oError! $z$s$fff' . $e->getMessage(), $login);
        }
    }

    function onShow() {
        
    }

    function destroy() {
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionCancelVote);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionEndRound);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionRestart);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionSkip);
        $this->connection = null;
        $this->storage = null;
        $this->clearComponents();
        parent::destroy();
    }

}

?>
