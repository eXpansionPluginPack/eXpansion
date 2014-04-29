<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

/**
 * Description of AutoUpdate
 *
 * @author Petri
 */
class AutoUpdate extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var ManiaLivePlugins\eXpansion\Core\DataAccess */
    private $dataAccess;
    private $currentVersion = \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION;
    // private $currentVersion = "0.9.3";
    private $updateAvailable = false;
    private $msg_update;

    /** @var Config */
    private $config;

    public function exp_onLoad() {
	$this->msg_update = exp_getMessage("new eXpansion version is available to update (%s)");
	$this->dataAccess = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();
    }

    public function exp_onReady() {
	$adm = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();

	$cmd = $adm->addAdminCommand("update", $this, "autoUpdate", "server_update");
	$cmd->getMinParam(0);
	$cmd = $adm->addAdminCommand("check", $this, "checkUpdate", "server_update");
	$cmd->getMinParam(0);
	$this->config = Config::getInstance();
	$this->enableDedicatedEvents();
	
	if ($this->config->autoCheckUpdates) {
	    $this->checkUpdate(null);
	}
    }

    public function onPlayerConnect($login, $isSpectator) {
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "server_update") && $this->config->autoCheckUpdates) {
	    
	    $this->checkUpdate($login);
	}
    }

    public function checkUpdate($login) {
	$query = "http://reaby.kapsi.fi/ml/update/index.php";
	$this->dataAccess->httpGet($query, array($this, "xCheck"), array($login));
    }

    function autoUpdate($login) {
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

    public function xCheck($data, $code, $login = null) {
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

}
