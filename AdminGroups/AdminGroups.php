<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;
use ManiaLive\Event\Dispatcher;

/**
 * Admin Groups for eXpansion
 *
 * @author oliver
 */
class AdminGroups extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    /** Constant for permission values */
    const HAVE_PERMISSION = "y";
    const NO_PERMISSION = "n";
    const UNKNOWN_PERMISSION = "u";

    /** Default name of Guest Group */
    const GROUP_GUEST = "Guest";

    /**
     * The instance of the runing AdminGroup plugin
     *
     * @var \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups
     */
    private static $instance;

    /** @var  GuestGroup */
    private static $guestGroup;

    /**
     * Get currect running instance of the singleton
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * List of all the admins(of any group)
     *
     * @var Admin[]
     */
    private static $admins = array();

    /**
     * The commands array
     *
     * @var AdminCmd[]
     */
    private static $commands = array();

    /**
     * The short command list
     *
     * @var AdminCmd[]
     */
    private static $shortCommands = array();

    /**
     * @var AdminCmd[] List of All commans
     * Used for the Help
     */
    private static $commandsList = array();

    /**
     * List of all permissions
     *
     * @var type
     */
    private static $permissionList = array();

    /**
     * List of all Groups
     *
     * @var Group[]
     */
    private static $groupList = array();

    /**
     * The Configuration
     *
     * @var Config
     */
    private $config;

    /**
     * When was the configuration file loaded?
     *
     * @var Integer
     */
    private $readTime = 0;

    /**
     * All messages & text needed
     */
    private $msg_needBeAdmin;
    private $msg_cmdDontEx;
    private $msg_neeMorPerm;
    private $msg_aInGroup;
    private $msg_paddSuc;
    private $msg_paddFai;
    private $msg_premoveSelf;
    private $msg_pRemoveSuc;
    private $msg_pRemoveFa;
    private $msg_masterMasterE;
    private $adminIps = array();
    public static $txt_msg_cmdDontEx;
    public static $txt_groupsTitle;
    public static $txt_helpTitle;
    public static $txt_permissionsTitle;
    public static $txt_playersTitle;
    public static $txt_nwGroupNameL;
    public static $txt_add;
    public static $txt_inherits;
    public static $txt_inheritsTitle;
    public static $txt_groupName;
    public static $txt_nbPlayers;
    public static $txt_playerList;
    public static $txt_permissionList;
    public static $txt_deletegroup;
    public static $txt_rmPlayer;
    public static $txt_command;
    public static $txt_description;
    public static $txt_descMore;
    public static $txt_aliases;
    public static $txt_noPermissionMsg;
    public static $txt_permissions = array();

    /**
     * @inheritdoc
     */
    public function expOnInit()
    {
        parent::expOnInit();
        self::$instance = $this;

        //Recovering the configuration
        $this->config = Config::getInstance();

        $this->loadIps();
        $this->loadAdmins();

        // Re-save file after reading. The format might have changed.
        $this->saveFile();
    }

    /**
     * @inheritdoc
     */
    public function eXpOnLoad()
    {

        //Loading all Messages;
        $this->msg_needBeAdmin = eXpGetMessage('#admin_error#You need to be an Admin to use that command');
        $this->msg_cmdDontEx = eXpGetMessage('#admin_error#That Admin command doesen\'t exist. Use #variable#/admin help #admin_error#to see all commands');
        $this->msg_neeMorPerm = eXpGetMessage('#admin_error#You don\'t have the permission to use that admin command');
        $this->msg_aInGroup = eXpGetMessage('#admin_error#Player #variable#%1$s #admin_error#is already in a group #admin_error#%2$s. #admin_error#Remove him first');
        $this->msg_paddSuc = eXpGetMessage('#admin_action#Player #variable# %1$s #admin_action#has been added to admin group #variable#%2$s');
        $this->msg_paddFai = eXpGetMessage('#admin_action#Failed to add player #variable# %1$s #admin_action# to admin group #variable#%2$s');
        $this->msg_premoveSelf = eXpGetMessage('#admin_error#Your are #variable#%1$s #admin_error#You can\'t remove yourself from a group');
        $this->msg_pRemoveSuc = eXpGetMessage('#admin_action#Player : #variable#%1$s #admin_action#Has been removed from admin group #variable#%2$s');
        $this->msg_pRemoveFa = eXpGetMessage('#admin_error#Player #variable#%1$s #admin_action#isn\'t in the group');
        $this->msg_masterMasterE = eXpGetMessage('#admin_error#Master Admins has all rights. You can\'t change that!');
        $this->msg_removeMlAdmin = eXpGetMessage('#admin_error#Master admin #variable#%1$s has been defined in config.ini and not throught eXpansion. Can\'t remove!');
        self::$txt_msg_cmdDontEx = $this->msg_cmdDontEx;
        self::$txt_noPermissionMsg = $this->msg_neeMorPerm;
        self::$txt_groupsTitle = eXpGetMessage('Admin Groups');
        self::$txt_helpTitle = eXpGetMessage('Admin Commands Help');
        self::$txt_permissionsTitle = eXpGetMessage('Admin Group Permission - %1$s');
        self::$txt_playersTitle = eXpGetMessage('Admin Group Players - %1$s');
        self::$txt_nwGroupNameL = eXpGetMessage('New Group Name :');
        self::$txt_add = eXpGetMessage('Add');
        self::$txt_inherits = eXpGetMessage('Inherits');
        self::$txt_inheritsTitle = eXpGetMessage('Admin Group Inherits - %1$s');
        self::$txt_groupName = eXpGetMessage('Group Name');
        self::$txt_nbPlayers = eXpGetMessage('Nb Players');
        self::$txt_playerList = eXpGetMessage("Player List");
        self::$txt_permissionList = eXpGetMessage('Change Permissions');
        self::$txt_deletegroup = eXpGetMessage('Delete Group');
        self::$txt_rmPlayer = eXpGetMessage('Remove Player');
        self::$txt_command = eXpGetMessage('Command');
        self::$txt_description = eXpGetMessage('Description');
        self::$txt_descMore = eXpGetMessage('More');
        self::$txt_aliases = eXpGetMessage('Aliases');

        foreach (self::$permissionList as $permission => $val) {
            self::$txt_permissions[$permission] = eXpGetMessage("Permission_" . $permission);
        }

        //No idea if needed, I think not need to check
        // $this->enableDedicatedEvents();
        //Registering public functions
        $this->setPublicMethod('adminCmd');
        $this->setPublicMethod('getPermission');

        //$this->registerChatCommand('test', "test", 0, true);
        //Registering the admin chat comman with a lot of parameters
        $this->registerChatCommand('admin', "adminCmd", -1, true);
        $this->registerChatCommand('adm', "adminCmd", -1, true);

        $cmd = $this->addAdminCommand('groups', $this, "windowGroups", null);
        $cmd->setHelp("Administrate the admin groups players and permissions.");

        $cmd = $this->addAdminCommand('help', $this, "windowHelp", null);
        $cmd->setHelp("Show the list of all available admin commands and alliases.");

        Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_PLAYER_CHAT);
    }

    /**
     * Reload the admin configuration file if needed.
     */
    public function reLoadAdmins()
    {
        if ($this->config->fileName == null) {
            $filename = "config/" . $this->storage->serverLogin . "_admins.ini";
        } else {
            $filename = "config/" . $this->config->fileName;
        }

        if (file_exists($filename) && is_readable($filename)) {
            $time = filemtime($filename);

            if ($time > $this->readTime) {
                $this->loadAdmins();
            }
        } else {
            touch($filename);
            $this->loadAdmins();
        }
    }

    /**
     * loads the ip addresses of the admins
     */
    public function loadIps()
    {
        $filename = "config/exp_admin_ip.ini";
        try {
            if (file_exists($filename) && is_readable($filename)) {
                $values = \parse_ini_file($filename, true);

                foreach ($values as $login => $ips) {
                    $this->adminIps[$login] = $ips;
                }
            } else {
                touch($filename);
                $file = ";-----------------------------------------\n" . "; allowed admin ip table\n" . ";------------------------------------------\n" . "; this file allows you to set restrict admin access for specific ip. \n" . "; tip: you can define multiple ip's for logins.\n" . '; format: login[] = "ip-value"' . "\n;\n;example:\n" . '; oliverde8[] = "192.168.0.1"' . "\n" . '; reaby[] = "192.168.0.2"' . "\n" . '; reaby[] = "192.168.0.1"' . "\n";

                file_put_contents($filename, $file);
            }
        } catch (\Exception $e) {
            $this->console("Error while loading allowed admin ips from file: " . $e->getMessage());
        }
    }

    /**
     * Gets allowed ips for the login
     *
     * @param string $login
     *
     * @return false|string[]
     */
    public function getAllowedIp($login)
    {
        if (array_key_exists($login, $this->adminIps)) {
            return $this->adminIps[$login];
        }

        return false;
    }

    /**
     * Loads the Admin configuration File. And will reset everything
     */
    public function loadAdmins()
    {
        //Reset settings
        self::$admins = array();
        self::$groupList = array();

        //Recovering the admin groups
        try {
            if ($this->config->fileName == null) {
                $filename = "config/" . $this->storage->serverLogin . "_admins.ini";
            } else {
                $filename = "config/" . $this->config->fileName;
            }

            $values = \parse_ini_file($filename, true);

            //Save the read Time
            $this->readTime = time();

            $inheritances = array();

            //reading the admin groups and settings
            foreach ($values as $key => $value) {
                //THe settings
                if ($key == 'Settings') {

                } else {
                    $param = explode(": ", $key);

                    if ($param[0] == 'MasterAdmin') {
                        $this->parseMaster($param[1], $value);
                    } else if ($param[0] == 'Group') {
                        //We have found a Admin group, lets see the permissions of
                        //the group and the players that is part of it
                        $inheritances[$param[1]] = $this->parseGroup($param[1], $value);
                    } else if ($param[0] == 'Guest') {
                        //We have found a the guest group.
                        $inheritances[$param[1]] = $this->parseGroup($param[1], $value, true);
                    }
                }
            }


            foreach ($inheritances as $name => $inherits) {
                $mainGroup = null;
                foreach (self::$groupList as $groupe) {
                    if ($groupe->getGroupName() == $name) {
                        $mainGroup = $groupe;
                        break;
                    }
                }

                foreach ($inherits as $groupe) {
                    $inheritedGroup = null;
                    foreach (self::$groupList as $g) {
                        if (strtolower($g->getGroupName()) == strtolower($groupe)) {
                            $inheritedGroup = $g;
                            break;
                        }
                    }

                    if ($inheritedGroup != null) {
                        $mainGroup->addInherits($inheritedGroup);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->console("Error while loading admins from file: " . $e->getMessage());
        }


        $this->loadMLAdmins();
    }

    /**
     * Load the admins for each group.
     */
    public function loadMLAdmins()
    {
        $masterGroup = $this->getMasterGroup();
        self::$guestGroup = $this->getGuestGroup();

        foreach (\ManiaLive\Features\Admin\AdminGroup::get() as $login) {
            $admin = new Admin($login, $masterGroup);
            $admin->setReadOnly(true);
            $admin->setAllowedIP($this->getAllowedIp($login));
            if (isset(self::$admins[$login])) {
                self::$admins[$login]->getGroup()->removeAdmin($admin->getLogin());
                unset(self::$admins[$login]);
            }
            self::$admins[$login] = $admin;
            $masterGroup->addAdmin($admin);
        }
    }

    /**
     * Get the guest group. If the group don't exist it will create it.
     *
     * @return GuestGroup
     */
    public function getGuestGroup()
    {
        $guestGroup = null;
        foreach (self::$groupList as $group) {
            if ($group instanceof GuestGroup) {
                $guestGroup = $group;
                break;
            }
        }

        if ($guestGroup == null) {
            $guestGroup = new GuestGroup(self::GROUP_GUEST, false, $this);
            self::$groupList[] = $guestGroup;
        }

        return $guestGroup;
    }

    /**
     * Get the master group. If the group don't exist it will create it.
     *
     * @return Group|null
     */
    public function getMasterGroup()
    {
        $masterGroup = null;
        foreach (self::$groupList as $group) {
            if ($group->isMaster()) {
                $masterGroup = $group;
                break;
            }
        }

        if ($masterGroup == null) {
            $masterGroup = new Group('Master Admin', true);
            self::$groupList[] = $masterGroup;
        }

        return $masterGroup;
    }

    /**
     * Announces a chat message to a certaint group
     *
     *  $ag = AdminGroups::getIntance();
     *  $ag->announceToGroup($ag->getGroup("Admins"), exp_getMessage("your message here"));
     *
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\Group $group
     * @param String|\ManiaLivePlugins\eXpansion\Core\i18n\Message $msg
     */
    public function announceToGroup(Group $group, $msg, $args = array())
    {
        foreach ($group->getGroupUsers() as $user) {
            $player = $this->storage->getPlayerObject($user->getLogin());
            if ($player != null && $player->isConnected) {
                $this->eXpChatSendServerMessage($msg, $user->getLogin(), $args);
            }
        }
    }

    /**
     * Announces a chat message to certaint permission
     *
     * @param string $permission common usage would be to use
     *                                                                         Permission::constant
     * @param String|\ManiaLivePlugins\eXpansion\Core\i18n\Message $msg
     */
    public function announceToPermission($permission, $msg, $args = array())
    {
        foreach (self::$groupList as $group) {
            if ($group->hasPermission($permission)) {
                $this->announceToGroup($group, $msg, $args);
            }
        }
    }

    /**
     * Gets group object by name
     *
     * @param String $groupName
     *
     * @return Null|Group
     */
    public function getGroup($groupName)
    {
        foreach (self::$groupList as $group) {
            if ($group->getGroupName() == $groupName) {
                return $group;
            }
        }

        return null;
    }

    /**
     * Parse a group
     *
     * @param string $groupName The groups name
     * @param array $value Data from the csv
     * @oaram bool   $isGuest   Is the group the guest group
     *
     * @return array
     */
    private function parseGroup($groupName, $value, $isGuest = false)
    {
        $inherits = array();

        if ($isGuest) {
            $group = new GuestGroup($groupName, false, $this);
        } else {
            $group = new Group($groupName, false);
        }

        //Settings and Permissions
        foreach ($value as $key => $val) {
            $param = explode(".", $key);

            if ($param[0] == 'permission') {
                if (!empty($param[1])) {
                    self::$permissionList[$param[1]] = true;
                    $group->addPermission($param[1], $this->entryCheck($val));
                }
            } elseif ($param[0] == 'settings') {
                //
            }
        }

        if (isset($value["inherit"])) {
            $inherits = $value["inherit"];
        }

        //Lets get the players
        if (isset($value["login"])) {
            foreach ($value["login"] as $login) {
                $admin = new Admin($login, $group);
                $admin->setAllowedIP($this->getAllowedIp($login));
                if (!isset(self::$admins[$login])) {
                    self::$admins[$login] = $admin;
                    $group->addAdmin($admin);
                }
            }
        }
        self::$groupList[] = $group;

        return $inherits;
    }

    /**
     * Parse the Master admins group
     *
     * @param string $groupName The groups name
     * @param array $permissions
     */
    private function parseMaster($groupName, $permissions)
    {
        //Settings and Permissions
        foreach ($permissions as $key => $val) {
            $param = explode(".", $key);

            if ($param[0] == 'permission') {
                if (!empty($param[1])) {
                    self::$permissionList[$param[1]] = true;
                }
            }
        }

        $group = new Group($groupName, true);
        if (isset($permissions["login"])) {
            foreach ($permissions["login"] as $login) {
                $admin = new Admin($login, $group);
                $admin->setAllowedIP($this->getAllowedIp($login));
                $group->addAdmin($admin);

                self::$admins[$login] = $admin;
            }
        }
        self::$groupList[] = $group;
    }

    /**
     * Saves the AdminGroups settings file
     *
     * @throws \Exception
     */
    public function saveFile()
    {
        $string = "";

        foreach (self::$groupList as $group) {

            if ($group->isMaster()) {
                $string .= ";MasterAdmin is a special group that has all permissions. \n";
                $string .= ";No need to specify permissions. But we will to show all permissions\n";
                $string .= "\n\n[MasterAdmin: " . $group->getGroupName() . "]\n";
            } else if ($group instanceof GuestGroup) {
                $string .= "\n\n[Guest: " . $group->getGroupName() . "]\n";
            } else {
                $string .= "\n\n[Group: " . $group->getGroupName() . "]\n";
            }

            foreach (self::$permissionList as $key => $value) {
                $string .= "permission." . $key . " = '" . $group->getPermission($key) . "'\n";
            }

            $string .= "\n;List of Inheritances.\n";
            foreach ($group->getInherits() as $value) {
                $string .= "inherit[] = '" . $value->getGroupName() . "'\n";
            }

            $string .= "\n;List of Players.\n";
            if (!($group instanceof GuestGroup)) {
                // Guest group must not have any users saved.
                foreach ($group->getGroupUsers() as $value) {
                    if (!$value->isReadOnly()) {
                        $string .= "login[] = '" . $value->getLogin() . "'\n";
                    }
                }
            }
        }
        if ($this->config->fileName == null) {
            $file = "config/" . $this->storage->serverLogin . "_admins.ini";
        } else {
            $file = "config/" . $this->config->fileName;
        }

        if (!file_exists($file)) {
            if (touch($file) == false) {
                throw new \Exception("Writing the admingroups file at " . $file . " FAILED. perhaps not enough permissions for folder & file ?");
            }
        }
        if (!is_writable($file)) {
            throw new \Exception("Writing the admingroups file at " . $file . " FAILED. perhaps not enough permissions for folder & file ?");
        }
        $status = file_put_contents($file, $string, LOCK_EX);

        if ($status === false) {
            throw new \Exception("Writing the admingroups file at " . $file . " FAILED. perhaps not enough permissions for folder & file ?");
        }
    }

    /**
     * Does the player has this permission
     * Usage:
     *       if (AdminGroups::hasPermission($login, Permission::server_admin) {
     *          // do something
     *      }
     *
     * @param string $login The login of the player
     * @param string $permissionName The permission name
     *
     * @return boolean Has the player this permission
     */
    public static function hasPermission($login, $permissionName)
    {

        self::$permissionList[$permissionName] = true;

        //Is this player an Admin
        if (isset(self::$admins[$login])) {
            //Does he has this permission
            return self::$admins[$login]->hasPermission($permissionName);
        } else {
            return self::$guestGroup->hasPermission($permissionName);
        }
    }

    /**
     * Returns group name for login, returns "player" for non-admins.
     *
     * @param string $login
     *
     * @return string containing "Player" for non-admingroup memebers, othervice returns the admin group name.
     */
    public static function getGroupName($login)
    {
        $grpName = "Player";

        $admin = self::getAdmin($login);
        if ($admin !== null) {
            $grpName = $admin->getGroup()->getGroupName();
        }

        return $grpName;
    }

    /**
     * Gets permission, and send error message on fail.
     *
     * @param string $login
     * @param string $permissionName
     *
     * @return boolean
     */
    public function getPermission($login, $permissionName)
    {
        //Is this player an Admin
        if (isset(self::$admins[$login])) {
            //Does he has this permission
            if (self::$admins[$login]->hasPermission($permissionName)) {
                return true;
            } else {
                $this->eXpChatSendServerMessage($this->msg_neeMorPerm, $login);
            }
        } elseif (self::$guestGroup->hasPermission($permissionName)) {
            return true;
        } else {
            $this->eXpChatSendServerMessage($this->msg_needBeAdmin, $login);

            return false;
        }
    }

    /**
     * Gets admin
     *
     * @param string $login
     *
     * @return Admin|Null
     */
    public static function getAdmin($login)
    {
        return isset(self::$admins[$login]) ? self::$admins[$login] : null;
    }

    /**
     * Is the player in any admin groups
     *
     * @param string $login
     *
     * @return boolean
     */
    public static function isInList($login)
    {
        if (isset(self::$admins[$login])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * returns the no permission message
     *
     * @return string
     */
    public static function getNoPermissionMsg()
    {
        return self::$txt_noPermissionMsg;
    }

    /**
     * Add an admin command
     *
     * usage at plugin:
     *
     * $cmd = AdminGroups::addAdminCommand('player kick', $this, 'kick', Permission::player_kick); //
     * $cmd->setHelp('kick the player from the server');
     * $cmd->setHelpMore('$w/admin player kick #login$z will kick the player from the server. A kicked player may
     * return to the server whanever he desires.');
     * $cmd->setMinParam(1);
     * AdminGroups::addAlias($cmd, "kick"); // xaseco & fast
     * AdminGroups::addAlias($cmd, "boot"); // just example on how to add multiple aliases
     *
     * function kick($fromLogin, $params)
     * }
     *
     * @param String $cmd The string of the command
     * @param Object $class The object to call
     * @param String $function The name of the function to call
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\Permissions $permission The permission level needed to do the
     *                                                                        command. If null then an admin from any
     *                                                                        group can do the command
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd The AdminCmd object
     */
    public static function addAdminCommand($cmd, $class, $function, $permission)
    {
        $comand = new AdminCmd($cmd, $class, $function, $permission);

        self::addCommand($comand, $cmd);
        self::$commandsList[$cmd] = $comand;
        if ($permission != null) {
            self::$permissionList[$permission] = true;
        }

        self::$instance->saveFile();

        return $comand;
    }

    /**
     * Adds an alias to an existing command
     *
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $adminCmd The command object to which we want to add an
     *                                                                   alias
     * @param string $cmd The new command
     */
    public static function addAlias(AdminCmd $adminCmd, $cmd)
    {
        self::addCommand($adminCmd, $cmd);
        $adminCmd->addAlias($cmd);
    }

    /**
     * Adds a very short alias to an existing command.
     * Very short aliases doesen't require /admin
     * They work the same way other commands works.
     *
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $adminCmd
     * @param type $cmd
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    public function addShortAlias(AdminCmd $adminCmd, $cmd)
    {
        $adminCmd->addAlias($cmd);

        //We explode the command to sub commands
        $cmdArray = explode(" ", strtolower($cmd));

        //The first element is the main element
        $ccmd = array_shift($cmdArray);

        $this->registerChatCommand($ccmd, "shortAdminCmd", -1, true);


        //If the command is new we set a value to it. We will change it later
        if (!isset(self::$shortCommands[$ccmd])) {
            self::$shortCommands[$ccmd] = null;
        }

        //We apply the new command to the array
        self::$shortCommands[$ccmd] = self::addRecursive(self::$shortCommands[$ccmd], $cmdArray, $adminCmd);

        //We return the command object
        return $adminCmd;
    }

    /**
     * Adds the command
     *
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $adminCmd
     * @param type $cmd
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    private static function addCommand(AdminCmd $adminCmd, $cmd)
    {
        //We explode the command to sub commands
        $cmdArray = explode(" ", strtolower($cmd));

        //The first element is the main element
        $ccmd = array_shift($cmdArray);

        //If the command is new we set a value to it. We will change it later
        if (!isset(self::$commands[$ccmd])) {
            self::$commands[$ccmd] = null;
        }

        //We apply the new command to the array
        self::$commands[$ccmd] = self::addRecursive(self::$commands[$ccmd], $cmdArray, $adminCmd);

        //We return the command object
        return $adminCmd;
    }

    /**
     *  remove admin command, usually used eXpOnUnload
     *
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $adminCmd
     */
    public static function removeAdminCommand(AdminCmd $adminCmd)
    {
        unset(self::$commandsList[$adminCmd->getCmd()]);
        $adminCmd->deactivate();
    }

    /**
     * Un register a chat command by it's alias.
     *
     * @param string $command
     */
    public static function removeShortAllias($command)
    {
        self::getInstance()->unregisterChatCommand($command);
    }

    /**
     * Add a command to the command tree.
     *
     * @param string[] $commands The commands tree
     * @param string[] $cmdArray The command to add
     * @param AdminCmd $comandObj The command to add.
     *
     * @return mixed
     */
    private static function addRecursive($commands, $cmdArray, $comandObj)
    {
        //If we have finished looking all the sub commands we have finished our work.
        if (empty($cmdArray) || !is_array($cmdArray)) {
            return $comandObj;
        } else {
            //Recovering the main command
            $cmd = array_shift($cmdArray);
            if (!isset($commands[$cmd])) {
                $commands[$cmd] = null;
            }
            //Continue to add recursively
            $commands[$cmd] = self::addRecursive($commands[$cmd], $cmdArray, $comandObj);

            return $commands;
        }
    }

    /**
     * Add title to a certain permission.
     *
     * @param                                               $permissionName
     * @param \ManiaLivePlugins\eXpansion\Core\i18n\Message $msg
     */
    public static function addPermissionTitleMessage($permissionName, \ManiaLivePlugins\eXpansion\Core\i18n\Message $msg)
    {
        self::$txt_permissions[$permissionName] = $msg;
    }

    /**
     * Get the title of a permission
     *
     * @param String $permissionName The name of the permission to get the message for
     *
     * @return \ManiaLivePlugins\eXpansion\Core\i18n\Message|string
     */
    public static function getPermissionTitleMessage($permissionName)
    {
        return isset(self::$txt_permissions[$permissionName]) ? self::$txt_permissions[$permissionName] : $permissionName;
    }

    /**
     * Invoke admin Chat command
     *
     * usage at plugin:
     *  $ag = AdminGroup::getInstance();
     *  $ag->adminCmd($login, "remove this"); // works like $login player would write /adm remove this
     *
     * @param string $login
     * @param string $params
     */
    public function adminCmd($login, $params = "", $cmds = array(), $errors = true)
    {
        if (empty($cmds)) {
            $cmds = self::$commands;
        }

        // $args = explode(" ", $params);

        $matches = array();
        preg_match_all('/(?!\\\\)"((?:\\\\"|[^"])+)"?|([^\s]+)/', $params, $matches);
        $args = array_map(function ($str, $word) {
            $temp = str_replace('\"', '"', $str != '' ? $str : $word);
            if ($temp == '""') {
                return "";
            }

            return $temp;
        }, $matches[1], $matches[2]);

        //Lets see if the command is correct
        $arg = strtolower(array_shift($args));
        if (isset($cmds[$arg])) {
            $this->doAdminCmd($cmds[$arg], $args, $login);
        } else {
            if ($errors) {
                $this->eXpChatSendServerMessage($this->msg_cmdDontEx, $login);
            }
        }

    }

    /**
     * Executes an admin command. (will do permission checks as well)
     *
     * @param AdminCmd $commands
     * @param array $chats
     * @param string $login
     */
    private function doAdminCmd($commands, $chats, $login)
    {
        if (!is_array($commands)) {
            //We found the command
            if ($this->hasPermission($login, $commands->getPermission())) {
                $error = $commands->cmd($login, $chats);
                if ($error != '') {
                    $this->eXpChatSendServerMessage(__('#admin_error#' . $error, $login), $login);
                }
            } else {
                $this->eXpChatSendServerMessage($this->msg_neeMorPerm, $login);
            }
        } else {
            if (isset($chats[0])) {
                $chat = strtolower(array_shift($chats));
                if (is_array($commands) && isset($commands[$chat])) {
                    $this->doAdminCmd($commands[$chat], $chats, $login);
                } else {
                    $this->eXpChatSendServerMessage($this->msg_cmdDontEx, $login);
                }
            } else {
                $this->eXpChatSendServerMessage($this->msg_cmdDontEx, $login);
            }
        }
    }

    public function shortAdminCmd($login, $params = "")
    {

    }

    /**
     * @inheritdoc
     */
    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {
        if (!$isRegistredCmd || strpos($text, "/admin") !== false || strpos($text, "/adm") !== false) {
            return;
        }

        $text = substr($text, 1);

        $this->adminCmd($login, $text, self::$shortCommands, false);
    }

    /**
     * Adds a player to a group
     *
     * @param String $login The login of the player which makes the change
     * @param Group $group Group of where to add the player
     * @param String $login2 Login which is to be added to the group
     */
    public function addToGroup($login, Group $group, $login2)
    {

        if (isset(self::$admins[$login2])) {
            $this->eXpChatSendServerMessage($this->msg_aInGroup, $login, array($login2, $group->getGroupName()));
        } else {
            $this->reLoadAdmins();


            $success = false;
            foreach (self::$groupList as $id => $groupp) {
                if ($groupp->getGroupName() === $group->getGroupName()) {
                    $admin = new Admin($login2, $groupp);
                    $admin->setAllowedIP($this->getAllowedIp($login));
                    $groupp->addAdmin($admin);
                    self::$admins[$login2] = $admin;
                    $this->saveFile();
                    $success = true;
                    Dispatcher::dispatch(new Events\Event(Events\Event::ON_ADMIN_NEW, $login2));
                    $this->eXpChatSendServerMessage($this->msg_paddSuc, null, array($login2, $group->getGroupName()));
                    break;
                }
            }
            if ($success == false) {
                $this->eXpChatSendServerMessage($this->msg_paddFai, null, array($login2, $group->getGroupName()));
            }
        }
    }

    /**
     * Removes a player from a group
     *
     * @param string $login the login who performs removal
     * @param Group $group group where to remove
     * @param Admin $admin admin to remove
     */
    public function removeFromGroup($login, Group $group, Admin $admin)
    {

        if (isset(self::$admins[$login]) && $admin->getLogin() == $login) {
            $this->eXpChatSendServerMessage($this->msg_premoveSelf, $login, array($login));
        } else {
            if ($admin->isReadOnly()) {
                $this->eXpChatSendServerMessage($this->msg_removeMlAdmin, $login, array($admin->getLogin()));
            } else {
                if (isset(self::$admins[$login]) && $group->removeAdmin($admin->getLogin())) {
                    $this->reLoadAdmins();

                    foreach (self::$groupList as $id => $groupp) {
                        if ($groupp->getGroupName() === $group->getGroupName()) {
                            $groupp->removeAdmin($admin->getLogin());
                            break;
                        }
                    }
                    unset(self::$admins[$admin->getLogin()]);
                    Dispatcher::dispatch(new Events\Event(Events\Event::ON_ADMIN_REMOVED, $admin->getLogin()));
                    $this->eXpChatSendServerMessage($this->msg_pRemoveSuc, null, array($admin->getLogin(), $group->getGroupName()));

                    $this->saveFile();
                } else {
                    $this->eXpChatSendServerMessage($this->msg_pRemoveFa, $login, array($admin->getLogin()));
                }
            }
        }
    }

    /**
     * Create a new group
     *
     * @param string $login2 The login if the user creating the group.
     * @param string $groupName The name of the new group
     *
     * @throws \Exception
     */
    public function addGroup($login2, $groupName)
    {
        // First be sure the lis ot groups we have is up to date.
        $this->reLoadAdmins();
        self::$groupList[] = new Group($this->escapeSpecials($groupName), false);
        $this->saveFile();
    }

    /**
     * Removes a group.
     *
     * @param string $login The login if the user creating the group.
     * @param Group $group The group to be deleted
     *
     * @throws \Exception
     */
    public function removeGroup($login, $group)
    {
        if ($group->isMaster()) {
            // The master group can't be removed.
            $this->eXpChatSendServerMessage($this->msg_masterMasterE, $login);
            return;
        }

        // First be sure the lis ot groups we have is up to date.
        $this->reLoadAdmins();

        $i = 0;
        $groupName = $group->getGroupName();
        while ($i < sizeof(self::$groupList)) {
            $group = self::$groupList[$i];
            if ($group->getGroupName() === $groupName) {
                foreach ($group->getGroupUsers() as $user) {
                    unset(self::$admins[$user->getLogin()]);
                }
                while (isset(self::$groupList[$i + 1])) {
                    self::$groupList[$i] = self::$groupList[$i + 1];
                    $i++;
                }
                unset(self::$groupList[$i]);
            }
            $i++;
        }

        $this->saveFile();
    }

    /**
     * Change the permissions of a group
     *
     * @param String $login
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\Group $group
     * @param array $newPermissions The list of new permissions.
     */
    public function changePermissionOfGroup($login, Group $group, array $newPermissions)
    {
        if ($group->isMaster()) {
            $this->eXpChatSendServerMessage($this->msg_masterMasterE, $login);
        } else {
            $this->reLoadAdmins();

            foreach (self::$groupList as $id => $groupp) {
                if ($groupp->getGroupName() === $group->getGroupName()) {
                    foreach ($newPermissions as $key => $val) {
                        $groupp->addPermission($key, $val);
                    }
                }
            }

            $this->saveFile();
        }
    }

    /**
     * Change the permissions of a group
     *
     * @param String $login
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\Group $group
     * @param array $newPermissions The list of new permissions.
     */
    public function changeInheritanceOfGroup($login, Group $group, array $newHeritances)
    {
        if ($group->isMaster()) {
            $this->eXpChatSendServerMessage($this->msg_masterMasterE, $login);
        } else {
            $this->reLoadAdmins();

            foreach (self::$groupList as $id => $groupp) {
                if ($groupp->getGroupName() === $group->getGroupName()) {
                    $groupp->resetInherits();
                    foreach ($newHeritances as $val) {
                        $groupp->addInherits($val);
                    }
                }
            }
            $this->saveFile();
        }
    }

    /**
     *
     * @param string $string
     *
     * @return boolean
     */
    private function entryCheck($string)
    {
        $upper = strtoupper($string);
        if ($upper == "FALSE" || $string == "0" || $upper == "NO" || $upper == strtoupper(self::NO_PERMISSION)) {
            return self::NO_PERMISSION;
        } else {
            if ($upper == "TRUE" || $string == "1" || $upper == "YES" || $upper == strtoupper(self::HAVE_PERMISSION)) {
                return self::HAVE_PERMISSION;
            } else {
                return self::UNKNOWN_PERMISSION;
            }
        }
    }

    /**
     * Returns the list of all admin commands
     *
     * @return AdminCmd[]
     */
    public function getAdminCommands()
    {
        return self::$commandsList;
    }

    /**
     * Return the list of all admins and capabilities
     *
     * @return Admin[]
     */
    public function getAdmins()
    {
        return self::$admins;
    }

    /**
     * Returns the list of all Groups
     *
     * @return Group[]
     */
    public function getGroupList()
    {
        return self::$groupList;
    }

    /**
     * Return the list of all the permissions
     *
     * @return array
     */
    public function getPermissionList()
    {
        return self::$permissionList;
    }

    /**
     * Gets admin logins by permission
     *
     * @param String $permissionName
     *
     * @return String[String]
     */
    public static function getAdminsByPermission($permissionName)
    {
        $admins = array();
        foreach (self::$groupList as $group) {
            if ($group->hasPermission($permissionName)) {
                foreach ($group->getGroupUsers() as $admin) {
                    $login = $admin->getLogin();
                    $admins[$login] = $login;
                }
            }
        }

        return $admins;
    }

    /**
     * Return the list of all admins in manialive style
     *
     * usage:
     *  $this->registerChatCommand("test", "test",0 , true, AdminGroups::get());
     *
     *
     * @return array of admins
     */
    public function get()
    {
        $admins = array_keys(self::$admins);
        if (sizeof($admins) == 0) {
            $admins[] = false;
        }

        return $admins;
    }

    /**
     *  Create Management window for groups
     *
     * @param string $login
     */
    public function windowGroups($login)
    {
        \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::Create($login);
        $window->setTitle(__(self::$txt_groupsTitle, $login));
        $window->setSize(110, 100);
        $window->centerOnScreen();
        $window->show();
    }

    /**
     * Display he window with help about all commands.
     *
     * @param string $login
     */
    public function windowHelp($login)
    {
        \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Help::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Help::Create($login);
        $window->setTitle(__(self::$txt_helpTitle, $login));
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }

    /**
     * @inheritdoc
     */
    public function eXpOnUnload()
    {
        self::$admins = array();
        self::$commands = array();
        self::$commandsList = array();
        self::$groupList = array();
        self::$permissionList = array();
    }

    /**
     * escape specials for the admin goups save.
     *
     * @param string $text The text to escape
     *
     * @return string The escaped string
     */
    public function escapeSpecials($text)
    {
        $text = str_replace("'", "", $text);
        $text = str_replace('"', "", $text);

        return $text;
    }
}