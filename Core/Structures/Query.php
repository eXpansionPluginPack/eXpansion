<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures;

class Query
{

    public $callback;
    public $method;
    public $params;
    public $from;

    public function __construct($method, $params, $callback, $from)
    {
        $this->callback = $callback;
        $this->method = $method;
        $this->params = $params;
        $this->from = $from;
    }

}
