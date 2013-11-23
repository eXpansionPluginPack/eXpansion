<?php

namespace ManiaLivePlugins\eXpansion\Faq;

class Faq extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $msg_admin_redirect, $msg_admin_info;

    public function exp_onLoad() {
	$this->enableDedicatedEvents();
	$this->msg_admin_redirect = exp_getMessage('Notice: a help page is displayed by an admin!');
	$this->msg_admin_info = exp_getMessage('Notice: Displaying a help page "%1$s" to "%2$s"');
    }

    public function onPlayerConnect($login, $isSpectator) {
	$window = Gui\Windows\FaqWidget::Create($login, true);
	$window->setSize(50, 20);
	$window->setPosition(-161, 68);
	$window->show();
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
	Gui\Windows\FaqWidget::Erase($login);
    }

    public function exp_onReady() {
	$this->registerChatCommand("faq", "showFaq", 0, true);
	$this->registerChatCommand("faq", "showFaq", 1, true);
	$this->registerChatCommand("faq", "showFaq", 2, true);
	Gui\Windows\FaqWindow::$mainPlugin = $this;
	Gui\Windows\FaqWidget::$mainPlugin = $this;

	foreach ($this->storage->players as $login => $player)
	    $this->onPlayerConnect($login, false);
	foreach ($this->storage->spectators as $login => $player)
	    $this->onPlayerConnect($login, true);
    }

    public function showFaq($login, $topic = "toc", $recipient = null) {

	$showTo = $login;
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin")) {
	    if (!empty($recipient)) {
		if (array_key_exists($recipient, $this->storage->players)) {
		    $showTo = $recipient;
		    $this->exp_chatSendServerMessage($this->msg_admin_redirect, $showTo);
		    $this->exp_chatSendServerMessage($this->msg_admin_info, $login, array($showTo, $topic));
		}
	    }
	}

	$window = Gui\Windows\FaqWindow::Create($showTo, true);
	$player = $this->storage->getPlayerObject($login);
	$window->setLanguage($player->language);
	$window->setTopic($topic);
	$window->setSize(160, 90);
	$window->show();
    }

}

?>
