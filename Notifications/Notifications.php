<?php

namespace ManiaLivePlugins\eXpansion\Notifications;

use \ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets\Panel2 as NotificationPanel;
//use \ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets\Panel2 as NotifiPanel2;
use \ManiaLivePlugins\eXpansion\Notifications\Structures\Message;

class Notifications extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $publicMessages = array();
    private $personalMessages = array(array());

    function exp_onInit() {
        $this->setPublicMethod("send");
    }

    function exp_onReady() {
        $this->enableDedicatedEvents();
        // \ManiaLive\Gui\CustomUI::HideForAll(\ManiaLive\Gui\CustomUI::CHECKPOINT_LIST);

        /**
         * Redirecting The Announcements of the Admin Groups plugin
         */
        if ($this->isPluginLoaded('eXpansion\\Chat_Admin'))
            $this->callPublicMethod('eXpansion\\Chat_Admin', 'exp_activateChatRedirect', array($this, 'send'));

        if ($this->isPluginLoaded('eXpansion\\Dedimania'))
            $this->callPublicMethod('eXpansion\\Dedimania', 'exp_activateChatRedirect', array($this, 'send'));

        if ($this->isPluginLoaded('eXpansion\\LocalRecords'))
            $this->callPublicMethod('eXpansion\\LocalRecords', 'exp_activateChatRedirect', array($this, 'send'));

        if ($this->isPluginLoaded('eXpansion\\Database'))
            $this->callPublicMethod('eXpansion\\Database', 'exp_activateChatRedirect', array($this, 'send'));

        if ($this->isPluginLoaded('eXpansion\\Maps'))
            $this->callPublicMethod('eXpansion\\Database', 'exp_activateChatRedirect', array($this, 'send'));

        foreach ($this->storage->players as $login => $player)
            $this->onPlayerConnect($login, false); // force update...        
        foreach ($this->storage->spectators as $login => $player)
            $this->onPlayerConnect($login, false); // force update...        
    }

    function send($login, $message, $icon = null, $callback = null, $pluginid = null) {
        if (is_callable($callback) || $callback === null) {
            //$hash = spl_object_hash($item);
            if ($login == null)
                array_unshift($this->publicMessages, new Message($login, $icon, $message, $callback));
            else {
                if (!array_key_exists($login, $this->personalMessages)) {
                    $this->personalMessages[$login] = array();
                }
                array_unshift($this->personalMessages[$login], new Message($login, $icon, $message, $callback));
            }
            $this->publicMessages = array_slice($this->publicMessages, 0, 6, true);

            foreach ($this->personalMessages as $login => $messages) {
                $this->personalMessages[$login] = array_slice($this->personalMessages[$login], 0, 6, true);
            }

            $this->reDraw();
        } else {
            \ManiaLive\Utilities\Console::println("Notification adding failed for plugin:" . $pluginid . " callback is not valid.");
        }
    }

    function reDraw() {
        //$this->onPlayerConnect(NotificationPanel::RECIPIENT_ALL, true);
        try {
            foreach (NotificationPanel::GetAll() as $window) {
                $login = $window->getRecipient();
                $personal = array();
                if (array_key_exists($login, $this->personalMessages)) {
                    $personal = $this->personalMessages[$login];
                }

                $messages = array_merge($personal, $this->publicMessages);
                \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::sortDesc($messages, "timestamp");
                $messages = array_slice($messages, 0, 6);
                $messages = array_reverse($messages);
               
                $window->setMessages($messages);
                $window->redraw();
            }
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("error:" . $e->getMessage());
        }
    }

    function onPlayerConnect($login, $isSpectator) {
        /* $ui = \ManiaLive\Gui\CustomUI::Create($login);
          $ui->hide(\ManiaLive\Gui\CustomUI::CHECKPOINT_LIST);
         */

        NotificationPanel::Erase($login);

        if (array_key_exists($login, $this->personalMessages)) {
            unset($this->personalMessages[$login]);
        }

        $info = NotificationPanel::Create($login, true);
        $info->setSize(100, 40);
        $info->setMessages($this->publicMessages);
        $info->setPosition(40, -40);
        $info->show();

        //$info = NotifiPanel2::Create($login, true);
    }

    public function onPlayerDisconnect($login, $reason = null) {
        /*      \ManiaLive\Gui\CustomUI::Erase($login); */
        NotificationPanel::Erase($login);
    }

}

?>