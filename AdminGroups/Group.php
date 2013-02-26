<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of Groups
 *
 * @author oliverde8
 */
class Group {
	
	private $groupName;
	private $master;
	private $groupUsers = array();
	private $permissions;
	
	function __construct($groupName, $master) {
		$this->groupName = $groupName;
		$this->master = $master;
	}
	
	public function addAdmin($admin){
		$this->groupUsers[] = $admin;
	}
	
	public function removeAdmin($login){
		$i = 0;
		$found = false;
		while($i < sizeof($this->groupUsers) && !$found){
			if($this->groupUsers[$i]->getLogin() == $login){
				$found = true;
				while(isset($this->groupUsers[$i+1])){
					$this->groupUsers[$i] = $this->groupUsers[$i+1];
					$i++;
				}
				unset($this->groupUsers[$i]);
				return true;
			}
			
		}
		return false;
	}


	public function addPermission($name, $val){
		$this->permissions[$name] = $val;
	}
	
	public function removePermission($name){
		$this->permissions[$name] = false;
	}
	
	public function hasPermission($name){
		return $name==null || $this->master || $this->hasPermission2($name);
	}
	
    private function hasPermission2($name) {
        if ($name == "")
            return true;
        else if (isset($this->permissions[$name])) {
            return $this->permissions[$name];
        }
        else
            return false;
    }
	
	public function getGroupName() {
		return $this->groupName;
	}

	public function getMaster() {
		return $this->master;
	}

	public function getGroupUsers() {
		return $this->groupUsers;
	}

	public function getPermissions() {
		return $this->permissions;
	}



}

?>
