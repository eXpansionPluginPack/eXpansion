<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

/**
 * Description of AdminCmd
 *
 * @author oliver
 */
class AdminCmd
{

    private $class;

    /**
     * @var callable
     */
    private $function;

    /** @var string */
    private $cmd;

    /**
     * @var string[]
     */
    private $aliases = array();
    /**
     *
     * @var string
     */
    private $permission;

    /** @var string */
    private $help;

    /** @var string */
    private $helpMore;

    /** @var types\absChecker */
    private $checker = array();

    /** @var int */
    private $minParam = 0;

    /**
     * AdminCmd constructor.
     *
     * @param $cmd
     * @param $class
     * @param $function
     * @param $permission
     */
    public function __construct($cmd, $class, $function, $permission) {
        $this->cmd = $cmd;
        $this->class = $class;
        $this->function = $function;
        $this->permission = $permission;
    }

    /**
     *
     * @param string $login
     * @param array  $param
     *
     * @return string
     */
    public function cmd($login, $param) {
        if ($this->class != null && method_exists($this->class, $this->function)) {

            /*
             * Checking parameters
             */

            //Parameter count
            if (sizeof($param) < $this->minParam) {
                return __("This command expect at least one parameter");
            }

            //All Parameters.
            foreach ($this->checker as $cmd_num => $checkers) {
                if (isset($param[ $cmd_num ]) && is_array($checkers)) {
                    foreach ($checkers as $check) {
                        if (! $check->check($param[ $cmd_num ])) {
                            return $check->getErrorMsg();
                        }
                    }
                }
            }
            // add login to the first element of the params array;

            call_user_func_array(array($this->class, $this->function), array($login, $param));

            return "";
        }

        return AdminGroups::$txt_msg_cmdDontEx;
    }

    /**
     * getCmd()
     *
     * @return string
     */
    public function getCmd() {
        return $this->cmd;
    }

    /**
     * @return int
     */
    public function getMinParam() {
        return $this->minParam;
    }

    /**
     *
     * @param int $minParam
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    public function setMinParam($minParam) {
        $this->minParam = $minParam;

        return $this;
    }

    /**
     *
     * @param int                                                      $numParam
     * @param \ManiaLivePlugins\eXpansion\AdminGroups\types\absChecker $check
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    public function addchecker($numParam, types\absChecker $check) {
        $this->checker[ $numParam - 1 ][] = $check;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermission() {
        return $this->permission;
    }

    /**
     *
     * @return string
     */
    public function getHelp() {
        return $this->help;
    }

    /**
     *
     * @param string $help
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    public function setHelp($help) {
        $this->help = $help;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getHelpMore() {
        return $this->helpMore;
    }

    /**
     *
     * @param string $helpMore
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    public function setHelpMore($helpMore) {
        $this->helpMore = $helpMore;

        return $this;
    }

    /**
     *
     * @param string $line
     *
     * @return \ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd
     */
    public function addLineHelpMore($line) {
        if ($this->helpMore == null) {
            $this->helpMore = $line;
        }
        else {
            $this->helpMore .= "\n" . $line;
        }

        return $this;
    }

    public function addAlias($cmd) {
        $this->aliases[] = $cmd;
    }

    public function getAliases() {
        return $this->aliases;
    }

    public function deactivate() {
        $this->class = null;
    }

}
