<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures;

use Maniaplanet\DedicatedServer\Structures\AbstractStructure;

/**
 * Description of RpcObject
 *
 * @author Petri
 */
class RpcObject extends AbstractStructure
{

    /**
     *
     * @param string|bool $json The json string to decode or false if error
     */
    public function __construct($json = false)
    {
        if ($json)
            $this->set(json_decode($json, true));
    }

    /**
     * Sets the json data to the object variables
     *
     * @param $data
     */
    public function set($data)
    {
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
