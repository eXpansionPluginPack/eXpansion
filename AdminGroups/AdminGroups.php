<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 *  
 * @author oliver
 */
class AdminGroups extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /**
     * The instance of the runing AdminGroup plugin
     *
     * @var \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups 
     */
    static private $instance;

    /**
     * Get currect running instance of the singleton
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups 
     */
    public static function getInstance() {
        return self::$instance;
    }

    /*
     * List of all the admins(of any group)
     */

    static private $admins = array();

    /**
     * The commands array
     * @var AdminCmd[] 
     */
    static private $commands = array();

    /**
     * @var array List of All commans
     * Used for the Help
     */
    static private $commandsList = array();

    /**
     * List of all permissions
     * @var type 
     */
    static private $permissionList = array();

    /**
     * List of all Groups
     */
    static private $groupList = array();

    /**
     * The Configuration
     * @var Config
     */
    private $config;
	
	/**
	 * When was the configuration file loaded? 
	 * @var type 
	 */
	private $readTime;

    public function exp_onInit() {
        parent::exp_onInit();
        self::$instance = $this;

        //Recovering the configuration 
        $this->config = Config::getInstance();

       $this->loadAdmins();
    }

    public function exp_onLoad() {
        parent::exp_onLoad();
        //No idea if needed, I think not need to check
        // $this->enableDedicatedEvents();  
        //Registering public functions
        $this->setPublicMethod('adminCmd');
        $this->setPublicMethod('getPermission');

        //Registering the admin chat comman with a lot of parameters              
        $this->registerChatCommand('admin', "adminCmd", -1, true, $this->get());

        $this->addAdminCommand('groups', $this, "windowGroups", null);
    }
	
	public function reLoadAdmins(){
		$time = filemtime("config/" . $this->config->config_file);
		
		if($time > $this->readTime){
			$this->loadAdmins();	
		}
	}
	
	/**
	 * Loads the Admin configuration File. And will reset everything
	 */
	public function loadAdmins(){
		//Reseting settings
		self::$admins = array();
		self::$groupList = array();
		self::$permissionList = array();
		
		 //Recovering the admin groups
        $values = \parse_ini_file("config/" . $this->config->config_file, true);
		
		//Save the read Time
		$this->readTime = time();
		
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
                    $this->parseGroup($param[1], $value);
                }
            }
        }
	}

    /**
     * Parsing a group
     * 
     * @param string $groupName The groups name
     * @param array $value
     */
    private function ParseGroup($groupName, $value) {

        $group = new Group($groupName, false);

        //Settings and Permissions
        foreach ($value as $key => $val) {
            $param = explode(".", $key);

            if ($param[0] == 'permission') {
                self::$permissionList[$param[1]] = true;
                $group->addPermission($param[1], $this->stringToBool($val));
            } elseif ($param[0] == 'settings') {
                //
            }
        }

        //Lets get the players
        if (isset($value["login"])) {
            foreach ($value["login"] as $login) {
                $admin = new Admin($login, $group);
                if (!isset(self::$admins[$login])) {
                    self::$admins[$login] = $admin;
                    $group->addAdmin($admin);
                }
            }
        }
        self::$groupList[] = $group;
    }

    /**
     * Parsing the Master group
     * 
     * @param string $groupName The groups name
     * @param array $permissions
     */
    private function parseMaster($groupName, $permissions) {
        //Settings and Permissions
        foreach ($permissions as $key => $val) {
            $param = explode(".", $key);

            if ($param[0] == 'permission') {
                self::$permissionList[$param[1]] = true;
            }
        }

        $group = new Group($groupName, true);
        if (isset($permissions["login"])) {
            foreach ($permissions["login"] as $login) {
                $admin = new Admin($login, $group);
                $group->addAdmin($admin);
                self::$admins[$login] = $admin;
            }
        }
        self::$groupList[] = $group;
    }

	public function saveFile(){
		$string = "";
		
		foreach(self::$groupList as $group){
			
			if($group->isMaster()){
				$string .= ";MasterAdmin is a special group that has all permissions. \n";
				$string .= ";No need to specify permissions. But we will to show all permissions\n";
				$string .= "\n\n[MasterAdmin: ".$group->getGroupName()."]\n";
			}else{
				$string .= "\n\n[Group: ".$group->getGroupName()."]\n";
			}
			
			foreach (self::$permissionList as $key => $value) {
				$bool = $group->hasPermission($key)? "true" : "false";
				$string .= "permission.restart".$key." = '".$bool."'\n";
			}
			
			$string.="\n;List of Players.\n";
			foreach ($group->getGroupUsers() as $value) {
				$string .= "login[] = '".$value->getLogin()."'\n";
			}
		}
		
		file_put_contents("config/" . $this->config->config_file, $string);
	}
	
    /**
     * Does the player has this permission
     * 
     * @param string $login The login of the player
     * @param string $permissionName The permission name
     * @return boolean Has the player this permission
     */
    static public function hasPermission($login, $permissionName) {

        self::$permissionList[$permissionName] = true;

        //Is this player an Admin
        if (isset(self::$admins[$login])) {
            //Does he has this permission
            return self::$admins[$login]->hasPermission($permissionName);
        } else {
            return false;
        }
    }

    /**
     * 
     * @param string $login
     * @param string $permissionName
     * @return boolean
     */
    public function getPermission($login, $permissionName) {
        //Is this player an Admin
        if (isset(self::$admins[$login])) {
            //Does he has this permission
            if (self::$admins[$login]->hasPermission($permissionName)) {
                return true;
            } else {
                $this->exp_chatSendServerMessage($this->config->msg->msg_noPermissionMsg, $login);
            }
        } else {
            $this->exp_chatSendServerMessage("#admin_action#You need to be an Admin to use that command", $login);
            return false;
        }
    }

    public static function getAdmin($login) {
        return isset(self::$admins[$login]) ? self::$admins[$login] : null;
    }

    /**
     * Is the player in any admin groups
     * 
     * @param string $login
     * @return boolean
     */
    static public function isInList($login) {
        if (isset(self::$admins[$login]))
            return true;
        else
            return false;
    }

    /**
     * returns the no permission message
     * @return string
     */
    static public function GetnoPermissionMsg() {
        return $this->config->msg_noPermissionMsg;
    }

    /**
     * Add an admin command
     * 
     * @param string $cmd The string of the command
     * @param Object $class The object to call
     * @param string $function The name of the function to call
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\Permissions $permission The permission level needed to do the command.
     * 		If null then an admin from any group can do the command
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd The AdminCmd object
     */
    static public function addAdminCommand($cmd, $class, $function, $permission) {
        $comand = new AdminCmd($cmd, $class, $function, $permission);

        self::addCommand($comand, $cmd);
        self::$commandsList[] = $cmd;
        return $comand;
    }

    /**
     * Adds an alias to an existing command
     * 
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $adminCmd The command object to which we want to add an alias
     * @param string $cmd The new command
     */
    static public function addAlias(AdminCmd $adminCmd, $cmd) {
        self::addCommand($adminCmd, $cmd);
        $adminCmd->addAlias($cmd);
        self::$commandsList[] = $cmd;
    }

    /**
     * Adds the command 
     * 
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $adminCmd
     * @param type $cmd
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    static private function addCommand(AdminCmd $adminCmd, $cmd) {
        //We explode the command to sub commands
        $cmdArray = explode(" ", strtolower($cmd));

        //The first element is the main element
        $ccmd = array_shift($cmdArray);

        //If the command is new we set a value to it. We will change it later
        if (!isset(self::$commands[$ccmd]))
            self::$commands[$ccmd] = null;

        //We apply the new command to the array
        self::$commands[$ccmd] = self::addRecursive(self::$commands[$ccmd], $cmdArray, $adminCmd);

        //We return the command object
        return $adminCmd;
    }

    static private function addRecursive($commands, $cmdArray, $comandObj) {
        //If we have finished looking all the sub commands we have finished our work.
        if (empty($cmdArray) || !is_array($cmdArray))
            return $comandObj;
        else {
            //Recovering the main command
            $cmd = array_shift($cmdArray);
            if (!isset($commands[$cmd]))
                $commands[$cmd] = null;
            //Continue to add recursively
            $commands[$cmd] = self::addRecursive($commands[$cmd], $cmdArray, $comandObj);
            return $commands;
        }
    }

    /**
     * Chat command
     * @param string $login 
     * @param string $args
     */
    public function adminCmd($login, $args) {

        /** @var array */
        $args = explode(" ", $args);

        //First lets check if player is an admin
        if (!isset(self::$admins[$login])) {
            $this->exp_chatSendServerMessage("#admin_action#You need to be an Admin to use that command", $login);
        } else {
            //Lets see if the command is correct
            $arg = strtolower(array_shift($args));
            if (isset(self::$commands[$arg])) {
                $this->doAdminCmd(self::$commands[$arg], $args, $login);
            } else {
                $this->exp_chatSendServerMessage("#admin_action#The command don't exist", $login);
            }
        }
    }

    /**
     * 
     * @param AdminCmd $commands
     * @param array $chats
     * @param string $login
     */
    private function doAdminCmd($commands, $chats, $login) {
        if (!is_array($commands)) {
            //We found the command
            if ($this->hasPermission($login, $commands->getPermission())) {
                $error = $commands->cmd($login, $chats);               
                if ($error != '')
                    $this->exp_chatSendServerMessage('#admin_error#' . $error, $login);
            }else {
                $this->exp_chatSendServerMessage("#admin_action#You don't have the permission to use that admin command", $login);
            }
        } else if (isset($chats[0])) {
            $chat = strtolower(array_shift($chats));
            if (is_array($commands) && isset($commands[$chat])) {
                $this->doAdminCmd($commands[$chat], $chats, $login);
            } else {
                $this->exp_chatSendServerMessage("#admin_action#The command don't exist", $login);
            }
        } else {
            $this->exp_chatSendServerMessage("#admin_action#The command don't exist", $login);
        }
    }

	/**
	 * Adds a player to a group
	 * 
	 * @param String The login of the player who makes the changes
	 * @param \ManiaLivePlugins\eXpansion\AdminGroups\Group $group The group to which the player needs to be added
	 * @param String $login2 The player to add to the group
	 */
	public function addToGroup($login, Group $group, $login2){
		if (isset(self::$admins[$login2])){
			$this->exp_chatSendServerMessage('#admin_error#Player "%1" is already in a group %2. Remove him first');
		}else{
			$this->reLoadAdmins();
			
			self::$admins[$login2]=true;
			$group->addAdmin(new Admin($login2, $group));
			$this->exp_chatSendServerMessage('#admin_action#Player "%1" has been added to admin group #variable#%2.');
			
			$this->saveFile();
		}
	}
    
	/**
     * Removes a player from a group
	 * 
     * @param string $login
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\Group $group
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\Admin $admin
     */
    public function removeFromGroup($login, Group $group, Admin $admin) {
         if (isset(self::$admins[$login]) && $admin->getLogin() == $login) {
            $this->exp_chatSendServerMessage('#admin_error#Your are : "%1" You can\'t remove yourself from a group', $login);
        }else if (isset(self::$admins[$login]) && $group->removeAdmin($admin->getLogin())) {
			$this->reLoadAdmins();
			
			$group->removeAdmin($admin->getLogin());
            unset(self::$admins[$login]);
            $this->exp_chatSendServerMessage('#admin_error#Player : "%1" Has been taken out admin group %2');
			
			$this->saveFile();
        } else {
            $this->exp_chatSendServerMessage('#admin_error#Player : "%1" isn\'t in the grop', $login);
        }
    }
	
	public function addGroup($login2, $groupName){
		$this->reLoadAdmins();
		self::$groupList[] = new Group($groupName, false);
		$this->saveFile();
	}
	
	/**
	 * Change the permissions of a group
	 * 
	 * @param String $login
	 * @param \ManiaLivePlugins\eXpansion\AdminGroups\Group $group
	 * @param array $newPermissions The list of new permissions.
	 */
	public function changePermissionOfGroup($login, Group $group, array $newPermissions){
		if($group->isMaster()){
			$this->exp_chatSendServerMessage('#admin_error#Master Admins has all rights. You can\'t change that!');
		} else{
			$this->reLoadAdmins();
			
			foreach ($newPermissions as $key => $val) {
				$group->addPermission($key, $val);
			}
			
			$this->saveFile();
		}
	}

    /**
     * 
     * @param string $string
     * @return boolean
     */
    private function stringToBool($string) {
        if (strtoupper($string) == "FALSE" || $string == "0" || strtoupper($string) == "NO" || empty($string))
            return false;
        return true;
    }

    /**
     * Returns the list of all admin commands
     * @return array
     */
    public function getAdminCommands() {
        return self::$commandsList;
    }

    /**
     * Return the list of all admins and capabilities
     * @return array
     */
    public function getAdmins() {
        return self::$admins;
    }

    /**
     * Returns the list of all Groups
     * @return type
     */
    public function getGroupList() {
        return self::$groupList;
    }

    /**
     * Return the list of all the permissions
     * @return type
     */
    public function getPermissionList() {
        return self::$permissionList;
    }

    /**
     * Return the list of all admins in manialive style
     * @return array of admins
     */
    public function get() {
        return array_keys(self::$admins);
    }

    /**
     *  Create Management window for groups
     * @param string $login
     */
    public function windowGroups($login) {
        \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::Create($login);
        $window->setTitle(__('Admin Groups'));
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }
	
	public function onUnload() {
		parent::onUnload();
		self::$admins = array();
		self::$commands = array();
		self::$commandsList = array();
		self::$groupList = array();
		self::$permissionList = array();
	}

}