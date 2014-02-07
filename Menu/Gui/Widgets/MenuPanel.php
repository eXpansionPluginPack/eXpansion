<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Menu\Gui\Controls\PanelItem;

class MenuPanel extends \ManiaLive\Gui\Window {

    private $connection;
    private $storage;
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $frame;
    public static $menuPlugin;
    private $lbl;
    private $bg;

    protected function onConstruct() {
	parent::onConstruct();
	/*    $config = Config::getInstance();

	  $dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
	  $this->connection = \Maniaplanet\DedicatedServer\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
	  $this->storage = \ManiaLive\Data\Storage::getInstance(); */

	$this->setScriptEvents(true);
	$this->setAlign("left", "top");

	$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
	$this->_windowFrame->setAlign("left", "top");
	$this->_windowFrame->setId("Frame");
	$this->_windowFrame->setScriptEvents(true);

	$this->_mainWindow = new \ManiaLib\Gui\Elements\Quad(60, 10);
	$this->_mainWindow->setId("myWindow");
	$this->_mainWindow->setStyle("Bgs1InRace");
	$this->_mainWindow->setSubStyle("BgList");
	$this->_mainWindow->setAlign("left", "top");
	$this->_mainWindow->setScriptEvents(true);
	$this->_windowFrame->addComponent($this->_mainWindow);



	$this->lbl = new \ManiaLib\Gui\Elements\Label(30, 6);
	$this->lbl->setTextSize(1);
	$this->lbl->setAlign("center", "center");
	$this->lbl->setText("Menu");
	$this->lbl->setStyle("TextStaticVerySmall");
	$this->_windowFrame->addComponent($this->lbl);

	$this->quad = new \ManiaLib\Gui\Elements\Quad(30, 8);
	$this->quad->setStyle("Bgs1InRace");
	$this->quad->setSubStyle("BgTitle3_3");
	$this->quad->setAlign("left", "center");
	$this->_windowFrame->addComponent($this->quad);


//        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(10, 10);
//        $this->_minButton->setId("minimizeButton");
//        $this->_minButton->setStyle("Icons128x128_1");
//        $this->_minButton->setSubStyle("Options");
//        $this->_minButton->setScriptEvents(true);
//        $this->_minButton->setAlign("left", "center");
//        $this->_windowFrame->addComponent($this->_minButton);

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
                        declare autoCloseTimeout = 2500;
                        declare isMouseOver = False;
                        declare positionMin = 4.0;
                        declare positionMax = -24.0;
                        mainWindow.PosnX = 4.0;
			declare Text id = "Menu";
			
			declare persistent Boolean[Text] widgetVisible;
			    if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }
			    
                        while(True) {
			 if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }   
			    if (widgetVisible[id] == True) {
				Window.Show();
			    }
				else {
			        Window.Hide();
				yield;
			    }
                                if (isMinimized)
                                {
                                     if (mainWindow.PosnX <= positionMin) {
                                          mainWindow.PosnX += 4;
                                    }
                                }

                                if (!isMinimized)
                                {
                                    if (!isMouseOver && Now-lastAction > autoCloseTimeout) {
                                        if (mainWindow.PosnX <= positionMin) {
                                                mainWindow.PosnX += 4;
                                        }
                                        if (mainWindow.PosnX >= positionMin)  {
                                                isMinimized = True;
                                        }
                                    }

                                    else {
                                        if ( mainWindow.PosnX >= positionMax) {
                                                  mainWindow.PosnX -= 4;
                                        }
                                    }
                                }

                                foreach (Event in PendingEvents) {
                                    //if (Event.Type == CMlEvent::Type::MouseOver && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                    if (Event.Type == CMlEvent::Type::MouseOver && ( Event.ControlId == "myWindow" )) {
                                           isMinimized = False;
                                           isMouseOver = True;
                                           lastAction = Now;
                                    }
                                     if (Event.Type == CMlEvent::Type::MouseOut) {
                                        isMouseOver = False;
                                    }

                                 //  if (!isMinimized && Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                 //if (!isMinimized && Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "minimizeButton" )) {                                   
                                 //       isMinimized = True;
                                 //   } 
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
	$this->_mainWindow->setSize($this->sizeX, $this->sizeY);
	$this->lbl->setPosX(0);
	$this->lbl->setPosY(1);
	$this->quad->setSizeX($this->sizeX + 10);
	$this->quad->setPosX(-8);
	$this->quad->setPosY(1);

//   $this->_minButton->setPosition(-2, -8);
    }

    function setItems(array $menuItems) {
	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setAlign("left", "top");
	$this->frame->setPosition(8, -4);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
	$login = $this->getRecipient();

	foreach ($menuItems as $menuItem) {
	    if (!$menuItem->isAdmin || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'server_admin')) {
		$item = new PanelItem($menuItem, $login);
		$this->frame->addComponent($item);
	    }
	}
	$this->_windowFrame->addComponent($this->frame);
    }

    function destroy() {
	parent::destroy();
    }

}

?>
