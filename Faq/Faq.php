<?php

namespace ManiaLivePlugins\eXpansion\Faq;

class Faq extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $msg_admin_redirect, $msg_admin_info;
    public static $availableLanguages = array();

    public function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->msg_admin_redirect = exp_getMessage('Notice: a help page is displayed by an admin!');
        $this->msg_admin_info = exp_getMessage('Notice: Displaying a help page "%1$s" to "%2$s"');
        $this->setPublicMethod("showFaq");
                
        $langs = new \DirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . "Topics");

        foreach ($langs as $lang) {
            if ($lang->isDot())
                continue;
            if ($lang->isDir()) {
                if (is_file($lang->getPath() . DIRECTORY_SEPARATOR . $lang->getFilename() . DIRECTORY_SEPARATOR . "toc.txt")) {
                    self::$availableLanguages[] = $lang->getFilename();
                }
            }
        }
    }
    
    public function exp_onReady() {
        $this->registerChatCommand("faq", "showFaq", 0, true);
        $this->registerChatCommand("faq", "showFaq", 1, true);
        $this->registerChatCommand("faq", "showFaq", 2, true);
        Gui\Windows\FaqWindow::$mainPlugin = $this;
        Gui\Windows\FaqWidget::$mainPlugin = $this;
	
        $window = Gui\Windows\FaqWidget::Create(null);
        $window->setSize(7, 7);
        $window->show();
      
    }

    public function showFaq($login, $topic = "toc", $recipient = null) {

        $showTo = $login;
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::game_settings)) {
            if (!empty($recipient)) {
                if (array_key_exists($recipient, $this->storage->players)) {
                    $showTo = $recipient;
                    $this->exp_chatSendServerMessage($this->msg_admin_redirect, $showTo);
                    $this->exp_chatSendServerMessage($this->msg_admin_info, $login, array($showTo, $topic));
                }
            }
        }
        $player = $this->storage->getPlayerObject($login);
        $window = Gui\Windows\FaqWindow::Create($showTo, true);
        $window->setLanguage($player->language);
        $window->setTopic($topic);
        $window->setSize(160, 90);
        $window->show();
    }

}

?>
