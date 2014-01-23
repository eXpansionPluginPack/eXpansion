<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;
use ManiaLive\Database\Connection as DbConnection;

/**
 * Description of BillManager
 *
 * @author De Cramer Oliver
 */
class BillManager implements \ManiaLive\DedicatedApi\Callback\Listener {

    private static $instance = null;
    private $db = null;
    private $bills = array();
    private $connection;
    private $aplugin;

    function __construct(\Maniaplanet\DedicatedServer\Connection $connection, DbConnection $dbcon, $plugin) {
        
        echo "Init Bill \n";
        
        $this->connection = $connection;
        self::$instance = $this;
        $this->db = $dbcon;

        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

        if (!$this->db->tableExists("exp_planet_transaction")) {
            $q = "CREATE TABLE `exp_planet_transaction` (
                    `transaction_id` MEDIUMINT( 9 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `transaction_fromLogin` VARCHAR( 200 ) NOT NULL,
                    `transaction_toLogin` VARCHAR( 200 ) NOT NULL,
                    `transaction_plugin` VARCHAR( 200 ) NOT NULL DEFAULT 'unknown',
                    `transaction_subject` VARCHAR( 200 ) NOT NULL DEFAULT 'unknown',
                    `transaction_amount` MEDIUMINT( 4 ) DEFAULT '0'
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = MYISAM ;";
            $this->db->query($q);
        }
    }

    public static function getInstance() {
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
                //Sucess :D

                $q = 'INSERT INTO `exp_planet_transaction` (`transaction_fromLogin`, `transaction_toLogin`, `transaction_plugin`
                            ,`transaction_subject`, `transaction_amount`)
                        VALUES(' . $this->db->quote($bill->getSource_login()) . ',
                            ' . $this->db->quote($bill->getDestination_login()) . ',
                            ' . $this->db->quote($bill->getPluginName()) . ',
                            ' . $this->db->quote($bill->getSubject()) . ',
                            ' . $this->db->quote($bill->getAmount()) . '
                        )';
                $this->db->query($q);
                
                $bill->validate();
                unset($this->bills[$billId]);
            } elseif ($state == 5) { // No go
                $bill->error(5, $stateName);
                unset($this->bills[$billId]);
            } else if ($state == 6) {  // Error
                $bill->error(6, $stateName);
                unset($this->bills[$billId]);
            }
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        
    }

    public function onBeginMatch() {
        
    }

    public function onBeginRound() {
        
    }

    public function onEcho($internal, $public) {
        
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        
    }

    public function onEndRound() {
        
    }

    public function onManualFlowControlTransition($transition) {
        
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {
        
    }

    public function onModeScriptCallback($param1, $param2) {
        
    }

    public function onPlayerAlliesChanged($login) {
        
    }

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        
    }

    public function onPlayerConnect($login, $isSpectator) {
        
    }

    public function onPlayerDisconnect($login, $disconnectionReason) {
        
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        
    }

    public function onPlayerIncoherence($playerUid, $login) {
        
    }

    public function onPlayerInfoChanged($playerInfo) {
        
    }

    public function onPlayerManialinkPageAnswer($playerUid, $login, $answer, array $entries) {
        
    }

    public function onServerStart() {
        
    }

    public function onServerStop() {
        
    }

    public function onStatusChanged($statusCode, $statusName) {
        
    }

    public function onTunnelDataReceived($playerUid, $login, $data) {
        
    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {
        
    }

}

?>
