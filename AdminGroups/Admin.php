<?php
namespace ManiaLivePlugins\eXpansion\AdminGroups;

use ManiaLive\Data\Storage;

/**
 * Description of Admin
 *
 * @author oliver
 */
class Admin
{

    /** @var  string */
    private $login;

    /** @var Group */
    private $group;

    /** @var boolean */
    private $readOnly = false;

    /** @var bool */
    private $ipAllowed = false;

    /**
     * Admin constructor.
     * @param $login
     * @param Group $group
     */
    public function __construct($login, Group $group)
    {
        $this->login = $login;
        $this->group = $group;
        $this->readOnly = false;
    }

    /**
     * gets the login
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasPermission($name)
    {
        if (is_array($this->ipAllowed)) {
            $ip = Storage::getInstance()->getPlayerObject($this->login)->iPAddress;
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

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param $readOnly
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
    }

    /**
     * @param $address
     */
    public function setAllowedIP($address)
    {
        $this->ipAllowed = $address;
    }
}
