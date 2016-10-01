<?php

namespace ManiaLivePlugins\eXpansion\DonatePanel;

use ManiaLivePlugins\eXpansion\DonatePanel\Gui\DonatePanelWindow;

class DonatePanel extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public static $billId = array();
    private $config;

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
        $this->config = Config::getInstance();
        DonatePanelWindow::$donatePlugin = $this;

        $cmd = $this->registerChatCommand("donate", "donate", 2, true);
        $cmd = $this->registerChatCommand("donate", "donate", 1, true);
        $cmd->help = '/donate X where X is ammount of Planets';
    }

    public function eXpOnReady()
    {
        $window = DonatePanelWindow::Create();
        $window->setDisableAxis("x");
        $window->show();
    }

    public function eXpOnUnload()
    {
        DonatePanelWindow::EraseAll();
    }

    /**
     * Donate()
     * Function provides the /donate command.
     *
     * @param mixed $login
     * @param mixed $amount
     *
     * @return void
     */
    public function donate($login, $amount = null, $someOtherPlayer = null)
    {
        $player = $this->storage->getPlayerObject($login);
        if ($amount == "help" || $amount == null) {
            $this->showHelp($login);

            return;
        }
        if (is_numeric($amount)) {
            $amount = (int)$amount;
        } else {
            $this->eXpChatSendServerMessage('#error#Donate takes one argument and it needs to be numeric.', $login);

            return;
        }

        $toLogin = $someOtherPlayer;
        if (empty($someOtherPlayer)) {
            $toLogin = $this->storage->serverLogin;
        }

        $bill = $this->eXpStartBill($login, $toLogin, $amount, 'Planets Donation', array($this, 'billSucess'));
        $bill->setErrorCallback(5, array($this, 'billFail'));
        $bill->setErrorCallback(6, array($this, 'billFail'));
        $bill->setSubject('server_donation');

    }

    public function billSucess(\ManiaLivePlugins\eXpansion\Core\types\Bill $bill)
    {
        if ($bill->getDestinationLogin() != $this->storage->serverLogin) {
            $this->eXpChatSendServerMessage('#donate#You donated #variable#' . $bill->getAmount() . '#donate# Planets to #variable#' . $toLogin . '$z$s#donate#', $bill->getSourceLogin());
        } else {
            if ($bill->getAmount() < $this->config->donateAmountForGlobalMsg) {
                $this->eXpChatSendServerMessage('#donate#You donated #variable#' . $bill->getAmount() . '#donate# Planets to server$z$s#donate#, Thank You.', $bill->getSourceLogin());
            } else {
                $fromPlayer = $this->storage->getPlayerObject($bill->getSourceLogin());
                $this->eXpChatSendServerMessage('#donate#The server recieved a donation of #variable#' . $bill->getAmount() . '#donate# Planets from #variable#' . $fromPlayer->nickName . '$z$s#donate#, Thank You.', null);
            }
        }
    }

    public function billFail(\ManiaLivePlugins\eXpansion\Core\types\Bill $bill, $state, $stateName)
    {
        if ($state == 5) { // No go
            $login = $bill->getSourceLogin();

            $this->eXpChatSendServerMessage('#error#No Planets billed.', $login);
        }

        if ($state == 6) {  // Error
            $fromPlayer = $this->storage->getPlayerObject($bill->getSourceLogin());
            $this->eXpChatSendServerMessage('#error# There was error with player #variable#' . $fromPlayer->nickName . '$z$s#error# donation.');
            $this->eXpChatSendServerMessage('#error#' . $stateName, $bill->getSourceLogin());
        }
    }
}
