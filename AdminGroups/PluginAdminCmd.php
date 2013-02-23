<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 *
 * @author oliver
 */
class PluginAdminCmd extends \ManiaLive\PluginHandler\Plugin{

    final protected function addAdminChat($cmd, $function){
        AdminCommand::addAdminChat($cmd, $this, $function);
    }
}
?>
