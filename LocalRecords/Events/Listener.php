<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Events;

/**
 * Listener interface for local records
 * @author Petri
 */
interface Listener extends \ManiaLive\Event\Listener {

    /**
     *  @param \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record $record
     */
    function onNewRecord($record);

    /**
     * onUpdateRecords($record)
     *      
     * @param \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record[] $record 
     */
    function onUpdateRecords($record);

    /**
     * 
     * @param \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record $record
     */
    function onPersonalBestRecord($record);
}

?>
