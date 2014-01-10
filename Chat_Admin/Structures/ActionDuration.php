<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Structures;

/**
 * Description of ActionDuration
 *
 * @author Reaby
 */
class ActionDuration extends \DedicatedApi\Structures\AbstractStructure {

    public $login;
    public $action;
    public $stamp;

    public function __construct($login, $action, $duration) {
        $this->login = $login;
        $this->action = $action;
        $this->stamp = strtotime($duration);
    }

}
