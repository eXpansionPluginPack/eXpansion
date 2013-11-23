<?php

namespace ManiaLivePlugins\eXpansion\Faq;

class Faq extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onReady() {
        $this->registerChatCommand("faq", "showFaq", 0, true);
        $this->registerChatCommand("faq", "showFaq", 1, true);    
        Gui\Windows\FaqWindow::$mainPlugin = $this;
    }

    public function showFaq($login, $topic = "toc") {
        $player = $this->storage->getPlayerObject($login);

        $window = Gui\Windows\FaqWindow::Create($login, true);
        $window->setLanguage($player->language);
        $window->setTopic($topic);
        $window->setSize(160, 90);
        $window->show();
    }

}

?>
