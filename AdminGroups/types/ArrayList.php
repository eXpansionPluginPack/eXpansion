<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\types;

/**
 * Description of Interger
 *
 * @author oliverde8
 */
class Interger extends \ManiaLivePlugins\eXpansion\AdminGroups\types\absChecker{
	
	public function check($data) {
		return is_numeric($data);
	}

	public function getErrorMsg() {
		return "A numerical value was expected";
	}
	
	
	
}

?>
