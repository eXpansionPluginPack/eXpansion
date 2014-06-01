<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ResSkip;

use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\Bill;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\DonatePanel\Config as DonateConfig;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Widgets_ResSkip\Gui\Widgets\ResSkipButtons;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

class Widgets_ResSkip extends ExpPlugin {

    private $msg_resOnProgress, $msg_resUnused, $msg_resMax, $msg_skipUnused, $msg_skipMax, $msg_prestart, $msg_pskip;
    private $config;
    private $donateConfig;
    private $lastMapUid = null;
    private $resCount = 0;
    private $resActive;
    private $skipCount = 0;
    private $skipActive;
    private $actions = array();

    public function exp_onLoad() {
	$this->msg_resOnProgress = exp_getMessage("The restart of this track is in progress!");
	$this->msg_prestart = exp_getMessage("#player#Player #variable# %s #player#pays and restarts the challenge!");
	$this->msg_pskip = exp_getMessage('#player#Player#variable# %s #player#pays and skips the challenge!');
	$this->msg_resUnused = exp_getMessage("#error#Player can't restart tracks on this server");
	$this->msg_resMax = exp_getMessage("#error#The map has already been restarted. Limit reached!");
	$this->msg_skipUnused = exp_getMessage("#error#You can't skip tracks on this server.");
	$this->msg_skipMax = exp_getMessage("#error#You have skipped to many maps already!");

	$this->setPublicMethod('isPublicResIsActive');
	$this->setPublicMethod('isPublicSkipActive');
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();

	$this->config = Config::getInstance();
	$this->donateConfig = DonateConfig::getInstance();
	$this->actions['skip_final'] = ActionHandler::getInstance()->createAction(array($this, "skipMap"));
	$this->actions['skip'] = Gui::createConfirm($this->actions['skip_final']);
	$this->actions['res_final'] = ActionHandler::getInstance()->createAction(array($this, "restartMap"));
	$this->actions['res'] = Gui::createConfirm($this->actions['res_final']);

	$this->showResSkip(null);
    }

    public function isPublicResIsActive() {
	return !(empty($this->config->publicResAmount) || $this->config->publicResAmount[0] == -1);
    }

    public function isPublicSkipActive() {
	return !(empty($this->config->publicSkipAmount) || $this->config->publicSkipAmount[0] == -1);
    }

    public function showResSkip($login) {
	if ($this->expStorage->isRelay)
	    return;

	$widget = ResSkipButtons::Create($login);
	$widget->setActions($this->actions['res'], $this->actions['skip']);
	$widget->setServerInfo($this->storage->serverLogin);
	$widget->setSize(32.0, 10.0);


	$nbSkips = isset($this->skipCount[$login]) ? $this->skipCount[$login] : 0;

	if (isset($this->config->publicSkipAmount[$nbSkips]) && $this->config->publicSkipAmount[$nbSkips] != -1) {
	    $amount = $this->config->publicSkipAmount[$nbSkips];
	    $widget->setSkipAmount($amount);
	} else {
	    if ($nbSkips >= count($this->config->publicSkipAmount)) {
		$widget->setSkipAmount("max");
	    } else {
		$widget->setSkipAmount("no");
	    }
	}

	if (isset($this->config->publicResAmount[$this->resCount]) && $this->config->publicResAmount[$this->resCount] != -1) {
	    $amount = $this->config->publicResAmount[$this->resCount];
	    $widget->setResAmount($amount);
	} else {
	    if ($this->resCount >= count($this->config->publicResAmount)) {
		$widget->setResAmount("max");
	    } else {
		$widget->setResAmount("no");
	    }
	}	
	$widget->show();
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
	if (isset($this->skipCount[$login]))
	    unset($this->skipCount[$login]);
	ResSkipButtons::Erase($login);
    }

    public function restartMap($login) {

	if (AdminGroups::hasPermission($login, Permission::map_restart)) {
	    if ($this->isPluginLoaded('\\ManiaLivePlugins\\eXpansion\Maps\\Maps')) {
		$this->callPublicMethod('\\ManiaLivePlugins\\eXpansion\Maps\\Maps', 'replayMap', $login);
		return;
	    }

	    $this->connection->restartMap($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP);
	    $admin = $this->storage->getPlayerObject($login);
	    $this->exp_chatSendServerMessage('#admin_action#Admin#variable# %s #admin_action#restarts the challenge!', null, array($admin->nickName));
	} else {
	    //Player restart cost Planets
	    if ($this->resActive) {
		//Already restarted no need to do
		$this->exp_chatSendServerMessage($this->msg_resOnProgress, $login);
	    } else if (isset($this->config->publicResAmount[$this->resCount]) && $this->config->publicResAmount[$this->resCount] != -1 && $this->resCount < count($this->config->publicResAmount)) {
		$amount = $this->config->publicResAmount[$this->resCount];
		$this->resActive = true;

		if (!empty($this->donateConfig->toLogin))
		    $toLogin = $this->donateConfig->toLogin;
		else
		    $toLogin = $this->storage->serverLogin;

		$bill = $this->exp_startBill($login, $toLogin, $amount, __("Are you sure you want to restart this map", $login), array($this, 'publicRestartMap'));
		$bill->setSubject('map_restart');
		$bill->setErrorCallback(5, array($this, 'failRestartMap'));
		$bill->setErrorCallback(6, array($this, 'failRestartMap'));
	    }else {
		if (empty($this->config->publicResAmount) || $this->config->publicResAmount[0] == -1) {
		    $this->exp_chatSendServerMessage($this->msg_resUnused, $login);
		} else {
		    $this->exp_chatSendServerMessage($this->msg_resMax, $login);
		}
	    }
	}
    }

    public function publicRestartMap(Bill $bill) {
	$player = $this->storage->getPlayerObject($bill->getSource_login());
	$this->exp_chatSendServerMessage($this->msg_prestart, null, array($player->nickName));

	if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\Maps\\Maps')) {
	    $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\Maps\\Maps", "replayMap", $bill->getSource_login());
	    return;
	}
	$this->connection->restartMap($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP);
    }

    public function failRestartMap(Bill $bill, $state, $stateName) {
	$this->resActive = false;
    }

    public function publicSkipMap(Bill $bill) {
	$this->skipActive = true;
	$this->connection->nextMap($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP);
	$player = $this->storage->getPlayerObject($bill->getSource_login());
	$this->exp_chatSendServerMessage($this->msg_pskip, null, array($player->nickName));
    }

    public function skipMap($login) {

	if (AdminGroups::hasPermission($login, Permission::map_skip)) {
	    $adminGrp = AdminGroups::getInstance();
	    $adminGrp->adminCmd($login, "skip");
	    return;
	}

	$nbSkips = isset($this->skipCount[$login]) ? $this->skipCount[$login] : 0;

	if (isset($this->config->publicSkipAmount[$nbSkips]) && $this->config->publicSkipAmount[$nbSkips] != -1 && $nbSkips < count($this->config->publicSkipAmount)) {
	    $amount = $this->config->publicSkipAmount[$nbSkips];

	    if (!empty($this->donateConfig->toLogin))
		$toLogin = $this->donateConfig->toLogin;
	    else
		$toLogin = $this->storage->serverLogin;

	    $bill = $this->exp_startBill($login, $toLogin, $amount, __("Are you sure you want to skip this map", $login), array($this, 'publicSkipMap'));
	    $bill->setSubject('map_skip');
	} else {
	    if (empty($this->config->publicSkipAmount) || $this->config->publicSkipAmount[0] == -1) {
		$this->exp_chatSendServerMessage($this->msg_skipUnused, $login);
	    } else {
		$this->exp_chatSendServerMessage($this->msg_skipMax, $login);
	    }
	}
    }

    private function countMapRestart() {
	if ($this->storage->currentMap->uId == $this->lastMapUid)
	    $this->resCount++;
	else {
	    $this->lastMapUid = $this->storage->currentMap->uId;
	    $this->resCount = 0;
	}
	$this->resActive = false;

	if (!$this->skipActive) {
	    $this->skipCount = array();
	}
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
	ResSkipButtons::EraseAll();
    }

    public function onBeginMatch() {
	$this->countMapRestart();
	$this->showResSkip(null);
    }

    public function exp_onUnload() {
	ResSkipButtons::EraseAll();
    }

}
