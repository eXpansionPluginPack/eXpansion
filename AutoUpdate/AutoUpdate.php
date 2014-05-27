<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\AutoUpdate\Gui\Windows\UpdateProgress;
use ManiaLivePlugins\eXpansion\AutoUpdate\Structures\Repo;
use ManiaLivePlugins\eXpansion\AutoUpdate\Structures\Step;
use ManiaLivePlugins\eXpansion\Core\ParalelExecution;

/**
 * Description of AutoUpdate
 *
 * @author Petri
 */
class AutoUpdate extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    /** @var  \ManiaLivePlugins\eXpansion\Core\DataAccess */
    private $dataAccess;
    private $currentVersion = \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION;
    // private $currentVersion = "0.9.3";
    private $updateAvailable = false;
    private $msg_update;

    /** @var Config */
    private $config;

    private $repos;

    private $onGoingSteps = array();
    private $currentLogin;

    private $lastCheck = 0;
    private $isUpToDate = false;

    public function exp_onLoad()
    {
	$this->msg_update = exp_getMessage("new eXpansion version is available to update (%s)");
	$this->dataAccess = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();

	$this->repos = array(
	    './vendor/maniaplanet/dedicated-server-api' => "Dedicated server Api",
	    './vendor/maniaplanet/manialive-lib'        => "Manialive Lib",
	    './libraries/ManiaLivePlugins/eXpansion' => "eXpansion"
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
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::server_update) && $this->config->autoCheckUpdates) {

	    $this->checkUpdate($login);
	}
    }

    public function checkUpdate($login)
    {
	if ($this->config->useGit) {
	    $this->gitCheck($login, $this->config->branchName);
	} else {
	    $query = "http://reaby.kapsi.fi/ml/update/index.php";
	    $this->dataAccess->httpGet($query, array($this, "xCheck"), array($login));
	}
    }

    function autoUpdate($login)
    {
	if ($this->config->useGit) {
	    $this->pullFromGit($login, $this->config->branchName);
	} else {
	    if (!$this->updateAvailable) {
		$this->exp_chatSendServerMessage("Can't update, no update available!", $login);
		return;
	    }

	    $path = \ManiaLib\Utils\Path::getInstance();
	    $window = Gui\Windows\UpdateProgress::Create($login);
	    $window->show($login);

	    $path = $path->getRoot(true) . "/update/update.php";
	    $config = \ManiaLive\DedicatedApi\Config::getInstance();

	    $cmd = PHP_BINARY . " " . realpath($path) . " " . $this->currentVersion . " " . $config->host . " " . $config->port . " " . $config->user . " " . $config->password;

	    if (substr(php_uname(), 0, 7) == "Windows") {
		pclose(popen("start " . $cmd, "r"));
	    } else {
		exec($cmd . " > /dev/null &");
	    }
	}
    }

    public function checkForOnGoingGitUpdate($login)
    {
	if (!empty($this->onGoingSteps)) {
	    $this->exp_chatSendServerMessage("#admin_error#An update or check for update process is already under way!!!");
	    return true;
	}
	return false;
    }

    function pullFromGit($login, $branch = "master")
    {

	if ($this->checkForOnGoingGitUpdate($login))
	    return;


	$steps = array();
	foreach ($this->repos as $path => $name) {
	    $step = new Step();
	    $step->function = 'gitMultiPull';
	    $step->commands = array(
		'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' fetch',
		'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' reset --hard',
		'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' checkout ' . $branch,
		'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' merge origin/' . $branch
	    );
	    $step->startMessage = '[eXpansion:AutoUpdate]Starting to update ' . $name . ' !!';
	    $step->startConsole = 'Starting to update ' . $name . ' !!';

	    $step->errorMessage = "#admin_error#Error while updating '.$name.' !! ";
	    $step->errorConsole = "[eXpansion:AutoUpdate]Error while updating '.$name.' !! ";

	    $step->upConsole = '[eXpansion:AutoUpdate]' . $name . ' Updated!!';
	    $step->upMessage = '' . $name . ' Updated!!';

	    $steps[] = $step;
	}
	$this->onGoingSteps = $steps;
	$this->currentLogin = $login;
	$this->doSteps();
    }

    public function gitMultiPull($paralelExec, $results, $ret = 1)
    {

	$currentStep = $paralelExec->getValue('currentStep');
	$login = $paralelExec->getValue('login');
	if ($ret != 0) {
	    $this->console($currentStep->errorConsole);
	    $this->exp_chatSendServerMessage($currentStep->errorMessage, $login);
	} else {
	    $this->console($currentStep->upConsole);
	    $this->exp_chatSendServerMessage($currentStep->upMessage, $login);
	}

	$this->doSteps();
    }

    public function xCheck($data, $code, $login = null)
    {
	if ($code == 200) {
	    $updateData = json_decode($data);

	    if (version_compare($updateData->version, $this->currentVersion, "lt")) {
		return;
	    }
	    if ($updateData->version == $this->currentVersion) {
		return;
	    }
	    $this->updateAvailable = true;

	    $this->exp_chatSendServerMessage($this->msg_update, $login, array($updateData->version));
	}
    }

    public function gitCheck($login, $branch = "master")
    {
	if ($this->checkForOnGoingGitUpdate($login))
	    return;

	$steps = array();
	foreach ($this->repos as $path => $name) {
	    $step = new Step();
	    $step->function = 'gitCheckMultiStep';
	    $step->commands = array(
		'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' fetch',
		'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' checkout ' . $branch,
		'git --git-dir=' . $path . '/.git --work-tree=' . $path . ' status'
	    );
	    $step->startMessage = 'Starting checking for update to ' . $name . ' !!';
	    $step->startConsole = '[eXpansion:AutoUpdate]Starting checking for update to ' . $name . ' !!';

	    $step->errorMessage = "#admin_error#Error while checking for '.$name.' !! ";
	    $step->errorConsole = "[eXpansion:AutoUpdate]Error while checking for '.$name.' !! ";

	    $step->upConsole = '[eXpansion:AutoUpdate]' . $name . ' is up to date!!';
	    $step->upMessage = '' . $name . '  is up to date!!';

	    $step->noUpMessage = '' . $name . ' needs updating!!';
	    $step->noUpConsole = '[eXpansion:AutoUpdate]' . $name . ' needs updating!!';
	    $steps[] = $step;
	}
	$this->onGoingSteps = $steps;
	$this->currentLogin = $login;

	if (time() - $this->lastCheck > 60 * 60 * 6) {
	    $this->lastCheck = time();
	    $this->isUpToDate = true;
	    $this->doSteps();
	} else {
	    if (!$this->isUpToDate) {
		$this->console('[eXpansion:AutoUpdate]A system Module needs Updating!!');
		$this->exp_chatSendServerMessage('A system Module needs Updating!!', $login);
	    }
	}
    }

    public function gitCheckMultiStep($paralelExec, $results, $ret = 1)
    {
	/**
	 * @var Step $currentStep
	 */
	$currentStep = $paralelExec->getValue('currentStep');
	$login = $paralelExec->getValue('login');
	if ($ret != 0) {
	    $this->console($currentStep->errorConsole);
	    $this->exp_chatSendServerMessage($currentStep->errorMessage, $login);
	} else if (!$this->arrayContainsText('working directory clean', $results)) {
	    $this->console($currentStep->noUpConsole);
	    $this->exp_chatSendServerMessage($currentStep->noUpMessage, $login);
	    $this->isUpToDate = false;
	} else {
	    $this->console($currentStep->upConsole);
	    $this->exp_chatSendServerMessage($currentStep->upMessage, $login);
	}

	$this->doSteps();
    }

    public function doSteps()
    {
	if (!empty($this->onGoingSteps)) {
	    /**
	     * @var Step $currentStep
	     */
	    $currentStep = array_shift($this->onGoingSteps);

	    $this->console($currentStep->startConsole);
	    $this->exp_chatSendServerMessage($currentStep->startMessage, $this->currentLogin);
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

    protected function arrayContainsText($needle, $array){
	foreach($array as $val){
	    if(strpos($val, $needle) !== false)
		return true;
	}
	return false;
    }
}
