<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

use ManiaLive\DedicatedApi\Connection;
use ManiaLive\Data\Storage;
use ManiaLive\Config\Loader;

/**
 *  
 * @author oliver
 */
class AdminGroups extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {
	
	/**
	* The instance of the runing AdminGroup plugin
	*/
	static private $instance;
	
	public static function getInstance(){
		return self::$instance;
	}
	
	/*
	 * List of all the admins(of any group)
	 */
	static private $admins;
	
	/**
	 * The commands array
	 */
	static private $commands;
	
	/**
	 * @var type List of All commans
	 * Used for the Help
	 */
	static private $commandsList = array();
	
	/**
	 * The Configuration
	 */
	private $config;


	public function exp_onInit() {
		parent::exp_onInit();
		self::$instance = $this;
		
		//Recovering the configuration 
		$this->config = Config::getInstance();
		
		//Recovering the admin groups
		$values = \parse_ini_file("config/" . $this->config->config_file, true);
		
		//reading the admin groups and settings
		foreach ($values as $key => $value) {
			//THe settings
			if ($key == 'Settings') {
				
			} else {
				$param = explode(": ", $key);
				
				//We have found a Admin group, lets see the permissions of 
				//the group and the players that is part of it
				if ($param[0] == 'Group')
					$this->parseGroup($param[1], $value);
			}
		}
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
	}
	
	/**
	 * Parsing a group
	 * 
	 * @param type $groupName The groups name
	 * @param type $value
	 */
	private function ParseGroup($groupName, $value) {
		//List of admins of this group
		$groupAdmins = array();
		
		//The list of this groups permissions
		$groupPermissions = array();
		
		//List of this groups settings (Un used for further usage)
		$groupSettings = array();

		//Settings and Permissions
		foreach ($value as $key => $val) {
			$param = explode(".", $key);

			if ($param[0] == 'permission') {
				$groupPermissions[$param[1]] = $this->stringToBool($val);
			} elseif ($param[0] == 'settings') {
				$groupSettings[$param[1]] = $val;
			}
		}

		//Lets get the players
		if (isset($value["login"])) {
			foreach ($value["login"] as $login){
				$admin = new Admin($login, $groupPermissions, $groupName);
				self::$admins[$login] = $admin;
				$groupAdmins[$login] = $admin;
			}
		}
	}

	/**
	 * Does the player has this permission
	 * 
	 * @param type $login The login of the player
	 * @param type $permissionName The permission name
	 * @return boolean Has the player this permission
	 */
	static public function hasPermission($login, $permissionName) {
		//Is this player an Admin
		if (isset(self::$admins[$login])){
			//Does he has this permission
			return self::$admins[$login]->hasPermission($permissionName);
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * @param type $login
	 * @param type $permissionName
	 * @return boolean
	 */
	public function getPermission($login, $permissionName){
		//Is this player an Admin
		print_r($login);
		if (isset(self::$admins[$login])){
			//Does he has this permission
			if(self::$admins[$login]->hasPermission($permissionName)){
				return true;
			}else{
				$this->exp_chatSendServerMessage($this->config->msg->msg_noPermissionMsg, $login);
			}
		} else {
			$this->exp_chatSendServerMessage($this->config->msg_needBeAdmin, $login);
			return false;
		}
	}
	
	/**
	 * Is the player in any admin groups
	 * 
	 * @param type $login
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
	 * @return type
	 */
	static public function GetnoPermissionMsg() {
		return $this->config->msg_noPermissionMsg;
	}

	/**
	 * Add an admin command
	 * 
	 * @param type $cmd The string of the command
	 * @param type $class The object to call
	 * @param type $function The name of the function to call
	 * @param type $permission The permission level needed to do the command.
	 *		If null then an admin from any group can do the command
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
	 * @param type $cmd The new command
	 */
	static public function addAlias(AdminCmd $adminCmd, $cmd){
		self::addCommand($adminCmd, $cmd);
		$adminCmd->addAlias($cmd);
	}
	
	/**
	 * Adds the command 
	 * 
	 * @param \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $adminCmd
	 * @param type $cmd
	 * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
	 */
	static private function addCommand(AdminCmd $adminCmd, $cmd){
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

	public function adminCmd() {
		$args = func_get_args();
		$login = array_shift($args);

		//First lets check if player is an admin
		if (!isset(self::$admins[$login])) {
			$this->exp_chatSendServerMessage($this->config->msg_needBeAdmin, $login);
		} else {
			//Lets see if the command is correct
			$arg = strtolower (array_shift($args));
			if (isset(self::$commands[$arg])) {
				$this->doAdminCmd(self::$commands[$arg], $args, $login);
			} else {
				$this->exp_chatSendServerMessage($this->config->msg_commandDonExist, $login);
			}
		}
	}

	private function doAdminCmd($commands, $chats, $login) {
		if (!is_array($commands)) {
			//We found the command
			if ($this->hasPermission($login, $commands->getPermission())){
				$error = $commands->cmd($login, $chats);
				if($error != '')
					$this->exp_chatSendServerMessage('%admina_action%'.$error, $login);
			}else {
				$this->exp_chatSendServerMessage($this->config->msg_noPermissionMsg, $login);
			}
		} else if (isset($chats[0])) {
			$chat = strtolower (array_shift($chats));
			if (is_array($commands) && isset($commands[$chat])) {
				$this->doAdminCmd($commands[$chat], $chats, $login);
			} else {
				$this->exp_chatSendServerMessage($this->config->msg_commandDonExist, $login);
			}
		} else {
			$this->exp_chatSendServerMessage($this->config->msg_commandDonExist, $login);
		}
	}

	private function stringToBool($string) {
		if (strtoupper($string) == "FALSE" || $string == "0" || strtoupper($string) == "NO" || empty($string))
			return false;
		return true;
	}
	
	/**
	 * Returns the list of all admin commands
	 * @return type
	 */
	public function getAdminCommands() {
		return self::$commandsList;
	}
	
	/**
	 * Return the list of all admins and capabilities
	 * @return type
	 */
	public function getAdmins() {
		return self::$admins;
	}
        
        /**
	 * Return the list of all admins in manialive style
	 * @return type
	 */
        public function get() {
            return array_keys(self::$admins);
        }


}