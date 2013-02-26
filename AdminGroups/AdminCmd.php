<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of AdminCmd
 *
 * @author oliver
 */
class AdminCmd {

	private $class;
	private $function;
	private $cmd;
	private $aliases = array();
	private $permission;
	private $help;
	private $helpMore;
	private $checker = array();
	private $minParam = 0;

	function __construct($cmd, $class, $function, $permission) {
		$this->cmd = $cmd;
		$this->class = $class;
		$this->function = $function;
		$this->permission = $permission;
	}
        /**
         * 
         * @param string $login
         * @param array $param
         * @return string
         */
	public function cmd($login, $param) {
		if (method_exists($this->class, $this->function)) {

			/*
			 * Checking parameters
			 */
			
			//Parameter count
			if (sizeof($param) < $this->minParam)
				return __("This command expect at least one parameter");
			
			//All Parameters.
			foreach($this->checker as $cmd_num => $checkers){
				if(isset($param[$cmd_num]) && is_array($checkers)){
					foreach($checkers as $check){
						if(!$check->check($param[$cmd_num]))
							return $check->getErrorMsg();
					}
				}
			}
                        // add login to the first element of the params array;
                        
			call_user_func_array(array($this->class, $this->function), array($login, $param));
			return "";
		}
	}

	public function getCmd() {
		return $this->cmd;
	}

	public function getMinParam() {
		return $this->minParam;
	}

	public function setMinParam($minParam) {
		$this->minParam = $minParam;
	}

	public function addchecker($numParam, types\absChecker $check) {
		$this->checker[$numParam-1][] = $check;
	}

	public function getPermission() {
		return $this->permission;
	}

	public function getHelp() {
		return $this->help;
	}

	public function setHelp($help) {
		$this->help = $help;
	}

	public function getHelpMore() {
		return $this->helpMore;
	}

	public function setHelpMore($helpMore) {
		$this->helpMore = $helpMore;
	}

	public function addAlias($cmd) {
		$this->aliases[] = $cmd;
	}

}

?>
