<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of Config
 *
 * @author oliverde8
 */
class Config extends \ManiaLib\Utils\Singleton {

    public $msg_needBeAdmin = "%admin_error%You need to be an Admin to use that command";
    public $msg_commandDonExist = "%admin_error%The command don't exist";
    public $msg_noPermissionMsg = "%admin_error%You don't have the permission to use that admin command";
    public $config_file = "config-eXp-admins.ini";

}

?>
