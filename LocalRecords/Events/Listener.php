<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Events;

/**
 * Listener interface for local records
 * @author Petri
 */
interface Listener extends \ManiaLive\Event\Listener {

    /** @var \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record */
    function onNewRecord($data);
    
    /** 
     * onUpdateRecords($data)
     * 
     * Called when 
     * @param \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record[] $data  */
    function onUpdateRecords($data);
       
}

?>
