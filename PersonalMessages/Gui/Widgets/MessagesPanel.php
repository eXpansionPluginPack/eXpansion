<?php

namespace ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class MessagesPanel extends \ManiaLive\Gui\Window {

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $actionPlayers;
    private $actionSend;
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $frame;
    private $labelPlayer;
    private $inputboxMessage;
    private $buttonSend;
    private $status = "True";
    private $minMaxAction;

    /** @var \DedicatedApi\Structures\Player */
    private $targetPlayer = false;
    private $xml;

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();

        $this->setScriptEvents(true);
        $this->setAlign("left", "top");

        $dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->actionPlayers = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'players'));
        $this->actionSend = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'send'));

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad(100, 10);
        $this->_mainWindow->setId("MainWindow");
        $this->_mainWindow->setStyle("Bgs1InRace");
        $this->_mainWindow->setSubStyle("BgList");
        $this->_mainWindow->setAlign("left", "center");
        $this->_windowFrame->addComponent($this->_mainWindow);

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setAlign("left", "top");
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $frame->setPosition(6, 4);

        $this->labelPlayer = new \ManiaLib\Gui\Elements\Label();
        $this->labelPlayer->setAlign("left", "top");
        $this->labelPlayer->setTextColor('fff');
        $this->labelPlayer->setPosY(-2);
        $this->labelPlayer->setText("Select player");
        $this->labelPlayer->setAction($this->actionPlayers);
        $frame->addComponent($this->labelPlayer);

        $this->inputboxMessage = new \ManiaLib\Gui\Elements\Entry(70, 5);
        $this->inputboxMessage->setAlign("left", "top");
        $this->inputboxMessage->setName("message");
        $this->inputboxMessage->setScale(0.8);
        $this->inputboxMessage->setPosY(-2);
        $this->inputboxMessage->setTextColor('fff');
        $this->inputboxMessage->setScriptEvents(true);
        //$this->inputboxMessage->setAction($this->actionSend);
        $frame->addComponent($this->inputboxMessage);

        $this->buttonSend = new myButton(16, 6);
        $this->buttonSend->setAlign("left", "top");
        $this->buttonSend->setPosY(-4);
        $this->buttonSend->setText("Send");
        //$this->buttonSend->colorize("");
        $this->buttonSend->setAction($this->actionSend);
        $this->buttonSend->setScale(0.6);
        $frame->addComponent($this->buttonSend);

        $this->_windowFrame->addComponent($frame);

        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("Icons64x64_1");
        $this->_minButton->setSubStyle("Outbox");
        $this->_minButton->setScriptEvents(true);
        //$this->_minButton->setAction($this->minMaxAction);
        $this->_minButton->setAlign("left", "bottom");

        $this->_windowFrame->addComponent($this->_minButton);

        $this->addComponent($this->_windowFrame);

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->addComponent($this->xml);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(100, 6);
        $this->_mainWindow->setSize(100, 6);
        $this->_minButton->setPosition(100 - 6, -2.5);
        $this->removeComponent($this->xml);
        $this->xml->setContent('
        <timeout>0</timeout>            
        <script><!--
                     main () {
                       
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");
                        declare mainWindow <=> Page.GetFirstChild("Frame");                        
                        declare isMinimized = ' . $this->status . ';                                          
                        declare lastAction = Now;
                        declare positionMin = -90.0;
                        declare positionMax = -4.0;
                        if (isMinimized)  {
                        mainWindow.PosnX = -90.0;                        
                        }   
                        else {
                        mainWindow.PosnX = -3.9;                        
                        }
                        while(True) {
                                
                                if (isMinimized)
                                {
                                     if (mainWindow.PosnX >= positionMin) {                                          
                                          mainWindow.PosnX -= 4;                                          
                                    }
                                }

                                if (!isMinimized)
                                {         
                                     if ( mainWindow.PosnX <= positionMax) {                                                      
                                            mainWindow.PosnX += 4;
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
        $this->addComponent($this->xml);
    }

    function onShow() {
        
    }

    function sendPm($login, $target) {
        $this->targetPlayer = $target;
        $targetPlayer = $this->storage->getPlayerObject($target);
        $this->labelPlayer->setText($targetPlayer->nickName);
        \ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows\PmWindow::Erase($login);
        $this->onResize($this->sizeX, $this->sizeY);
        $this->redraw($this->getRecipient());
    }

    function players($login, $args) {
        $this->status = "False";
        $window = \ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows\PmWindow::Create($this->getRecipient());
        $window->setController($this);
        $window->setTitle(__('Select Player to send message'));
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }

    function send($login, $args) {
        try {
            $this->status = "False";
            $target = $this->targetPlayer;
            if ($target == false) {
                $this->connection->chatSendServerMessage('Select a player to send pm first by clicking!', $login);
                return;
            }
            if (empty($args['message'])) {
                $this->connection->chatSendServerMessage('Empty message!', $login);
                return;
            }


            $targetPlayer = $this->storage->getPlayerObject($target);
            $sourcePlayer = $this->storage->getPlayerObject($login);
            \ManiaLivePlugins\eXpansion\PersonalMessages\PersonalMessages::$reply[$login] = $target;
            $this->connection->chatSendServerMessage('$abcYou whisper to ' . ($targetPlayer->nickName) . '$z$s$abc: ' . $args['message'], $login);
            $this->connection->chatSendServerMessage('$abcA whisper from ' . ($sourcePlayer->nickName) . '$z$s$abc: ' . $args['message'], $target);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage('$f00$oError $z$s$fff' . $e->getMessage(), $login);
        }
        $this->inputboxMessage->setDefault("");
        $this->onResize($this->sizeX, $this->sizeY);
        $this->redraw($this->getRecipient());
    }

    function setTargetPlayer($login) {
        $this->targetPlayer = $login;
        $this->labelPlayer->setText($targetPlayer->nickName);
        $this->onResize($this->sizeX, $this->sizeY);
        $this->redraw($this->getRecipient());
    }

    function destroy() {
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionPlayers);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionSend);
        parent::destroy();
    }

}

?>
