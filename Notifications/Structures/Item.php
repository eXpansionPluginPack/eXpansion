<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Structures;

class Item {

    public $message;
    public $icon;
    public $callback;
    public $timestamp;          
    
    public function __construct($icon, $message, $callback) {        
        $this->message = $message;
        $this->icon = $icon;
        $this->callback = $callback;        
        $this->timestamp = time();
    }

}

?>
