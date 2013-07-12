<?php

namespace ManiaLivePlugins\eXpansion\DonatePanel;

use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\DonatePanel\Gui\DonatePanelWindow;

class DonatePanel extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $billId = array();
    private $enabled = true;
    private $show;
    private $config;

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->config = Config::getInstance();
        $colors = \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance();
        $colors->registerCode("donate", '$a0f');
        $colors->registerCode("error", '$f00');
        DonatePanelWindow::$donatePlugin = $this;

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);

        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);

        $cmd = $this->registerChatCommand("donate", "donate", 1, true);
		$cmd->help = '/donate X where X is ammount of Planets';
    }

    function onUnload() {
        parent::onUnload();
        DonatePanelWindow::EraseAll();
    }

    /**
     * onBillUpdated()
     * Function called when a bill is updated.
     *
     * @param mixed $billId
     * @param mixed $state
     * @param mixed $stateName
     * @param mixed $transactionId
     * @return
     */
    function onBillUpdated($billId, $state, $stateName, $transactionId) {

        if (count(self::$billId) == 0)
            return;

        foreach (self::$billId as $data) {
            if ($billId == $data[0]) {
                if ($state == 4) {  // Success
                    $login = $data[1];
                    $amount = $data[2];
                    $fromPlayer = $this->storage->getPlayerObject($login);
                    if ($amount < $this->config->donateAmountForGlobalMsg) {
                        $this->exp_chatSendServerMessage('#donate#You donated #variable#' . $amount . '#donate# Planets to the server.$z$s#donate#, Thank You.', $login);
                    } else {
                        $this->exp_chatSendServerMessage('#donate#The server recieved a donation of #variable#' . $amount . '#donate# Planets from #variable#' . $fromPlayer->nickName . '$z$s#donate#, Thank You.', null);
                    }
                    unset(self::$billId[$data[0]]);
                    break;
                }

                if ($state == 5) { // No go
                    $login = $data[1];
                    $amount = $data[2];

                    $this->exp_chatSendServerMessage('#error#No Planets billed.', $login);
                    unset(self::$billId[$data[0]]);
                    break;
                }

                if ($state == 6) {  // Error
                    $login = $data[1];
                    $fromPlayer = $this->storage->getPlayerObject($login);
                    $this->exp_chatSendServerMessage('#error# There was error with player #variable#' . $fromPlayer->nickName . '$z$s#error# donation.');
                    $this->exp_chatSendServerMessage('#error#' . $stateName, $login);
                    unset(self::$billId[$data[0]]);
                    break;
                }
            }
        }
    }

    /**
     * onPlayerConnect()
     * Function called when a player connects.
     *
     * @param mixed $login
     * @param mixed $isSpectator
     * @return void
     */
    function onPlayerConnect($login, $isSpec) {
        $window = DonatePanelWindow::Create($login);
        $window->setScale(0.8);
        $window->setPosition(44,-88);
        
        $window->show();
    }

    function onPlayerDisconnect($login, $reason = null) {
        DonatePanelWindow::Erase($login);
    }

    /**
     * Donate()
     * Function provides the /donate command.
     *
     * @param mixed $login
     * @param mixed $amount
     * @return void
     */
    function donate($login, $amount = null) {
        $player = $this->storage->getPlayerObject($login);
        if ($amount == "help" || $amount == null) {
            $this->showHelp($login);
            return;
        }
        if (is_numeric($amount)) {
            $amount = (int) $amount;
        } else {
            $this->exp_chatSendServerMessage('#error#Donate takes one argument and it needs to be numeric.', $login);
            return;
        }
        $toLogin = $this->config->toLogin;
        if (empty($this->config->toLogin))
            $toLogin = $this->storage->serverLogin;

        $fromPlayer = $this->storage->getPlayerObject($login);
        $bill = $this->connection->sendBill($login, (int) $amount, 'Planets Donation', $toLogin);
        self::$billId[$bill] = array($bill, $login, $amount);
    }

}

?>