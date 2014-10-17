<?php

namespace ManiaLivePlugins\eXpansion\Faq;

use DirectoryIterator;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWidget;
use ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWindow;

class Faq extends ExpPlugin {

    private $msg_admin_redirect, $msg_admin_info;
    public static $availableLanguages = array();

    public function exp_onLoad() {
        //$this->enableDedicatedEvents();
        $this->msg_admin_redirect = exp_getMessage('Notice: a help page is displayed by an admin!');
        $this->msg_admin_info = exp_getMessage('Notice: Displaying a help page "%1$s" to "%2$s"');
        $this->setPublicMethod("showFaq");
                
        $langs = new DirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . "Topics");

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
        FaqWindow::$mainPlugin = $this;
        FaqWidget::$mainPlugin = $this;
	
        /*$window = FaqWidget::Create(null);
        $window->setSize(7, 5);
	$window->setDisableAxis("x");
        $window->show();*/
      
    }

    public function showFaq($login, $topic = "toc", $recipient = null) {

        $showTo = $login;
        if (AdminGroups::hasPermission($login, Permission::game_settings)) {
            if (!empty($recipient)) {
                if (array_key_exists($recipient, $this->storage->players)) {
                    $showTo = $recipient;
                    $this->exp_chatSendServerMessage($this->msg_admin_redirect, $showTo);
                    $this->exp_chatSendServerMessage($this->msg_admin_info, $login, array($showTo, $topic));
                }
            }
        }
        $player = $this->storage->getPlayerObject($login);
        $window = FaqWindow::Create($showTo, true);
        $window->setLanguage($player->language);
        $window->setTopic($topic);
        $window->setSize(160, 90);
        $window->show();
    }

    function exp_onUnload() {
	FaqWindow::EraseAll();
	FaqWidget::EraseAll();
    }
}

?>
