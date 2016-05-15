<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of Groups
 *
 * @author oliverde8
 */
class Group
{
    private $groupName;

    /** @var boolean */
    private $master;

    /** @var Admin[] */
    private $groupUsers = array();
    private $permissions;
    private $inherits = array();

    function __construct($groupName, $master) {
        $this->groupName = $groupName;
        $this->master = $master;
    }

    public function addAdmin(Admin $admin) {
        $this->groupUsers[] = $admin;
    }

    public function removeAdmin($login) {
        $i = 0;
        $found = false;
        $newGroupUsers = array();

        while ($i < sizeof($this->groupUsers)) {
            if ($this->groupUsers[ $i ]->getLogin() === $login) {
                $found = true;
            }
            else {
                $newGroupUsers[] = $this->groupUsers[ $i ];
            }
            $i++;
        }
        $this->groupUsers = $newGroupUsers;

        return $found;
    }

    public function addPermission($name, $val) {
        $this->permissions[ $name ] = $val;
    }

    public function removePermission($name) {
        $this->permissions[ $name ] = false;
    }

    public function hasPermission($name) {
        return $name == null || $this->master || $this->hasPermission2($name);
    }

    public function getPermission($name) {
        if ($name == null || $this->master) {
            return AdminGroups::HAVE_PERMISSION;
        }
        else {
            if (isset($this->permissions[ $name ])) {
                return $this->permissions[ $name ];
            }
            else return AdminGroups::UNKNOWN_PERMISSION;
        }
    }

    private function hasPermission2($name) {
        if ($name == "") {
            return true;
        }
        else {
            if (isset($this->permissions[ $name ])) {
                if ($this->permissions[ $name ] == AdminGroups::UNKNOWN_PERMISSION) {

                    return $this->hasInheritancePermission($name);
                }

                return $this->permissions[ $name ] == AdminGroups::HAVE_PERMISSION;
            }
            else {
                $this->permissions[ $name ] = AdminGroups::UNKNOWN_PERMISSION;

                return $this->hasInheritancePermission($name);
            }
        }
    }

    private function hasInheritancePermission($name) {
        $actual = AdminGroups::UNKNOWN_PERMISSION;
        if (! empty($this->inherits)) {
            $i = 0;

            foreach ($this->inherits as $gname => $group) {
                $actual = $group->getPermission($name);
                if ($actual == AdminGroups::HAVE_PERMISSION)
                    return true;
            }
        }

        return $actual == AdminGroups::HAVE_PERMISSION;
    }

    public function getGroupName() {
        return $this->groupName;
    }

    public function isMaster() {
        return $this->master;
    }

    /** @return Admin[] */
    public function getGroupUsers() {
        return $this->groupUsers;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function getInherits() {
        return $this->inherits;
    }

    public function resetInherits() {
        $this->inherits = array();
    }

    public function addInherits(Group $group) {
        $this->inherits[ $group->getGroupName() ] = $group;
    }
}
