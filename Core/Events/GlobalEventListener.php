<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

/**
 * Description of PlayerEventListener
 *
 * @author reaby
 */
interface GlobalEventListener
{

    public function onMapRestart();

    public function onMapSkip();

}

?>

