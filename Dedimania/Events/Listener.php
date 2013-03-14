<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Events;

interface Listener extends \ManiaLive\Event\Listener {

    function onDedimaniaOpenSession($data);
    function onDedimaniaGetRecords($data);
    function onDedimaniaUpdateRecords($data);
    function onDedimaniaNewRecord($data);
}

?>
