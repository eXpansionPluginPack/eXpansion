<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls\DediItem;

class RecordsPanel extends \ManiaLive\Gui\Window {

    /** @var \ManiaLive\Gui\Controls\Frame */
    private $frame;
    private $actionDedi = null;
    private $actionLocal = null;
    private $btnDedi;
    private $btnLocal;
    private $items = array();
    private $bg;
    private $quad;
    private $lbl;
    private $_windowFrame;

    /** @var integer */
    public static $localrecords = array();
    public static $dedirecords = array();

    const SHOW_DEDIMANIA = 0x02;
    const SHOW_LOCALRECORDS = 0x04;

    private $showpanel = self::SHOW_DEDIMANIA;

    protected function onConstruct() {
        parent::onConstruct();

        $this->setScriptEvents(true);
        $this->setAlign("left", "top");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->bg = new \ManiaLib\Gui\Elements\Quad();
        $this->bg->setStyle("Bgs1InRace");
        $this->bg->setSubStyle("BgList");
        $this->bg->setId("MainWindow");
        $this->bg->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->bg);

        $this->lbl = new \ManiaLib\Gui\Elements\Label(50, 6);
        $this->lbl->setTextSize(1);
        $this->lbl->setStyle("TextStaticVerySmall");
        $this->_windowFrame->addComponent($this->lbl);

        $this->quad = new \ManiaLib\Gui\Elements\Quad(50, 8);
        $this->quad->setStyle("Bgs1InRace");
        $this->quad->setSubStyle("BgTitle3_3");
        $this->quad->setAlign("left", "center");
        $this->_windowFrame->addComponent($this->quad);


        $pmanager = \ManiaLive\PluginHandler\PluginHandler::getInstance();

        if ($pmanager->isLoaded('Reaby\Dedimania') && $pmanager->isLoaded('eXpansion\LocalRecords')) {
            $this->actionDedi = $this->createAction(array($this, "setPanel"), self::SHOW_DEDIMANIA);
            $this->btnDedi = new myButton(28);
            $this->btnDedi->setAction($this->actionDedi);
            $this->btnDedi->setText('$fffDedimania');
            $this->btnDedi->colorize(7778);
            $this->btnDedi->setPosX(2);
            $this->btnDedi->setScale(0.6);
            // $this->_windowFrame->addComponent($this->btnDedi);

            $this->actionLocal = $this->createAction(array($this, "setPanel"), self::SHOW_LOCALRECORDS);
            $this->btnLocal = new myButton(28);
            $this->btnLocal->setAction($this->actionLocal);
            $this->btnLocal->setText('$fffLocal');
            $this->btnLocal->colorize(7778);
            $this->btnLocal->setScale(0.6);
            $this->btnLocal->setPosX(20);
            //  $this->_windowFrame->addComponent($this->btnLocal);
        }

        if ($pmanager->isLoaded('eXpansion\LocalRecords'))
            $this->showpanel = self::SHOW_LOCALRECORDS;

        if ($pmanager->isLoaded('Reaby\Dedimania2'))
            $this->showpanel = self::SHOW_DEDIMANIA;

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "top");
        $this->frame->setPosition(4, -4);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
        $this->_windowFrame->addComponent($this->frame);


        $xml = new \ManiaLive\Gui\Elements\Xml();
        $xml->setContent('
        <timeout>0</timeout>            
        <script><!--
                      main () {
                       
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");
                        declare mainWindow <=> Page.GetFirstChild("Frame");
                        declare isMinimized = False;                                          
                        declare lastAction = Now;
                        declare autoCloseTimeout = 5500;
                        declare positionMin = -40.0;
                        declare positionMax = -4.0;
                        //mainWindow.PosnX = -40.0;                        
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
                                    if (Event.Type == CMlEvent::Type::MouseOver && (Event.ControlId == "MainWindow" || Event.ControlId == "minimizeButton" )) {
                                           isMinimized = False;
                                           isMouseOver = True;
                                           lastAction = Now;
                                    }
                                    if (Event.Type == CMlEvent::Type::MouseOut) {
                                        isMouseOver = False;
                                    }
                                    
                                    if (!isMinimized && Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "MainWindow" || Event.ControlId == "minimizeButton" )) {
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
        $this->bg->setSize($this->sizeX + 16, $this->sizeY);
        $this->bg->setPosX(-16);
        $this->lbl->setPosX($this->sizeX / 2);
        $this->lbl->setPosY(1);
        $this->quad->setSizeX($this->sizeX + 22);
        $this->quad->setPosX(-16);
        $this->quad->setPosY(1);

        parent::onResize($oldX, $oldY);
    }

    function update() {
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = array();

        $this->frame->clearComponents();




        $this->lbl->setAlign("center", "center");
        if ($this->showpanel == self::SHOW_DEDIMANIA)
            $this->lbl->setText('$000Dedimania Records');
        if ($this->showpanel == self::SHOW_LOCALRECORDS)
            $this->lbl->setText('$000Local Records');


        $index = 1;

        if ($this->showpanel == self::SHOW_DEDIMANIA) {
            $this->bg->setAction($this->actionLocal);
            if (!is_array(self::$dedirecords))
                return;
            foreach (self::$dedirecords as $record) {
                if ($index > 30)
                    return;
                $this->items[] = new DediItem($index, $record, $this->getRecipient());
                $this->frame->addComponent($this->items[$index - 1]);
                $index++;
            }
        }

        if ($this->showpanel == self::SHOW_LOCALRECORDS) {
            $this->bg->setAction($this->actionDedi);
            if (!is_array(self::$localrecords))
                return;
            foreach (self::$localrecords as $record) {
                if ($index > 30)
                    return;
                $this->items[] = new Recorditem($index, $record, $this->getRecipient());
                $this->frame->addComponent($this->items[$index - 1]);
                $index++;
            }
        }
    }

    function setPanel($login, $panel) {
        $this->showpanel = $panel;
        $this->update();
        $this->redraw($this->getRecipient());
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = array();

        if ($this->btnDedi != null)
            $this->btnDedi->destroy();


        if ($this->actionLocal != null)
            $this->btnLocal->destroy();

        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
