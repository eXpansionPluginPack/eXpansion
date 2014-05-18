<?php

namespace ManiaLivePlugins\eXpansion\AutoLoad;

use ManiaLive\Utilities\Console;

class AutoLoad extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $plugins;

    public function exp_onLoad() {

	$this->console("[eXpansion] AutoLoading eXpansion pack ... ");

	$config = Config::getInstance();
	
	$this->plugins = array(	'\ManiaLivePlugins\eXpansion\Core\Core'
	, '\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups'
	, '\ManiaLivePlugins\eXpansion\Gui\Gui'
	, '\ManiaLivePlugins\eXpansion\Menu\Menu'
	, '\ManiaLivePlugins\eXpansion\Adm\Adm'
	, '\ManiaLivePlugins\eXpansion\Database\Database');
	
	foreach($config->plugins as $plugin){
	    if(!in_array($plugin, $this->plugins))
		$this->plugins[] = $plugin;
	}
		
		//$config->plugins;
	
	//We Need the plugin Handler
	$pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

	$recheck = array();
	$lastSize = 0;

	$recheck = $this->loadPlugins($this->plugins, $pHandler);

	do {
	    $lastSize = sizeof($recheck);
	    $recheck = $this->loadPlugins($this->plugins, $pHandler);
	} while (!empty($recheck) && $lastSize != sizeof($recheck));

	if (!empty($recheck)) {
	    $this->dumpException("Couldn't Autoload all required plugins", new \Maniaplanet\WebServices\Exception("Autoload failed."));
	    $this->connection->chatSendServerMessage("couldn't Autoload all required plugins, see console log for more details.");
	    $this->console("Not all required plugins were loaded, list of not loaded plugins: ");
	    foreach ($recheck as $pname) {
		$this->console($pname);
	    }
	}
	
	\ManiaLivePlugins\eXpansion\Core\ConfigManager::getInstance()->check();
    }

    public function loadPlugins($list, \ManiaLive\PluginHandler\PluginHandler $pHandler) {
	$recheck = array();
	$disabled = Config::getInstance()->disable;
	if (!is_array($disabled))
	    $disabled = array($disabled);


	foreach ($list as $pname) {
	    try {
		if (!$pHandler->isLoaded($pname)) {
//$this->console("\n[eXpansion Pack]AutoLoading : Trying to Load $pname ... ");

		    if (in_array($pname, $disabled)) {
			$this->console("[" . $pname . "]..............................Disabled -> not loading");
			continue;
		    }
		    $status = false;
		    
		    $metaData = $pname::getMetaData();
		    if($metaData->checkAll()){
			try{
			    $status = $pHandler->load($pname);
			} catch (\Exception $ex) {
			    $status = false;
			}

			if (!$status) {
			    $this->console("[" . $pname . "]..............................FAIL -> will retry");
			    $this->connection->chatSendServerMessage('Starting ' . $pname . '........$f00 Failure');
			    $recheck[] = $pname;
			} else {
			    $this->debug("[" . $pname . "]..............................SUCCESS");
			    //   $this->connection->chatSendServerMessage('Starting ' . $pname . '........$0f0 Success');
			}
		    }else{
			$this->console("[" . $pname . "]..............................Disabled -> Not Compatible");
		    }
		}
	    } catch (\Exception $ex) {
		print_r($ex->getMessage());
		\ManiaLivePlugins\eXpansion\Core\types\ErrorHandler::displayAndLogError($ex);
		$this->connection->chatSendServerMessage("Error has occurred while loading plugins pack, see console for details.");
		$recheck[] = $pname;
	    }
	}
	return $recheck;
    }

}

?>
