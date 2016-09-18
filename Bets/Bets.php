<?php
/*
 * Copyright (C) 2014 Reaby
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\Bets;

use ManiaLive\Data\Player;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Bets\Classes\BetCounter;
use ManiaLivePlugins\eXpansion\Bets\Gui\Widgets\BetWidget;
use ManiaLivePlugins\eXpansion\Core\types\Bill;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;

/**
 * Description of Bets
 *
 * @author Reaby
 */
class Bets extends ExpPlugin
{
    const OFF = "off";
    const SET = "set";
    const ACCEPT = "accept";
    const RUNNING = "running";
    const NOBETS = "nobets";

    private $msg_fail, $msg_billSuccess, $msg_billPaySuccess, $msg_totalStake, $msg_winner, $msg_payFail;
    public static $state = self::OFF;
    public static $betAmount = 0;
    private $wasWarmup = false;

    /** @var BetCounter[] */
    private $counters = array();

    /** @var Player[] */
    private $players = array();

    public function eXpOnLoad()
    {
        $this->msg_fail = eXpGetMessage('#donate#No planets billed');
        $this->msg_error = eXpGetMessage('#donate#Error: %1$s');
        $this->msg_payFail = eXpGetMessage('#donate#The server was unable to pay your winning bet. Sorry.');
        $this->msg_billSuccess = eXpGetMessage('#donate#Bet accepted for#variable# %1$s #donate#planets');
        $this->msg_billPaySuccess = eXpGetMessage('#donate#You will recieve#variable# %1$s #donate#planets from the server soon.');
        $this->msg_totalStake = eXpGetMessage('#donate#The game is on as#variable# %1$s #donate#joins! Win stake of the bet is now#variable# %2$s #donate#planets');
        $this->msg_winner = eXpGetMessage('#variable# %1$s #donate#wins the bet with #variable# %2$s #donate#planets, congratulations');
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->enableTickerEvent();

        $ah = ActionHandler::getInstance();
        BetWidget::$action_setAmount = $ah->createAction(array($this, "setBetAmount"), null);
        BetWidget::$action_setAmount25 = $ah->createAction(array($this, "setBetAmount"), 25);
        BetWidget::$action_setAmount50 = $ah->createAction(array($this, "setBetAmount"), 50);
        BetWidget::$action_setAmount100 = $ah->createAction(array($this, "setBetAmount"), 100);
        BetWidget::$action_setAmount250 = $ah->createAction(array($this, "setBetAmount"), 250);
        BetWidget::$action_setAmount500 = $ah->createAction(array($this, "setBetAmount"), 500);
        BetWidget::$action_acceptBet = $ah->createAction(array($this, "acceptBet"));
        $this->reset();
    }

    public function onTick()
    {
        foreach ($this->counters as $idx => $counter) {
            if ($counter->check()) {
                unset($this->counters[$idx]);
            }
        }
    }

    public function onBeginMatch()
    {
        if (!$this->connection->getWarmUp()) {
            $this->start(Config::getInstance()->timeoutSetBet);
        }
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {

        switch (self::$state) {

            case self::RUNNING:
                if (!$this->connection->getWarmUp()) {
                    $this->checkWinner();
                }
                break;
            case self::NOBETS:
                break;
            default:
                BetWidget::EraseAll();
                $this->eXpChatSendServerMessage("#error#Map was skipped or replayed before bet was placed.");
                break;
        }
    }

    private function checkWinner()
    {
        $rankings = $this->expStorage->getCurrentRanking();
        $total = (count($this->players) * self::$betAmount);

        foreach ($rankings as $index => $player) {
            if (array_key_exists($player->login, $this->players)) {
                $this->eXpChatSendServerMessage($this->msg_winner, null, array($player->nickName . '$z$s', $total));
                $this->connection->pay($player->login, intval($total), 'Winner of the bet!');
                $this->players = array();
                return;
            }
        }
    }

    private function setState($data)
    {
        self::$state = $data;
    }

    public function setBetAmount($login, $amount = null, $data = array())
    {
        if ($amount == null) {
            $amount = $data['betAmount'];
        }

        if (!is_numeric($amount) || empty($amount) || $amount < 1) {
            $this->eXpChatSendServerMessage('#error#Can\'t place a bet, the value: "#variable#%1$s#error#" is not numeric value!', $login, array($amount));

            return;
        }

        if ($amount < 20) {
            $this->eXpChatSendServerMessage('#error#Custom value must be over 20 planets!', $login);

            return;
        }

        self::$betAmount = intval($amount);

        $bill = $this->eXpStartBill($login, $this->storage->serverLogin, $amount, 'Acccept Bet ?', array($this, 'billSetSuccess'));
        $bill->setErrorCallback(5, array($this, 'billFail'));
        $bill->setErrorCallback(6, array($this, 'billFail'));
        $bill->setSubject('bets_plugin');
    }

    public function acceptBet($login)
    {
        $bill = $this->eXpStartBill($login, $this->storage->serverLogin, self::$betAmount, 'Acccept Bet ?', array($this, 'billAcceptSuccess'));
        $bill->setErrorCallback(5, array($this, 'billFail'));
        $bill->setErrorCallback(6, array($this, 'billFail'));
        $bill->setSubject('bets_plugin');
    }

    /**
     *    this called when recieves the a bet from server
     *
     * @param \ManiaLivePlugins\eXpansion\Core\types\Bill $bill
     */
    public function billPaySuccess(Bill $bill)
    {
        $login = $bill->getSource_login();
        $this->eXpChatSendServerMessage($this->msg_billPaySuccess, $login, array($bill->getAmount()));
    }

    /**
     *    this called when player accepts a bet
     *
     * @param \ManiaLivePlugins\eXpansion\Core\types\Bill $bill
     */
    public function billAcceptSuccess(Bill $bill)
    {
        $login = $bill->getSource_login();
        try {
            $this->players[$login] = $this->storage->getPlayerObject($login);
            $this->eXpChatSendServerMessage($this->msg_billSuccess, $login, array($bill->getAmount()));
            $this->updateBetWidget();
            $this->announceTotal($login);
        } catch (\Exception $e) {
            $this->eXpChatSendServerMessage($this->msg_fail, $login, array($e->getMessage()));
        }
    }

    /**
     * This is called when initial bet is accepted and planets has been transferred
     *
     * @param \ManiaLivePlugins\eXpansion\Core\types\Bill $bill
     */
    public function billSetSuccess(Bill $bill)
    {
        $this->setState(self::ACCEPT);
        $login = $bill->getSource_login();
        try {
            $this->players[$login] = $this->storage->getPlayerObject($login);
            $this->eXpChatSendServerMessage($this->msg_billSuccess, $login, array($bill->getAmount()));
            BetWidget::EraseAll();
            $this->updateBetWidget();
            $this->announceTotal($login);
        } catch (\Exception $e) {
            $this->eXpChatSendServerMessage($this->msg_fail, $login, array($e->getMessage()));
        }
    }

    public function announceTotal($login)
    {
        $total = (count($this->players) * self::$betAmount);
        $nick = $this->players[$login]->nickName . '$z$s';
        $this->eXpChatSendServerMessage($this->msg_totalStake, null, array($nick, $total));
    }

    public function billFail(Bill $bill, $state, $stateName)
    {
        $this->eXpChatSendServerMessage($this->msg_fail, $bill->getSource_login());
    }

    public function billPayFail(Bill $bill, $state, $stateName)
    {
        $this->eXpChatSendServerMessage($this->msg_payFail, $bill->getSource_login());
    }

    public function updateBetWidget()
    {
        $widget = BetWidget::Create(null, true);
        $widget->setSize(80, 20);
        $widget->setToHide(array_keys($this->players));
        $widget->show();
    }

    public function start($timeout)
    {
        $this->reset();
        $this->setState(self::SET);
        $this->updateBetWidget();
        $this->counters[] = new BetCounter($timeout, array($this, "closeAccept"));
    }

    public function closeAccept($param = null)
    {
        if (self::$state == self::ACCEPT) {
            $this->setState(self::RUNNING);
        } else {
            $this->setState(self::NOBETS);
        }
        BetWidget::EraseAll();
    }

    private function reset()
    {
        self::$state = self::OFF;
        self::$betAmount = 0;
        $this->counters = array();
        $this->players = array();
        BetWidget::EraseAll();
    }

    public function eXpOnUnload()
    {
        $ah = ActionHandler::getInstance();
        $ah->deleteAction(BetWidget::$action_acceptBet);
        $ah->deleteAction(BetWidget::$action_setAmount);
        BetWidget::EraseAll();
        parent::eXpOnUnload();
    }
}