<?php
/**
 */
namespace ManiaLivePlugins\eXpansion\AutoTageXpansion;

use ManiaLive\PluginHandler\PluginHandler;

class AutoTageXpansion extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    function exp_onLoad()
    {
        $list = PluginHandler::getInstance()->getLoadedPluginsList();
        $i = 0;
        foreach ($list as $pluginn) {
            $plugin = explode('\\', $pluginn);
            $author = array_shift($plugin);
            $package = implode('\\', $plugin);
            $pluginlist[] = array($author, $package);
            $i++;
            $this->connection->setServerTag('nl.pluginlist', json_encode(array($pluginlist)), true);
            //$this->connection->executeMulticall();
        }
    }

    function exp_onReady()
    {
        $data = $this->connection->getServerTags();
        //var_dump($data);
    }


}

?>