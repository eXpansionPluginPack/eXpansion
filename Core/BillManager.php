<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;

/**
 * Description of BillManager
 *
 * @author De Cramer Oliver
 */
class BillManager implements \ManiaLive\DedicatedApi\Callback\Listener {

    private static $instance = null;
    
    private $bills = array();
    private $connection;

    function __construct(\DedicatedApi\Connection $connection) {
        $this->connection = $connection;
        self::$instance = $this;
    }
    
    public static function getInstance(){
        return self::$instance;
    }

    public function sendBill(types\Bill $bill) {
        $billId = $this->connection->sendBill($bill->getSource_login(), $bill->getAmount(), $bill->getMsg(), $bill->getDestination_login());

        if (empty($this->bills))
            \ManiaLive\Event\Dispatcher::register(ServerEvent::getClass(), $this);

        $this->bills[$billId] = $bill;
        $bill->setBillId($billId);
    }

    public function onBillUpdated($billId, $state, $stateName, $transactionId) {
        if (count($this->bills) == 0)
            \ManiaLive\Event\Dispatcher::unregister(ServerEvent::getClass(), $this);

        if (isset($this->bills[$billId])) {
            $bill = $this->bills[$billId];
            if ($state == 4) {
                $bill->validate();
                unset($this->bills[$billId]);
                
            }elseif ($state == 5) { // No go
                $bill->error(5);
                unset($this->bills[$billId]);
                
            }else if ($state == 6) {  // Error
                $bill->error(6);
                unset($this->bills[$billId]);
            }
        }
    }
    
    public function onBeginMap($map, $warmUp, $matchContinuation) {}

    public function onBeginMatch() {}

    public function onBeginRound() {}

    public function onEcho($internal, $public) {}

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {}

    public function onEndMatch($rankings, $winnerTeamOrMap) {}

    public function onEndRound() {}

    public function onManualFlowControlTransition($transition) {}

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {}

    public function onModeScriptCallback($param1, $param2) {}

    public function onPlayerAlliesChanged($login) {}

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {}

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {}

    public function onPlayerConnect($login, $isSpectator) {}

    public function onPlayerDisconnect($login, $disconnectionReason) {}

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {}

    public function onPlayerIncoherence($playerUid, $login) {}

    public function onPlayerInfoChanged($playerInfo) {}

    public function onPlayerManialinkPageAnswer($playerUid, $login, $answer, array $entries) {}

    public function onServerStart() {}

    public function onServerStop() {}

    public function onStatusChanged($statusCode, $statusName) {}

    public function onTunnelDataReceived($playerUid, $login, $data) {}

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {}

}

?>
