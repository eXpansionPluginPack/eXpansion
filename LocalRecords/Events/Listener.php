<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Events;

/**
 * Listener interface for local records
 * @author Petri
 */
interface Listener extends \ManiaLive\Event\Listener {

    /** @var \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record */
    function onNewRecord($data);
    
    /** @var array(\ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record) */
    function onUpdateRecords($data);
       
}

?>
