<?php

namespace ManiaLivePlugins\eXpansion\Maps\Structures;

/**
 * Structure mapWish
 *
 * @author Reaby
 */
class MapWish {

    /** @var \DedicatedApi\Structures\Map */
    public $map;

    /** @var \DedicatedApi\Structures\Player */
    public $player;

    public function __construct(\DedicatedApi\Structures\Player $player, \DedicatedApi\Structures\Map $map) {
        $this->map = $map;
        $this->player = $player;
    }

}

?>
