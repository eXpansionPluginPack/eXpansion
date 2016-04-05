<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of players
 *
 * @author oliver
 */
class Admin
{

    private $login;

    /** @var Group */
    private $group;

    /** @var boolean */
    private $readOnly = false;

    private $ipAllowed = false;

    function __construct($login, Group $group)
    {
        $this->login = $login;
        $this->group = $group;
        $this->readOnly = false;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function hasPermission($name)
    {
        if (is_array($this->ipAllowed)) {
            $ip = \ManiaLive\Data\Storage::getInstance()->getPlayerObject($this->login)->iPAddress;
            $address = explode(":", $ip, 2);

            if (sizeof($address) > 0) {
                foreach ($this->ipAllowed as $addr) {
                    if ($addr == (string)$address[0]) {
                        return $this->group->hasPermission($name);
                    }
                }
            }

            return false;
        } else {
            return $this->group->hasPermission($name);
        }
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function isReadOnly()
    {
        return $this->readOnly;
    }

    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
    }

    public function setAllowedIP($address)
    {
        $this->ipAllowed = $address;
    }

}

?>
