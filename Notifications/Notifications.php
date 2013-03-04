<?php

namespace ManiaLivePlugins\eXpansion\Notifications;

use \ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets\Panel2 as NotificationPanel;
//use \ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets\Panel2 as NotifiPanel2;
use \ManiaLivePlugins\eXpansion\Notifications\Structures\Item;

class Notifications extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $messages = array();

    function exp_onInit() {
        $this->setPublicMethod("send");  
    }

    function exp_onReady() {
        $this->enableDedicatedEvents();
          \ManiaLive\Gui\CustomUI::HideForAll(\ManiaLive\Gui\CustomUI::CHECKPOINT_LIST);
          
		/**
		 * Redirecting The Announcements of the Admin Groups plugin
		 */
		$this->callPublicMethod('eXpansion\\Chat_Admin', 'exp_activateAnnounceRedirect', array($this, 'send'));
                
                $this->callPublicMethod('eXpansion\\LocalRecords', 'exp_activateAnnounceRedirect', array($this, 'send'));
		$this->reDraw();                
    }
	
    function send($message, $icon = null, $callback = null, $pluginid = null) {
        if (is_callable($callback) || $callback === null) {
            $item = new Item($icon, $message, $callback);
            $hash = spl_object_hash($item);
            $this->messages[$hash] = $item;
            $array = array_reverse($this->messages, true);
            $array = array_slice($array, 0, 7, true);
            $this->messages = array_reverse($array, true);
            $this->reDraw();
        } else {
            \ManiaLive\Utilities\Console::println("Notification adding failed for plugin:" . $pluginid . " callback is not valid.");
        }
    }

    function reDraw() {
            $this->onPlayerConnect(NotificationPanel::RECIPIENT_ALL, true);
    }

    function onPlayerConnect($login, $isSpectator) {       
        $ui = \ManiaLive\Gui\CustomUI::Create($login);
        $ui->hide(\ManiaLive\Gui\CustomUI::CHECKPOINT_LIST);
        
       $info = NotificationPanel::Create($login, true);
        $info->setSize(100, 40);
        $info->setItems($this->messages);
        $info->setPosition(30, -60);        
        $info->show(); 
        
        //$info = NotifiPanel2::Create($login, true);
    }

    public function onPlayerDisconnect($login) {
        \ManiaLive\Gui\CustomUI::Erase($login);
        NotificationPanel::Erase($login);  
          
    }

}

?>