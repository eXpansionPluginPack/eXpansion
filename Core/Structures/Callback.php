<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures;

class Callback
{

    public $method;
    public $params;

    public function __construct($method, $params)
    {
        $this->method = $method;
        $this->params = $params;
    }

}
