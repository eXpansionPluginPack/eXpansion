<?php

namespace ManiaLivePlugins\eXpansion\Gui\Structures;

/**
 * Description of ConfigItem
 *
 * @author Reaby
 */
class ConfigItem extends \DedicatedApi\Structures\AbstractStructure {

    /** @var string $id */
    public $id;

    /** @var bool $value */
    public $value = true;

    public function __construct($id, $value) {
	$this->id = $id;
	$outval = true;
	if ($value == "0")
	    $outval = false;
	$this->value = $outval;
    }

}
