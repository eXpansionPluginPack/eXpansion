<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Events;

use ManiaLive\Event\Listener as EventListener;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

/**
 * Listener interface for local records
 *
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
    public function onNewRecord($record, $oldRecord);

    /**
     * onUpdateRecords($record)
     *
     * @param Record[] $records
     */
    public function onUpdateRecords($records);

    /**
     *
     * @param Record $record
     */
    public function onPersonalBestRecord($record);

    /**
     * @param Record[] $records
     */
    public function onRecordsLoaded($records);

    /**
     * @param Record $record
     */
    public function onRecordPlayerFinished($record);
}
