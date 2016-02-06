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
class Bets extends ExpPlugin {

    const state_off = "off";
    const state_setBets = "set";
    const state_acceptMoreBets = "accept";
    const state_running = "running";
    const state_nobets = "nobets";

    private $msg_fail, $msg_billSuccess, $msg_billPaySuccess, $msg_totalStake, $msg_winner, $msg_payFail;
    public static $state = self::state_off;
    public static $betAmount = 0;

    /** @var BetCounter[] */
    private $counters = array();

    /** @var Player[] */
    private $players = array();

    public function exp_onLoad() {
	$this->msg_fail = exp_getMessage('#donate#No planets billed');
	$this->msg_error = exp_getMessage('#donate#Error: %1$s');
	$this->msg_payFail = exp_getMessage('#donate#The server was unable to pay your winning bet. Sorry.');
	$this->msg_billSuccess = exp_getMessage('#donate#Bet accepted for#variable# %1$s #donate#planets');
	$this->msg_billPaySuccess = exp_getMessage('#donate#You will recieve#variable# %1$s #donate#planets from the server soon.');
	$this->msg_totalStake = exp_getMessage('#donate#The game is on as#variable# %1$s #donate#joins! Win stake of the bet is now#variable# %2$s #donate#planets');
	$this->msg_winner = exp_getMessage('#variable# %1$s #donate#wins the bet with #variable# %2$s #donate#planets, congratulations');
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();
	$this->enableTickerEvent();

	$ah = ActionHandler::getInstance();
	BetWidget::$action_setAmount = $ah->createAction(array($this, "setBetAmount"));
	BetWidget::$action_acceptBet = $ah->createAction(array($this, "acceptBet"));
	$this->reset();
	
    }

    public function onTick() {
	foreach ($this->counters as $idx => $counter) {
	    if ($counter->check()) {
		unset($this->counters[$idx]);
	    }
	}
    }

    public function onBeginMatch() {
	$this->start(Config::getInstance()->timeoutSetBet);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
	switch (self::$state) {

	    case self::state_running:
		$this->checkWinner();
		break;
	    case self::state_nobets:
		break;
	    default:
		BetWidget::EraseAll();
		$this->exp_chatSendServerMessage("#error#Map was skipped or replayed before bet was placed.");
		break;
	}
    }

    private function checkWinner() {
	$rankings = $this->connection->getCurrentRanking(-1, 0);
	$total = (count($this->players) * self::$betAmount);

	foreach ($rankings as $index => $player) {
	    if (array_key_exists($player->login, $this->players)) {
		$this->exp_chatSendServerMessage($this->msg_winner, null, array($player->nickName . '$z$s', $total));
		$this->connection->pay($player->login, intval($total), 'Winner of the bet!');
		return;
	    }
	}
    }

    private function setState($data) {
	self::$state = $data;
    }

    public function setBetAmount($login, $data) {
	$amount = $data['betAmount'];
	if (!is_numeric($amount) || empty($amount) || $amount < 1) {
	    $this->exp_chatSendServerMessage('#error#Can not place a bet, the value: "#variable#%1$s#error#" is not numeric value!', $login, array($amount));
	    return;
	}

	self::$betAmount = intval($amount);

	$bill = $this->exp_startBill($login, $this->storage->serverLogin, $amount, 'Acccept Bet ?', array($this, 'billSetSuccess'));
	$bill->setErrorCallback(5, array($this, 'billFail'));
	$bill->setErrorCallback(6, array($this, 'billFail'));
	$bill->setSubject('bets_plugin');
    }

    public function acceptBet($login) {
	$bill = $this->exp_startBill($login, $this->storage->serverLogin, self::$betAmount, 'Acccept Bet ?', array($this, 'billAcceptSuccess'));
	$bill->setErrorCallback(5, array($this, 'billFail'));
	$bill->setErrorCallback(6, array($this, 'billFail'));
	$bill->setSubject('bets_plugin');
    }

    /**
     * 	this called when recieves the a bet from server
     * @param \ManiaLivePlugins\eXpansion\Core\types\Bill $bill
     */
    public function billPaySuccess(Bill $bill) {
	$login = $bill->getSource_login();
	$this->exp_chatSendServerMessage($this->msg_billPaySuccess, $login, array($bill->getAmount()));
    }

    /**
     * 	this called when player accepts a bet
     * @param \ManiaLivePlugins\eXpansion\Core\types\Bill $bill
     */
    public function billAcceptSuccess(Bill $bill) {
	$login = $bill->getSource_login();
	try {
	    $this->players[$login] = $this->storage->getPlayerObject($login);
	    $this->exp_chatSendServerMessage($this->msg_billSuccess, $login, array($bill->getAmount()));
	    $this->updateBetWidget();
	    $this->announceTotal($login);
	} catch (\Exception $e) {
	    $this->exp_chatSendServerMessage($this->msg_fail, $login, array($e->getMessage()));
	}
    }

    /**
     * This is called when initial bet is accepted and planets has been transferred
     * @param \ManiaLivePlugins\eXpansion\Core\types\Bill $bill
     */
    public function billSetSuccess(Bill $bill) {
	$this->setState(self::state_acceptMoreBets);
	$login = $bill->getSource_login();
	try {
	    $this->players[$login] = $this->storage->getPlayerObject($login);
	    $this->exp_chatSendServerMessage($this->msg_billSuccess, $login, array($bill->getAmount()));
	    BetWidget::EraseAll();
	    $this->updateBetWidget();
	    $this->announceTotal($login);
	} catch (\Exception $e) {
	    $this->exp_chatSendServerMessage($this->msg_fail, $login, array($e->getMessage()));
	}
    }

    public function announceTotal($login) {
	$total = (count($this->players) * self::$betAmount);
	$nick = $this->players[$login]->nickName . '$z$s';
	$this->exp_chatSendServerMessage($this->msg_totalStake, null, array($nick, $total));
    }

    public function billFail(Bill $bill, $state, $stateName) {
	$this->exp_chatSendServerMessage($this->msg_fail, $bill->getSource_login());
    }

    public function billPayFail(Bill $bill, $state, $stateName) {
	$this->exp_chatSendServerMessage($this->msg_payFail, $bill->getSource_login());
    }

    public function updateBetWidget() {
	$widget = BetWidget::Create(null);
	$widget->setSize(80, 20);
	$widget->setToHide(array_keys($this->players));
	$widget->show(true);
    }

    public function start($timeout) {
	$this->reset();
	$this->setState(self::state_setBets);
	$this->updateBetWidget();
	$this->counters[] = new BetCounter($timeout, array($this, "closeAccept"));
    }

    public function closeAccept($param = null) {
	if (self::$state == self::state_acceptMoreBets) {
	    $this->setState(self::state_running);
	} else {
	    $this->setState(self::state_nobets);
	}
	BetWidget::EraseAll();
    }

    private function reset() {
	self::$state = self::state_off;
	self::$betAmount = 0;
	$this->counters = array();
	$this->players = array();
	BetWidget::EraseAll();
    }

    function exp_onUnload() {
	$ah = ActionHandler::getInstance();
	$ah->deleteAction(BetWidget::$action_acceptBet);
	$ah->deleteAction(BetWidget::$action_setAmount);
	BetWidget::EraseAll();
	parent::exp_onUnload();
    }

}
