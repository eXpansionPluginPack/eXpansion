<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Widgets;

use ManiaLivePlugins\eXpansion\ManiaExchange\Config;

class MxWidget extends \ManiaLive\Gui\Window {

    /**
     * @var \DedicatedApi\Connection
     */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $servername;
    private $btnVisit;
    private $btnAward;
    private $actionVisit;
    private $actionAward;

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();

        $dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
        
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->actionVisit = $this->createAction(array($this, 'Visit'));
        $this->actionAward = $this->createAction(array($this, 'Award'));

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

        $this->btnVisit = new \ManiaLib\Gui\Elements\Quad(6, 6);
        $this->btnVisit->setStyle("Icons64x64_1");
        $this->btnVisit->setSubStyle("TrackInfo");
        $this->btnVisit->setAction($this->actionVisit);
        $frame->addComponent($this->btnVisit);

        $this->btnAward = new \ManiaLib\Gui\Elements\Quad(6, 6);
        $this->btnAward->setStyle("Icons64x64_1");
        $this->btnAward->setSubStyle("OfficialRace");

        // $this->btnAward->setImage($config->iconAward);
        $this->btnAward->setAction($this->actionAward);
        $frame->addComponent($this->btnAward);
        $this->_windowFrame->addComponent($frame);

        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setImage($config->iconMx);
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
                        declare autoCloseTimeout = 3500;
                        declare positionMin = -25.0;
                        declare positionMax = -4.0;
                        mainWindow.PosnX = -25.0;                        
                        declare blink = True;
                        declare blinkDuration = 2000;
                        declare blinkStartTime = Now;
                        declare isMouseOver = False;
                            
                      

                        while(True) {
                              /*
                              // Blink cannot be implemented since CMlControl doesnt have opacity :(((
                              if (blink) {
                                     if (Now-blinkStartTime < blinkDuration) {
                                     declare seed =(Now-blinkStartTime)/1000;
                                     Window.O
                                     
                                    } else {
                                    blink = False;
                                    }                                        
                                } */
                                
                                if (isMinimized)
                                {
                                     if (mainWindow.PosnX >= positionMin) {                                          
                                          mainWindow.PosnX -= 4;                                          
                                    }
                                }

                            
                                if (!isMinimized)
                                {         
                                    if (!isMouseOver && Now-lastAction > autoCloseTimeout) {                                          
                                        if (mainWindow.PosnX <= positionMin) {                                                                                                 mainWindow.PosnX -= 4;                                      
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
                                    if (Event.Type == CMlEvent::Type::MouseOver && (Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                           isMinimized = False;
                                           isMouseOver = True;
                                           lastAction = Now;
                                    }
                                    if (Event.Type == CMlEvent::Type::MouseOut) {
                                        isMouseOver = False;
                                    }
                                    
                                    if (!isMinimized && Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                        isMinimized = True;
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
        $this->_windowFrame->setSize(35, 12);
        $this->_mainWindow->setSize(35, 6);
        $this->_minButton->setPosition(35 - 6, -2.5);
    }

    function Visit($login) {
        $link = "http://tm.mania-exchange.com/tracks/view/" . $this->getMXid();
        $this->connection->sendOpenLink($login, $link, 0);
    }

    function Award($login) {
        $link = "http://tm.mania-exchange.com/awards/add/" . $this->getMXid();
        $this->connection->sendOpenLink($login, $link, 0);
    }

    function getMXid() {
        $query = "http://api.mania-exchange.com/tm/tracks/" . $this->storage->currentMap->uId;

        $ch = curl_init($query);
        curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MXapi [getter] ver 0.1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);

        if ($data === false) {
            $this->connection->chatSendServerMessage(__('MX is down'), $login);
            return;
        }

        if ($status["http_code"] !== 200) {
            if ($status["http_code"] == 301) {
                $this->connection->chatSendServerMessage(__('Map not found', $login), $login);
                return;
            }

            $this->connection->chatSendServerMessage(__('MX returned http error code: %s', $login, $status["http_code"]), $login);
            return;
        }

        $json = json_decode($data);
        if ($json === false) {
            $this->connection->chatSendServerMessage(__('Map not found', $login), $login);
        }
        
        return $json[0]->TrackID;
        
    }

    function onShow() {
        
    }

    function destroy() {

        $this->clearComponents();
        parent::destroy();
    }

}

?>
