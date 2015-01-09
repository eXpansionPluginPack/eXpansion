<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AutoUpdate\Gui\Windows\UpdateProgress;
use ManiaLivePlugins\eXpansion\AutoUpdate\Structures\Repo;
use ManiaLivePlugins\eXpansion\AutoUpdate\Structures\Step;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use ManiaLivePlugins\eXpansion\Core\ParalelExecution;

/**
 * Auto update will check for updates and will update eXpansion if asked
 *
 * @author Petri & oliverde8
 */
class AutoUpdate extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	/**
	 * Message to show when an update is available for expansion
	 *
	 * @var Message
	 */
	private $msg_update;

	/**
	 * Configuration of eXpansion
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * List of directories that needs to be checked with git
	 *
	 * @var String[String]
	 */
	private $gitRepositories;

	/**
	 * Currently on going git updates or checks
	 *
	 * @var Step[]
	 */
	private $onGoingSteps = array();

	/**
	 * The login of the player that started the currently running steps
	 *
	 * @var String
	 */
	private $currentLogin;

	/**
	 * When was the last time there was check for an update
	 *
	 * @var int
	 */
	private $lastCheck = 0;

	/**
	 * Is after the last check all was up to date?
	 *
	 * @var bool
	 */
	private $isUpToDate = false;

	public function exp_onLoad()
	{
		$this->msg_update = exp_getMessage("new eXpansion version is available to update (%s)");
		$this->msg_notActive = exp_getMessage("#admin_error#git update isn't activated. Need to have git installed on the server first.");

		$this->gitRepositories = array(
			'./vendor/maniaplanet/dedicated-server-api' => "Dedicated Server Api",
			'./vendor/maniaplanet/manialive-lib' => "ManiaLive Lib",
			'./libraries/ManiaLivePlugins/eXpansion' => "eXpansion PluginPack"
		);
	}

	public function exp_onReady()
	{
		$adm = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();

		$cmd = $adm->addAdminCommand("update", $this, "autoUpdate", "server_update");
		$cmd->getMinParam(0);
		$cmd = $adm->addAdminCommand("check", $this, "checkUpdate", "server_update");
		$cmd->getMinParam(0);
		$this->config = Config::getInstance();
		$this->enableDedicatedEvents();

		if ($this->config->autoCheckUpdates && !$this->config->useGit) {
			$this->checkUpdate(null);
		}
	}

	public function onPlayerConnect($login, $isSpectator)
	{
		//If the current player is an admin he might want to know if his server is up to date
		if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission(
						$login, Permission::server_update
				) && $this->config->autoCheckUpdates
		) {
			//If needs to check for an update.
			$this->checkUpdate($login);
		}
	}

	/**
	 * Will start a check for update process using git or http
	 *
	 * @param string $login The login of the player that started the check for update process
	 */
	public function checkUpdate($login)
	{
		if ($this->config->useGit) {
			$this->gitCheck($login, $this->config->branchName);
		}
		else {
			$this->exp_chatSendServerMessage($this->msg_notActive, $login);
		}
	}

	/**
	 * Will start the auto update process using git or http
	 *
	 * @param $login
	 */
	function autoUpdate($login)
	{
		if ($this->config->useGit) {
			$this->pullFromGit($login, $this->config->branchName);
		}
		else {
			$this->exp_chatSendServerMessage($this->msg_notActive, $login);
		}
	}

	/**
	 * Checks of there is any on going git update check or updates
	 *
	 * @param $login The login of the user which tries to start it up
	 *
	 * @return bool
	 */
	public function checkForOnGoingGitUpdate($login)
	{
		if (!empty($this->onGoingSteps)) {
			$AdminGroups = AdminGroups::getInstance();
			$msg = "#admin_error#An update or check for update is already under way!";
			$AdminGroups->announceToPermission(Permission::server_update, $msg);
			return true;
		}

		return false;
	}

	/**
	 * Will update the current installation using git
	 *
	 * @param string $login  the player which started the update process
	 * @param string $branch the branch the update must be based on
	 */
	function pullFromGit($login, $branch = "master")
	{
		//If on going updates cancel !!
		if ($this->checkForOnGoingGitUpdate($login))
			return;


		$steps = array();

		//Declaring a step per directoy to update
		foreach ($this->gitRepositories as $path => $name) {
			$step = new Step();
			$step->function = 'gitMultiPull';
			$step->commands = array(
				//Need to update fetch last data
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' fetch',
				//Remove changes made to the source code by stupid user( :D ) or by zip update
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' reset --hard ',
				//Switch to demanded branch
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' checkout ' . $branch,
				//Removing unpushed changes
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' reset --hard origin/' . $branch,
				//Merges changes with origin
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' merge origin/' . $branch
			);
			//Messages to show on Start
			$step->startMessage = '#admin_action#[#variable#AutoUpdate#admin_action#] Update starting for "#variable#' . $name . '#admin_action#"';
			$step->startConsole = 'Starting to update ' . $name . ' !!';

			//Messages to show on Update
			$step->errorMessage = "#admin_error#Error";
			$step->errorConsole = "[eXpansion:AutoUpdate]Error while updating '" . $name . "' !! ";

			//Messages to show when updated
			$step->upMessage = '#vote_success#Done';
			$step->upConsole = '[eXpansion:AutoUpdate]' . $name . ' Updated!!';

			$steps[] = $step;
		}
		$this->onGoingSteps = $steps;
		$this->currentLogin = $login;
		$this->doSteps();
	}

	/**
	 * Handles the results of one of the update steps. and starts next step.
	 *
	 * @param ParalelExecution $paralelExec The parallel execution utility
	 * @param string[]         $results     The results of the previous steps execution
	 * @param int              $ret         The value returned from the previous
	 */
	public function gitMultiPull($paralelExec, $results, $ret = 1)
	{

		$currentStep = $paralelExec->getValue('currentStep');
		$login = $paralelExec->getValue('login');
		if ($ret != 0) {
			$this->console($currentStep->errorConsole);
			$this->exp_chatSendServerMessage($currentStep->errorMessage, $login);
		}
		else {
			$this->console($currentStep->upConsole);
			$this->exp_chatSendServerMessage($currentStep->upMessage, $login);
		}

		$this->doSteps();
	}

	/**
	 * Start the process to check if there is an update using git
	 *
	 * @param        $login  The login of the user that starts the process
	 * @param string $branch The name of the branch to compare this version with
	 */
	public function gitCheck($login, $branch = "master")
	{

		//If there is already an ongoing update or chec for update can't go further
		if ($this->checkForOnGoingGitUpdate($login))
			return;

		$steps = array();
		foreach ($this->gitRepositories as $path => $name) {
			$step = new Step();
			$step->function = 'gitCheckMultiStep';
			$step->commands = array(
				//Get latest info from the remote
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' fetch',
				//Switch to branch
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' checkout ' . $branch,
				//Get status
				'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' status'
			);
			$step->startMessage = '#admin_action#[#variable#AutoUpdate#admin_action#] Checking updates for "#variable#' . $name . '#admin_action#"';
			$step->startConsole = '[AutoUpdate] Checking for update in branch: ' . $name;

			$step->errorMessage = "#admin_error#An error has occurred while checking update";
			$step->errorConsole = "[AutoUpdate]Error while checking for '.$name.'";

			$step->upMessage = '#vote_success#Already newest';
			$step->upConsole = '[AutoUpdate]' . $name . ' is up to date!';

			$step->noUpMessage = '#admin_error#Update needed';
			$step->noUpConsole = '[AutoUpdate]' . $name . ' needs updating!';
			$steps[] = $step;
		}

		//If there wasn't a check recently then check
		if ((time() - $this->lastCheck) > (60 * 60 * 6)) {
			$this->lastCheck = time();
			$this->isUpToDate = true;
			$this->onGoingSteps = $steps;
			$this->currentLogin = $login;
			$this->doSteps();
		}
		else {
			//If not just show if not up to date
			if (!$this->isUpToDate) {
				$this->console('[AutoUpdate] eXpansion update is available at git!');
				$this->exp_chatSendServerMessage('eXpansion update is available at git!', $login);
			}
		}
	}

	/**
	 * Handles the results of each step
	 *
	 * @param ParalelExecution $paralelExec The parelle execution that did the step
	 * @param string[]         $results     The results of the current step
	 * @param int              $ret         The return values of the current step
	 */
	public function gitCheckMultiStep($paralelExec, $results, $ret = 1)
	{
		/**
		 * @var Step $currentStep
		 */
		$currentStep = $paralelExec->getValue('currentStep');
		$login = $paralelExec->getValue('login');
		$AdminGroups = AdminGroups::getInstance();

		if ($ret != 0) {
			//There was an error
			$this->console($currentStep->errorConsole);
			$AdminGroups->announceToPermission(Permission::server_update, $currentStep->errorMessage);
		}
		else if (!$this->arrayContainsText('working directory clean', $results)) {
			//Need for an update
			$this->console($currentStep->noUpConsole);
			$AdminGroups->announceToPermission(Permission::server_update, $currentStep->noUpMessage);
			$this->isUpToDate = false;
		}
		else {
			//Working directoy is clean no need for update
			$this->console($currentStep->upConsole);
			$AdminGroups->announceToPermission(Permission::server_update, $currentStep->upMessage);
		}

		$this->doSteps();
	}

	/**
	 * Executes the steps currentyl pending
	 */
	public function doSteps()
	{
		//If there is more steps to do
		if (!empty($this->onGoingSteps)) {
			/**
			 * Get next step to do, and remove it from step list
			 *
			 * @var Step $currentStep
			 */
			$currentStep = array_shift($this->onGoingSteps);

			$this->console($currentStep->startConsole);
			$AdminGroups = AdminGroups::getInstance();
			$AdminGroups->announceToPermission(Permission::server_update, $currentStep->startMessage);
			$exec = new ParalelExecution($currentStep->commands, array($this, $currentStep->function));
			$exec->setValue('login', $this->currentLogin);
			$exec->setValue('currentStep', $currentStep);
			$exec->start();
		}
	}

	public function exp_onUnload()
	{
		parent::exp_onUnload();
		$this->onGoingSteps = array();
		UpdateProgress::EraseAll();
	}

	/**
	 * Checks if one of the strings in the array contains another text
	 *
	 * @param string   $needle text to search for in the array
	 * @param string[] $array  The array of text in which we need to search for the text
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
