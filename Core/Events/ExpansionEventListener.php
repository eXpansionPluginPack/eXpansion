<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

interface ExpansionEventListener
{
    /**
     * Called before new eXpansion instance has started
     *
     * @return void
     */
    public function eXp_onRestartStart();

    /**
     * Called after the new eXpansion instance has been started
     *
     * @return void
     */
    public function eXp_onRestartEnd();
}
