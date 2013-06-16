<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Structures;

class Wish {

    public $song;
    public $player;

    public function __construct(Song $song, \DedicatedApi\Structures\Player $player) {
        $this->song = $song;
        $this->player = $player;
    }

}