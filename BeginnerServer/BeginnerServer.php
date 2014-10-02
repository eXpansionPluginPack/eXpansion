<?php

namespace ManiaLivePlugins\eXpansion\BeginnerServer;

class BeginnerServer extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
	
	function exp_onReady()
	{
		$this->connection->setServerTag("server.isBeginner", "true");
		
		
		$data = $this->connection->getServerTags();
		var_dump($data);
	}

}

?>