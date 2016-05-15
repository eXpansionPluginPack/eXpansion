<?php

namespace ManiaLivePlugins\eXpansion\LoadScreen\Gui\Windows;

use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Window;

class LScreen extends Window
{

    protected $frame;

    protected $xml;

    protected $quad;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setName("LoadingScreen");
        $this->quad = new Quad(320, 180);
        $this->quad->setAlign("top", "left");
        $this->quad->setId("image");
        $this->quad->setScriptEvents();
        $this->addComponent($this->quad);
        $this->setPosition(-160, 90);
        $this->setScriptEvents();
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->xml->setContent('
				<script>
				<!--
				main() {
				  declare Window <=> Page.GetFirstChild("image");
				  declare startTime = Now;
	

					while(True) {
						yield;
						if (Window.Visible) {
							if (Now > (startTime+8000)) {
								Window.Hide(); // autohide after 8 seconds
							}
									
							foreach (Event in PendingEvents) {                
								if (Event.Type == CMlEvent::Type::MouseClick || Event.Type == CMlEvent::Type::KeyPress ) {
									if (Now > (startTime+3000)) {
									Window.Hide(); // enable hide after 3 seconds, if user press key or clicks mouse
									}
								}
							}
						}
					}				
				} 
				-->
				</script>
');
        $this->addComponent($this->xml);
    }

    protected function onDraw()
    {
        $this->quad->setPosZ(70);
        parent::onDraw();
    }

    public function setImage($url)
    {
        $this->quad->setImage($url, true);
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }

}

