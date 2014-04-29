<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures;

/**
 * Description of RpcObject
 *
 * @author Petri
 */
class RpcObject extends \ManiaLive\DedicatedApi\Structures\AbstractStructure {

    /**
     * @param string $jsonString
     */
    public function __construct($jsonString = false) {
	if ($json)
	    $this->set(json_decode($json, true));
    }

    public function set($data) {
	foreach ($data AS $key => $value) {
	    if (is_array($value)) {
		$sub = new RpcObject();
		$sub->set($value);
		$value = $sub;
	    }
	    $key = lcfirst($key);
	    $this->{$key} = $value;
	}
    }

}
