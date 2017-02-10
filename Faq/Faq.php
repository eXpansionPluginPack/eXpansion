<?php

namespace ManiaLivePlugins\eXpansion\Faq;

use DirectoryIterator;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWidget;
use ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWindow;

class Faq extends ExpPlugin
{

    private $msg_admin_redirect;
    private $msg_admin_info;
    public static $availableLanguages = array();

    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
        $this->msg_admin_redirect = eXpGetMessage('Notice: a help page is displayed by an admin!');
        $this->msg_admin_info = eXpGetMessage('Notice: Displaying a help page "%1$s" to "%2$s"');
        $this->setPublicMethod("showFaq");

        $langs = new DirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . "Topics");
        foreach ($langs as $lang) {
            if ($lang->isDot()) {
                continue;
            }
            if ($lang->isDir()) {
                if (is_file(
                    $lang->getPath() . DIRECTORY_SEPARATOR . $lang->getFilename() . DIRECTORY_SEPARATOR . "toc.txt"
                )) {
                    self::$availableLanguages[] = $lang->getFilename();
                }
            }
        }
    }


    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {
        if ($text == "/help") {
            $this->showFaq($login);
        }
    }

    public function eXpOnReady()
    {
        $this->registerChatCommand("faq", "showFaq", 0, true);
        $this->registerChatCommand("faq", "showFaq", 1, true);
        $this->registerChatCommand("faq", "showFaq", 2, true);
        FaqWindow::$mainPlugin = $this;
        FaqWidget::$mainPlugin = $this;
    }

    public function showFaq($login, $topic = "toc", $recipient = null)
    {
        FaqWindow::Erase($login);
        $topic = str_replace(".md", "", $topic);
        $showTo = $login;
        if (AdminGroups::hasPermission($login, Permission::GAME_SETTINGS)) {
            if (!empty($recipient)) {
                if (array_key_exists($recipient, $this->storage->players)) {
                    $showTo = $recipient;
                    $this->eXpChatSendServerMessage($this->msg_admin_redirect, $showTo);
                    $this->eXpChatSendServerMessage($this->msg_admin_info, $login, array($showTo, $topic));
                }
            }
        }
        $player = $this->storage->getPlayerObject($login);
        /** @var FaqWindow $window */
        $window = FaqWindow::Create($showTo);
        $window->setLanguage($player->language);
        $window->setTopic($topic . ".md");
        $window->setSize(160, 90);
        $window->show();
    }

    public function eXpOnUnload()
    {
        FaqWindow::EraseAll();
        FaqWidget::EraseAll();
    }
}
