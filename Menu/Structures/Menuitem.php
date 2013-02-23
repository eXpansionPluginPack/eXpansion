<?php

namespace ManiaLivePlugins\eXpansion\Menu\Structures;

class Menuitem {

    public $title;
    public $icon;
    public $callback;
    public $isAdmin;
    public $isSeparator;
    public function __construct($title, $icon, $callback, $isAdmin, $isSeparator = false) {        
        $this->title = $title;
        $this->icon = $icon;
        $this->callback = $callback;
        $this->isAdmin = $isAdmin;
        $this->isSeparator = $isSeparator;
    }

}

?>
