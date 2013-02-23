<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of players
 *
 * @author oliver
 */
class Admin {

    private $login;
    private $permissions;
    private $GroupName;

    function __construct($login, $permissions, $groupName) {

        $this->login = $login;
        $this->permissions = $permissions;
        $this->GroupName = $groupName;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getGroupName() {
        return $this->GroupName;
    }

    public function hasPermission($name) {
        if ($name == "")
            return true;
        else if (isset($this->permissions[$name])) {
            return $this->permissions[$name];
        }
        else
            return false;
    }

}

?>
