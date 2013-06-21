<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of players
 *
 * @author oliver
 */
class Admin {

    private $login;

    /** @var Group */
    private $group;

    /** @var boolean */
    private $readOnly = false;

    function __construct($login, Group $group) {
        $this->login = $login;
        $this->group = $group;
        $this->readOnly = false;
    }

    public function getLogin() {
        return $this->login;
    }

    public function hasPermission($name) {
        return $this->group->hasPermission($name);
    }

    public function getGroup() {
        return $this->group;
    }

    public function isReadOnly() {
        return $this->readOnly;
    }

    public function setReadOnly($readOnly) {
        $this->readOnly = $readOnly;
    }

}

?>
