<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Events;

use ManiaLive\Event\Listener as EventListener;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

/**
 * Listener interface for local records
 * @author Petri
 */
interface Listener extends EventListener
{

    /**
     * Event triggered on new record
     *
     * @param Record $record
     * @param Record $oldRecord
     */
    function onNewRecord($record, $oldRecord);

    /**
     * onUpdateRecords($record)
     *
     * @param Record[] $record
     */
    function onUpdateRecords($records);

    /**
     *
     * @param Record $record
     */
    function onPersonalBestRecord($record);

    /**
     * @param Record[] $records
     */
    function onRecordsLoaded($records);

    /**
     * @param Record $record
     */
    function onRecordPlayerFinished($record);
}

?>
