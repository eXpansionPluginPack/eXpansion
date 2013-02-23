<?php

namespace ManiaLivePlugins\MaxPackage\Admin\events;


class onMaxAdmin_Restart extends \ManiaLive\Event\Event{

    function __construct(){

    }

    function fireDo($listener){
        call_user_func_array(array($listener, 'onMaxAdmin_Restart'), array());
    }

}
?>
