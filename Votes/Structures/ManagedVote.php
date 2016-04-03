<?php

namespace ManiaLivePlugins\eXpansion\Votes\Structures;

/**
 * Description of ManagedVote
 *
 * @author Petri
 */
class ManagedVote extends \Maniaplanet\DedicatedServer\Structures\Vote
{

    /** @var bool managed vote ? */
    public $managed = false;

    /** @var string voteCommand name */
    public $command = "";

    /** @var float ratio */
    public $ratio = 0.5;

    /** @var int timeout */
    public $timeout = 30;

    /** @var int voters */
    public $voters = 1;

    /** @var string */
    public $status;

    /** @var string */
    public $callerLogin;

    /** @var string */
    public $cmdName;

    /** @var mixed[] */
    public $cmdParam;

    public function __construct($cmdName = '', $cmdParam = array())
    {
        parent::__construct($cmdName, $cmdParam);
    }

}
