<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\AutoUpdate\Gui\Windows\UpdateProgress;
use ManiaLivePlugins\eXpansion\AutoUpdate\Structures\Repo;
use ManiaLivePlugins\eXpansion\Core\ParalelExecution;

/**
 * Auto update will check for updates and will update eXpansion if asked
 *
 * @author Petri & oliverde8
 */
class AutoUpdate extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    /**
     * Configuration of eXpansion
     *
     * @var Config
     */
    private $config;

    /**
     * Currently on going git updates or checks
     *
     * @var boolean[]
     */
    private $onGoing = false;

    /**
     * The login of the player that started the currently running steps
     *
     * @var String
     */
    private $currentLogin;

    public function eXpOnLoad()
    {

    }

    public function eXpOnReady()
    {
        $adm = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();

        $adm->addAdminCommand("update", $this, "autoUpdate", "server_update");
        $adm->addAdminCommand("check", $this, "checkUpdate", "server_update");

        $this->config = Config::getInstance();
        $this->enableDedicatedEvents();
    }

    /**
     * Will check if updates are necessary.
     */
    function checkUpdate()
    {
        $AdminGroups = AdminGroups::getInstance();

        //If on going updates cancel !!
        if ($this->onGoing) {
            $msg = "#admin_error#An update or check for update is already under way!";
            $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, $msg);

            return;
        }

        $this->onGoing = true;

        if ($this->config->useGit) {
            $cmds = array(PHP_BINARY . ' composer.phar update --prefer-source --no-interaction --dry-run');
        } else {
            $cmds = array(PHP_BINARY . ' composer.phar update --prefer-dist --no-interaction --dry-run');
        }

        $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, '#admin_action#[#variable#AutoUpdate#admin_action#] Checking updates for #variable#eXpansion & Components');

        $exec = new ParalelExecution($cmds, array($this, 'checkExecuted'), 'eXpansion_update_check');
        $exec->setValue('login', $this->currentLogin);
        $exec->start();
    }

    /**
     * Handles the results of one of the update steps. and starts next step.
     *
     * @param ParalelExecution $paralelExec The parallel execution utility
     * @param string[] $results The results of the previous steps execution
     * @param int $ret The value returned from the previous
     */
    public function checkExecuted($paralelExec, $results, $ret = 1)
    {
        $AdminGroups = AdminGroups::getInstance();

        if ($ret != 0) {
            $this->console('[eXpansion:AutoUpdate]Error while checking for updates eXpansion !!');
            $this->console($results);
            \ManiaLivePlugins\eXpansion\Gui\Gui::showError($results, AdminGroups::getAdminsByPermission(Permission::SERVER_UPDATE));
            $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, '#admin_error#Error while checking for updates of #variable#eXpansion & Components !!');
        } else {
            if ($this->arrayContainsText('Nothing to install or update', $results)) {
                $this->console('[eXpansion:AutoUpdate]eXpansion & Components are up to date');
                $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, '#vote_success#eXpansion & Components are up to date!');
            } else {
                $this->console('[eXpansion:AutoUpdate]eXpansion needs updating!!');
                $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, '#admin_error#eXpansion needs updating!');
            }
        }

        $this->onGoing = false;
    }

    /**
     * Will start the auto update process using git or http
     *
     * @param $login
     */
    function autoUpdate($login)
    {
        $AdminGroups = AdminGroups::getInstance();

        //If on going updates cancel !!
        if ($this->onGoing) {
            $msg = "#admin_error#An update or check for update is already under way!";
            $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, $msg);

            return;
        }

        $this->onGoing = true;

        if ($this->config->useGit) {
            $cmds = array(PHP_BINARY . ' composer.phar update --no-interaction --prefer-source');
        } else {
            $cmds = array(PHP_BINARY . ' composer.phar update --no-interaction --prefer-dist');
        }

        $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, '#admin_action#[#variable#AutoUpdate#admin_action#] Updating #variable#eXpansion & Components');

        $exec = new ParalelExecution($cmds, array($this, 'updateExecuted'), 'eXpansion_update');
        $exec->setValue('login', $this->currentLogin);
        $exec->start();
    }


    /**
     * Handles the results of one of the update steps. and starts next step.
     *
     * @param ParalelExecution $paralelExec The parallel execution utility
     * @param string[] $results The results of the previous steps execution
     * @param int $ret The value returned from the previous
     */
    public function updateExecuted($paralelExec, $results, $ret = 1)
    {
        $AdminGroups = AdminGroups::getInstance();

        if ($ret != 0) {
            $this->console('[eXpansion:AutoUpdate]Error while updating eXpansion !!');
            $this->console($results);
            \ManiaLivePlugins\eXpansion\Gui\Gui::showError($results, AdminGroups::getAdminsByPermission(Permission::SERVER_UPDATE));
            $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, '#admin_error#Error while updating #variable#eXpansion & Components !!');
        } else {
            $this->console('[eXpansion:AutoUpdate]eXpansion Updated!!');
            $AdminGroups->announceToPermission(Permission::SERVER_UPDATE, '#vote_success#Update of #variable#eXpansion & Components #vote_success#Done');
        }

        $this->onGoing = false;
    }


    public function eXpOnUnload()
    {
        parent::eXpOnUnload();
        $this->onGoingSteps = array();
        UpdateProgress::EraseAll();
    }

    /**
     * Checks if one of the strings in the array contains another text
     *
     * @param string $needle text to search for in the array
     * @param string[] $array The array of text in which we need to search for the text
     *
     * @return bool was the needle found in the array
     */
    protected function arrayContainsText($needle, $array)
    {
        foreach ($array as $val) {
            if (strpos($val, $needle) !== false)
                return true;
        }

        return false;
    }

}
