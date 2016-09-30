<?php

namespace ManiaLivePlugins\eXpansion\Gui\Structures;

/**
 * Description of ConfigItem
 *
 * @author Reaby
 */
class ConfigItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    /** @var string $id */
    public $id;

    /** @var bool $value */
    public $value = true;

    public $gameMode = "";

    public function __construct($id, $gamemode, $value)
    {
        $this->id = $id;
        $this->gameMode = $gamemode;
        $outval = true;
        if ($value == "0") {
            $outval = false;
        }
        $this->value = $outval;
    }
}
