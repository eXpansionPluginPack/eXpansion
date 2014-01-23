<?php

namespace ManiaLivePlugins\eXpansion\Maps\Structures;

/**
 * Structure mapWish
 *
 * @author Reaby
 */
class MapWish {

    /** @var \Maniaplanet\DedicatedServer\Structures\Map */
    public $map;

    /** @var \Maniaplanet\DedicatedServer\Structures\Player */
    public $player;

    /** @bool - For temporary additions */
    public $isTemp = false;

    /**
     * MapWish($player, $map, $isTemp);
     * @param \Maniaplanet\DedicatedServer\Structures\Player $player
     * @param \Maniaplanet\DedicatedServer\Structures\Map $map
     * @param bool $isTemp
     */
    public function __construct(\Maniaplanet\DedicatedServer\Structures\Player $player, \Maniaplanet\DedicatedServer\Structures\Map $map, $isTemp = false) {
        $this->map = $map;
        $this->player = $player;
        $this->isTemp = $isTemp;
    }

}

?>
