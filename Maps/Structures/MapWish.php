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

    /** @bool - For temporary additions */
    public $isTemp = false;

    /**
     * MapWish($player, $map, $isTemp);
     * @param \DedicatedApi\Structures\Player $player
     * @param \DedicatedApi\Structures\Map $map
     * @param bool $isTemp
     */
    public function __construct(\DedicatedApi\Structures\Player $player, \DedicatedApi\Structures\Map $map, $isTemp = false) {
        $this->map = $map;
        $this->player = $player;
        $this->isTemp = $isTemp;
    }

}

?>
