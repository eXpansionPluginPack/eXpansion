<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\Database\Connection as DbConnection;
use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;

/**
 * This will menage bills in order to insert them into the database so that all transactions are saved. It will allow
 * advanced statistics to be done on planet transgers
 *
 * @author De Cramer Oliver
 */
class BillManager implements \ManiaLive\DedicatedApi\Callback\Listener
{

    /**
     * The instance of the bill manager
     *
     * @var BillManager | null
     */
    private static $instance = null;

    /**
     * The database connection adapter
     *
     * @var \ManiaLive\Database\Connection | null
     */
    private $db = null;

    /**
     * List of on going bills
     *
     * @var types\Bill[]
     */
    private $bills = array();

    /**
     * Connection to the Dedicated server
     *
     * @var \Maniaplanet\DedicatedServer\Connection
     */
    private $connection;

    /**
     * Creates a bill manager
     *
     * @param \Maniaplanet\DedicatedServer\Connection $connection connection to the dedicated server
     * @param DbConnection                            $dbcon      The database connection
     * @param                                         $plugin     A plugin to have
     */
    function __construct(\Maniaplanet\DedicatedServer\Connection $connection, DbConnection $dbcon, $plugin)
    {

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
            $this->db->execute($q);
        }
    }

    /**
     * The instance of the BillManager
     *
     * @return BillManager | null
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Start a bill process
     *
     * @param types\Bill $bill
     */
    public function sendBill(types\Bill $bill)
    {
        $billId = $this->connection->sendBill(
            $bill->getSource_login(),
            $bill->getAmount(),
            $bill->getMsg(),
            $bill->getDestination_login()
        );

        if (empty($this->bills))
            \ManiaLive\Event\Dispatcher::register(ServerEvent::getClass(), $this);

        $this->bills[$billId] = $bill;
        $bill->setBillId($billId);
    }

    /**
     * When a bill gets update by the dedicated even is sent
     *
     * @param int    $billId        the identify of the bull
     * @param int    $state         The current code of state of the bill
     * @param string $stateName     the name of the state
     * @param int    $transactionId the identification of the transaction
     */
    public function onBillUpdated($billId, $state, $stateName, $transactionId)
    {
        //If there isn't any un going bills, well stop listening
        if (count($this->bills) == 0)
            \ManiaLive\Event\Dispatcher::unregister(ServerEvent::getClass(), $this);

        //If the bill Manager is managing this bill let do what needs to be done
        if (isset($this->bills[$billId])) {
            $bill = $this->bills[$billId];
            if ($state == 4) {
                //Sucess :D

                //Insert bill into database
                $q = 'INSERT INTO `exp_planet_transaction` (`transaction_fromLogin`, `transaction_toLogin`, `transaction_plugin`
                            ,`transaction_subject`, `transaction_amount`)
                        VALUES(' . $this->db->quote($bill->getSource_login()) . ',
                            ' . $this->db->quote($bill->getDestination_login()) . ',
                            ' . $this->db->quote($bill->getPluginName()) . ',
                            ' . $this->db->quote($bill->getSubject()) . ',
                            ' . $this->db->quote($bill->getAmount()) . '
                        )';
                $this->db->execute($q);

                $bill->validate();
                unset($this->bills[$billId]);
            } elseif ($state == 5) { // No go
                $bill->error(5, $stateName);
                unset($this->bills[$billId]);
            } else if ($state == 6) { // Error
                $bill->error(6, $stateName);
                unset($this->bills[$billId]);
            }
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {

    }

    public function onBeginMatch()
    {

    }

    public function onBeginRound()
    {

    }

    public function onEcho($internal, $public)
    {

    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {

    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {

    }

    public function onEndRound()
    {

    }

    public function onManualFlowControlTransition($transition)
    {

    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified)
    {

    }

    public function onModeScriptCallback($param1, $param2)
    {

    }

    public function onPlayerAlliesChanged($login)
    {

    }

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {

    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex)
    {

    }

    public function onPlayerConnect($login, $isSpectator)
    {

    }

    public function onPlayerDisconnect($login, $disconnectionReason)
    {

    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore)
    {

    }

    public function onPlayerIncoherence($playerUid, $login)
    {

    }

    public function onPlayerInfoChanged($playerInfo)
    {

    }

    public function onPlayerManialinkPageAnswer($playerUid, $login, $answer, array $entries)
    {

    }

    public function onServerStart()
    {

    }

    public function onServerStop()
    {

    }

    public function onStatusChanged($statusCode, $statusName)
    {

    }

    public function onTunnelDataReceived($playerUid, $login, $data)
    {

    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam)
    {

    }

}

?>
