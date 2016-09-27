<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

use ManiaLive\Data\Storage;

/**
 * Description of Groups
 *
 * @author oliverde8
 */
class GuestGroup extends Group
{
    protected $adminGroups;

    public function __construct($groupName, $master, AdminGroups $adminGroups)
    {
        parent::__construct($groupName, $master);

        $this->adminGroups = $adminGroups;
    }


    public function addAdmin(Admin $admin)
    {
        // Nothing to do here, all users are guest.
    }

    public function removeAdmin($login)
    {
        // Nothing to do here, all users are guest.
    }

    /**
     * Get he user of this group. All users that are not in a group are guest users.
     *
     * @return Admin[]
     */
    public function getGroupUsers()
    {
        /** @var Storage $storage */
        $storage = Storage::getInstance();
        /** @var Admin[] $guests */
        $guests = array();
        foreach ($storage->players as $player) {
            if (!$this->adminGroups->getAdmin($player->login)) {
                $guests[] = new Admin($player->login, $this);
            }
        }
        foreach ($storage->spectators as $player) {
            if (!$this->adminGroups->getAdmin($player->login)) {
                $guests[] = new Admin($player->login, $this);
            }
        }

        return $guests;
    }
}
