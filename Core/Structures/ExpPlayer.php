<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures;

class ExpPlayer extends \DedicatedApi\Structures\Player {

    const Player_rank_position_change = 1;
    const Player_cp_position_change = 2;

    /** @var int[] */
    public $checkpoints = array(0 => 0);

    /** @var int $finalTime finish time for player: -1 for retire */
    public $finalTime = -1;

    /** @var int $time current checkpoint timestamp */
    public $time = -1;

    /** @var int $curCpIndex current checkpoint index number */
    public $curCpIndex = -1;

    /** @var int $curLap current lap number */
    public $curLap = 0;

    /** @var bool $hasRetired is player retired */
    public $hasRetired = false;

    /** @var int $position current position inside round */
    public $position = -1;

    /** @var int $oldPosition old position inside round */
    public $oldPosition = -2;

    /** @var int $matchScore cumulative score for teams mode in one map */
    public $matchScore = 0;

    /** @var int $deltaTimeTop1  time difference to first player */
    public $deltaTimeTop1 = 0;

    /** @var int $deltaTimeTop1 checkpoint difference to first player */
    public $deltaCpCountTop1 = 0;

    /** @var int $changeFlags flag to indicate change */
    public $changeFlags = 0;

    /** @var bool $isPlaying true if player is playing ie not spectator or disconnected currently */
    public $isPlaying = true;

    static public function fromArray($array) {
        $object = parent::fromArray($array);
        $object->skins = null;
        return $object;
    }

}

?>
