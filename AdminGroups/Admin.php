<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of players
 *
 * @author oliver
 */
class Admin {

    private $login;
    private $group;

    function __construct($login, Group $group) {
        $this->login = $login;
        $this->group = $group;
    }

    public function getLogin() {
        return $this->login;
    }
	
	public function hasPermission($name){
		return $this->group->hasPermission($name);
	}
	
	public function getGroup() {
		return $this->group;
	}


	
}

?>
