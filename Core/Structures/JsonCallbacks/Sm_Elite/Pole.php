<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

class Pole extends ManiaLiveDedicatedApiStructuresAbstractStructure
{

    /** @var string */
    public $tag = "";

    /** @var integer */
    public $order;

    /** @var boolean */
    public $captured = false;

}