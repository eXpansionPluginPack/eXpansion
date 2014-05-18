<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

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

    public function exp_onLoad()
    {
	$this->msg_update = exp_getMessage("new eXpansion version is available to update (%s)");
	$this->dataAccess = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();
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
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "server_update") && $this->config->autoCheckUpdates && !$this->config->useGit) {

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

    function pullFromGit($login, $branch = "master")
    {
	$results = array();

	$this->console("[eXpansion:AutoUpdate]Starting to update Dedicated server API !!");
	$this->exp_chatSendServerMessage("Starting to update Dedicated server API !!", $login);
	exec("cd ./vendor/maniaplanet/dedicated-server-api && git fetch && git reset --hard && git checkout $branch && git pull --rebase", $results, $ret);
	if ($ret != 0) {
	    $this->console("[eXpansion:AutoUpdate]Error while updating Dedicated server API !!");
	    $this->exp_chatSendServerMessage("#admin_error#Error while updating Dedicated server API !! ", $login);
	    return;
	}
	$this->console("[eXpansion:AutoUpdate]Dedicated server API Updated!!");
	$this->exp_chatSendServerMessage("Dedicated server API Updated!!", $login);

	$this->console("[eXpansion:AutoUpdate]Starting to update Manialive-lib !!");
	$this->exp_chatSendServerMessage("Starting to update Manialive-lib !!", $login);
	exec("cd ./vendor/maniaplanet/manialive-lib && git fetch && git reset --hard && git checkout $branch && git pull --rebase", $results, $ret);
	if ($ret != 0) {
	    $this->console("[eXpansion:AutoUpdate]Error while updating Manialive Lib !!");
	    $this->exp_chatSendServerMessage("#admin_error#Error while updating Manialive Lib !! ", $login);
	    return;
	}
	$this->console("[eXpansion:AutoUpdate]Manialive Lib Updated!!");
	$this->exp_chatSendServerMessage("Manialive Lib Updated!!", $login);

	$this->console("[eXpansion:AutoUpdate]Starting to update eXpansion !!");
	$this->exp_chatSendServerMessage("Starting to eXpansion !!", $login);
	exec("cd ./libraries/ManiaLivePlugins/eXpansion && git fetch && git reset --hard && git checkout $branch && git pull --rebase", $results, $ret);
	if ($ret != 0) {
	    $this->console("[eXpansion:AutoUpdate]Error while updating eXpansion !!");
	    $this->exp_chatSendServerMessage("#admin_error#Error while updating eXpansion !! !! ", $login);
	    return;
	}
	$this->console("[eXpansion:AutoUpdate]eXpansion Updated!!");
	$this->exp_chatSendServerMessage("eXpansion Updated!!", $login);
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
	$this->console("[eXpansion:AutoUpdate]Starting checking for update to Dedicated server API !!");
	$this->exp_chatSendServerMessage("Starting checking for update to Dedicated server API !!", $login);
	exec("cd ./vendor/maniaplanet/dedicated-server-api && git fetch && git reset --hard && git checkout $branch && git pull --rebase", $results, $ret);
	if ($ret != 0) {
	    $this->console("[eXpansion:AutoUpdate]Error while checking for Dedicated server API !!");
	    $this->exp_chatSendServerMessage("#admin_error#Error while checking for Dedicated server API !! ", $login);
	    return;
	}
	if (sizeof($results) > 3) {
	    $this->console("[eXpansion:AutoUpdate]Dedicated server API needs updating!!");
	    $this->exp_chatSendServerMessage("Dedicated server API needs updating!!", $login);
	} else {
	    $this->console("[eXpansion:AutoUpdate]Dedicated server API is up to date!!");
	    $this->exp_chatSendServerMessage("Dedicated server API is up to date!!", $login);
	}
	$results = array();


	$this->console("[eXpansion:AutoUpdate]Starting checking for update to  Manialive-lib API !!");
	$this->exp_chatSendServerMessage("Starting checking for update to Manialive-lib !!", $login);
	exec("cd ./vendor/maniaplanet/manialive-lib && git fetch && git reset --hard && git checkout $branch && git pull --rebase", $results, $ret);
	if ($ret != 0) {
	    $this->console("[eXpansion:AutoUpdate]Error while checking for Manialive Lib Updates !!");
	    $this->exp_chatSendServerMessage("#admin_error#Error while checking for Manialive Lib Updates !! ", $login);
	    return;
	}
	if (sizeof($results) > 3) {
	    $this->console("[eXpansion:AutoUpdate]Manialive-lib needs updating!!");
	    $this->exp_chatSendServerMessage("Manialive-lib  needs updating!!", $login);
	} else {
	    $this->console("[eXpansion:AutoUpdate]Manialive-lib  is up to date!!");
	    $this->exp_chatSendServerMessage("Manialive-lib  is up to date!!", $login);
	}
	$results = array();


	$this->console("[eXpansion:AutoUpdate]Starting checking for update to eXpansion !!");
	$this->exp_chatSendServerMessage("Starting checking for update to eXpansion !!", $login);
	exec("cd ./libraries/ManiaLivePlugins/eXpansion && git fetch && git reset --hard && git checkout $branch && git pull --rebase", $results, $ret);
	if ($ret != 0) {
	    $this->console("[eXpansion:AutoUpdate]Error while checking for eXpansion Updates !!");
	    $this->exp_chatSendServerMessage("#admin_error#Error while checking for eXpansion Updates !! ", $login);
	    return;
	}
	if (sizeof($results) > 3) {
	    $this->console("[eXpansion:AutoUpdate]eXpansion needs updating!!");
	    $this->exp_chatSendServerMessage("eXpansion needs updating!!", $login);
	} else {
	    $this->console("[eXpansion:AutoUpdate]eXpansion is up to date!!");
	    $this->exp_chatSendServerMessage("eXpansion is up to date!!", $login);
	}
    }

}
