<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use \DedicatedApi\Structures\GameInfos;

class GameOptions extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    /** @var  \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    /** @var GameInfos */
    private $nextGameInfo;
    protected $actionOK, $actionCancel, $actionTA, $actionRounds, $actionLaps, $actionCup, $actionTeam;
    protected $btn_ta, $btn_rounds, $btn_cup, $btn_team, $btn_laps, $buttonOK, $buttonCancel;
    protected $frameGameMode, $frameGeneral, $frameCup, $frameTa, $frameRounds, $frameContainer;
    private $e = array();
    private $nextMode = null;

    function onConstruct() {
	parent::onConstruct();
	$config = \ManiaLive\DedicatedApi\Config::getInstance();
	$this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
	$this->storage = \ManiaLive\Data\Storage::getInstance();

	$this->actionOK = $this->createAction(array($this, "Ok"));
	$this->actionCancel = $this->createAction(array($this, "Cancel"));
	$this->actionTA = $this->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_TIMEATTACK);
	$this->actionRounds = $this->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_ROUNDS);
	$this->actionLaps = $this->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_LAPS);
	$this->actionCup = $this->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_CUP);
	$this->actionTeam = $this->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_TEAM);

	$this->setTitle(__('Game Options', $this->getRecipient()));

	$this->nextGameInfo = $this->connection->getNextGameInfo();
	$this->nextMode = $this->nextGameInfo->gameMode;

	$this->buttonOK = new myButton();
	$this->buttonOK->setText(__("Apply", $this->getRecipient()));
	$this->buttonOK->setAction($this->actionOK);
	$this->addComponent($this->buttonOK);

	$this->buttonCancel = new myButton();
	$this->buttonCancel->setText(__("Cancel", $this->getRecipient()));
	$this->buttonCancel->setAction($this->actionCancel);
	$this->addComponent($this->buttonCancel);

	$this->genGameModes();
	$this->genGeneral();
    }

    private function genGeneral() {
	$login = $this->getRecipient();
	$this->frameContainer = new \ManiaLive\Gui\Controls\Frame();
	$this->frameContainer->setAlign("left", "top");
	$this->frameContainer->setLayout(new \ManiaLib\Gui\Layouts\Column());

	$this->frameGeneral = new \ManiaLive\Gui\Controls\Frame();
	$this->frameGeneral->setAlign("left", "top");
	$this->frameGeneral->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->frameGeneral->setSize(160, 8);

	$this->frameRounds = new \ManiaLive\Gui\Controls\Frame();
	$this->frameRounds->setAlign("left", "top");
	$this->frameRounds->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->frameRounds->setSize(160, 8);

	$this->frameTa = new \ManiaLive\Gui\Controls\Frame();
	$this->frameTa->setAlign("left", "top");
	$this->frameTa->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->frameTa->setSize(160, 8);

	$this->frameTeam = new \ManiaLive\Gui\Controls\Frame();
	$this->frameTeam->setAlign("left", "top");
	$this->frameTeam->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->frameTeam->setSize(160, 8);

	$this->frameCup = new \ManiaLive\Gui\Controls\Frame();
	$this->frameCup->setAlign("left", "top");
	$this->frameCup->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->frameCup->setSize(160, 8);

	$this->e['ChatTime'] = new Inputbox("ChatTime");
	$this->e['ChatTime']->setText(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::TMtoMS($this->nextGameInfo->chatTime));
	$this->e['ChatTime']->setLabel(__("Podium Chat time", $login));
	$this->frameGeneral->addComponent($this->e['ChatTime']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameGeneral->addComponent($spacer);

	$this->e['AllWUduration'] = new Inputbox("AllWarmupDuration");
	$this->e['AllWUduration']->setLabel(__("All Warmup Duration", $login));
	$this->e['AllWUduration']->setText($this->nextGameInfo->allWarmUpDuration);
	$this->frameGeneral->addComponent($this->e['AllWUduration']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameGeneral->addComponent($spacer);

	$this->e['finishTimeout'] = new Inputbox("finishTimeOut");
	$this->e['finishTimeout']->setLabel(__("finishTimeout", $login));
	$this->e['finishTimeout']->setText($this->nextGameInfo->finishTimeout);
	$this->frameGeneral->addComponent($this->e['finishTimeout']);

	$this->frameContainer->addComponent($this->frameGeneral);

	// ta

	$this->e['timeAttackLimit'] = new Inputbox("timeAttackLimit");
	$this->e['timeAttackLimit']->setLabel(__("timeAttackLimit", $login));
	$this->e['timeAttackLimit']->setText(\ManiaLivePlugins\eXpansion\Helpers\TimeConversion::TMtoMS($this->nextGameInfo->timeAttackLimit));
	$this->frameTa->addComponent($this->e['timeAttackLimit']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameTa->addComponent($spacer);

	$this->e['timeAttackSynchStartPeriod'] = new Inputbox("timeAttackSynchStartPeriod");
	$this->e['timeAttackSynchStartPeriod']->setLabel(__("timeAttackSynchStartPeriod", $login));
	$this->e['timeAttackSynchStartPeriod']->setText($this->nextGameInfo->timeAttackSynchStartPeriod);
	$this->frameTa->addComponent($this->e['timeAttackSynchStartPeriod']);

	$this->frameContainer->addComponent($this->frameTa);

	// rounds
	$this->e['roundsPointsLimit'] = new Inputbox("roundsPointsLimit");
	$this->e['roundsPointsLimit']->setLabel(__("roundsPointsLimit", $login));
	$this->e['roundsPointsLimit']->setText($this->nextGameInfo->roundsPointsLimit);
	$this->frameRounds->addComponent($this->e['roundsPointsLimit']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameRounds->addComponent($spacer);

	$this->e['roundsForcedLaps'] = new Inputbox("roundsForcedLaps");
	$this->e['roundsForcedLaps']->setLabel(__("roundsForcedLaps", $login));
	$this->e['roundsForcedLaps']->setText($this->nextGameInfo->roundsForcedLaps);
	$this->frameRounds->addComponent($this->e['roundsForcedLaps']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameRounds->addComponent($spacer);

	$this->e['roundsPointsLimitNewRules'] = new Inputbox("roundsPointsLimitNewRules");
	$this->e['roundsPointsLimitNewRules']->setLabel(__("roundsPointsLimitNewRules", $login));
	$this->e['roundsPointsLimitNewRules']->setText($this->nextGameInfo->roundsPointsLimitNewRules);
	$this->frameRounds->addComponent($this->e['roundsPointsLimitNewRules']);

	$this->frameContainer->addComponent($this->frameRounds);

	// Team

	$this->e['teamPointsLimit'] = new Inputbox("teamPointsLimit");
	$this->e['teamPointsLimit']->setLabel(__("teamPointsLimit", $login));
	$this->e['teamPointsLimit']->setText($this->nextGameInfo->teamPointsLimit);
	$this->frameTeam->addComponent($this->e['teamPointsLimit']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameTeam->addComponent($spacer);

	$this->e['teamMaxPoints'] = new Inputbox("teamMaxPoints");
	$this->e['teamMaxPoints']->setLabel(__("teamMaxPoints", $login));
	$this->e['teamMaxPoints']->setText($this->nextGameInfo->teamMaxPoints);
	$this->frameTeam->addComponent($this->e['teamMaxPoints']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameTeam->addComponent($spacer);

	$this->e['teamPointsLimitNewRules'] = new Inputbox("teamPointsLimitNewRules");
	$this->e['teamPointsLimitNewRules']->setLabel(__("teamPointsLimitNewRules", $login));
	$this->e['teamPointsLimitNewRules']->setText($this->nextGameInfo->teamPointsLimitNewRules);
	$this->frameTeam->addComponent($this->e['teamPointsLimitNewRules']);
	$this->frameContainer->addComponent($this->frameTeam);

	$this->e['cupPointsLimit'] = new Inputbox("cupPointsLimit");
	$this->e['cupPointsLimit']->setLabel(__("cupPointsLimit", $login));
	$this->e['cupPointsLimit']->setText($this->nextGameInfo->cupPointsLimit);
	$this->frameCup->addComponent($this->e['cupPointsLimit']);


	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameCup->addComponent($spacer);

	$this->e['cupNbWinners'] = new Inputbox("cupNbWinners");
	$this->e['cupNbWinners']->setLabel(__("cupNbWinners", $login));
	$this->e['cupNbWinners']->setText($this->nextGameInfo->cupNbWinners);
	$this->frameCup->addComponent($this->e['cupNbWinners']);

	$spacer = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgEmpty);
	$this->frameCup->addComponent($spacer);

	$this->e['cupRoundsPerMap'] = new Inputbox("cupRoundsPerMap");
	$this->e['cupRoundsPerMap']->setLabel(__("cupRoundsPerMap", $login));
	$this->e['cupRoundsPerMap']->setText($this->nextGameInfo->cupRoundsPerMap);
	$this->frameCup->addComponent($this->e['cupRoundsPerMap']);

	$this->frameContainer->addComponent($this->frameCup);


	$this->mainFrame->addComponent($this->frameContainer);
    }

// Generate all inputboxes
    private function genGameModes() {
	$login = $this->getRecipient();
	$this->frameGameMode = new \ManiaLive\Gui\Controls\Frame($this->sizeX - 40, 0);
	$this->frameGameMode->setAlign("left", "top");
	$this->frameGameMode->setLayout(new \ManiaLib\Gui\Layouts\Column());
	$this->frameGameMode->setSize(100, 11);

	$lbl = new \ManiaLib\Gui\Elements\Label(25, 6);
	$lbl->setText(__("Choose Gamemode:", $login));
	$lbl->setTextSize(1);
	$this->frameGameMode->addComponent($lbl);

	$this->btn_ta = new Ratiobutton();
	$this->btn_ta->setText(__("Time Attack", $login));
	$this->btn_ta->setAction($this->actionTA);
	if ($this->nextMode == GameInfos::GAMEMODE_TIMEATTACK)
	    $this->btn_ta->setStatus(true);
	$this->frameGameMode->addComponent($this->btn_ta);

	$this->btn_rounds = new Ratiobutton();
	$this->btn_rounds->setText(__("Rounds", $login));
	$this->btn_rounds->setAction($this->actionRounds);
	if ($this->nextMode == GameInfos::GAMEMODE_ROUNDS)
	    $this->btn_rounds->setStatus(true);
	$this->frameGameMode->addComponent($this->btn_rounds);

	$this->btn_cup = new Ratiobutton();
	$this->btn_cup->setText(__("Cup", $login));
	$this->btn_cup->setAction($this->actionCup);
	if ($this->nextMode == GameInfos::GAMEMODE_CUP)
	    $this->btn_cup->setStatus(true);
	$this->frameGameMode->addComponent($this->btn_cup);

	$this->btn_laps = new Ratiobutton();
	$this->btn_laps->setText(__("Laps", $login));
	$this->btn_laps->setAction($this->actionLaps);
	if ($this->nextMode == GameInfos::GAMEMODE_LAPS)
	    $this->btn_laps->setStatus(true);
	$this->frameGameMode->addComponent($this->btn_laps);

	$this->btn_team = new Ratiobutton();
	$this->btn_team->setText(__("Team", $login));
	$this->btn_team->setAction($this->actionTeam);
	if ($this->nextMode == GameInfos::GAMEMODE_TEAM)
	    $this->btn_team->setStatus(true);
	$this->frameGameMode->addComponent($this->btn_team);

	$lbl = new \ManiaLib\Gui\Elements\Label(25, 6);
	$lbl->setText(__("Additional Options:", $login));
	$lbl->setTextSize(1);
	$this->frameGameMode->addComponent($lbl);

	$this->e['roundsUseNewRules'] = new Checkbox();
	if ($this->nextGameInfo->roundsUseNewRules)
	    $this->e['roundsUseNewRules']->setStatus(true);
	$this->e['roundsUseNewRules']->setText(__("Rounds: use new rules", $login));
	$this->frameGameMode->addComponent($this->e['roundsUseNewRules']);

	$this->e['teamUseNewRules'] = new Checkbox();
	if ($this->nextGameInfo->teamUseNewRules)
	    $this->e['teamUseNewRules']->setStatus(true);
	$this->e['teamUseNewRules']->setText(__("Team: use new rules", $login));
	$this->frameGameMode->addComponent($this->e['teamUseNewRules']);

	$this->e['DisableRespawn'] = new Checkbox();
	if ($this->nextGameInfo->disableRespawn)
	    $this->e['DisableRespawn']->setStatus(true);
	$this->e['DisableRespawn']->setText(__("Disable Respawn", $login));
	$this->frameGameMode->addComponent($this->e['DisableRespawn']);

	$this->e['ForceShowAllOpponents'] = new Checkbox();
	if ($this->nextGameInfo->forceShowAllOpponents)
	    $this->e['ForceShowAllOpponents']->setStatus(true);

	$this->e['ForceShowAllOpponents']->setText(__("Force Show All Opponents", $login));
	$this->frameGameMode->addComponent($this->e['ForceShowAllOpponents']);

	$this->mainFrame->addComponent($this->frameGameMode);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->frameGameMode->setPosition($this->sizeX - 40, 0);
	$this->frameContainer->setPosition(0, -8);
	$this->buttonOK->setPosition($this->sizeX - $this->buttonCancel->sizeX - $this->buttonOK->sizeX, -$this->sizeY + 6);
	$this->buttonCancel->setPosition($this->sizeX - $this->buttonCancel->sizeX, -$this->sizeY + 6);
    }

    function setGameMode($login, $gameMode) {
	$this->nextMode = $gameMode;
//$this->connection->chatSendServerMessage(__('$fff Next Gamemode is now set to $o%s', $this->getRecipient(), $mode));
//$this->nextGameInfo = $this->connection->getNextGameInfo();
	$this->frameGameMode->clearComponents();
	$this->mainFrame->removeComponent($this->frameGameMode);
	$this->genGameModes();
	$this->RedrawAll();
    }

    public function Ok($login, $options) {
	print_r($options);

	$gameInfos = $this->nextGameInfo;

	// general
	$gameInfos->allWarmUpDuration = intval($options['AllWarmupDuration']);
	$gameInfos->cupWarmUpDuration = intval($options['AllWarmupDuration']);
	$gameInfos->finishTimeout = intval($options['finishTimeOut']);

	$gameInfos->chatTime = \ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($options['ChatTime']);

	$gameInfos->disableRespawn = $this->e['DisableRespawn']->getStatus();
	$gameInfos->forceShowAllOpponents = $this->e['ForceShowAllOpponents']->getStatus();

	$gameInfos->roundsUseNewRules = $this->e['roundsUseNewRules']->getStatus();
	$gameInfos->teamUseNewRules = $this->e['teamUseNewRules']->getStatus();

	$gameInfos->gameMode = $this->nextMode;

	// ta
	$gameInfos->timeAttackLimit = \ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($options['timeAttackLimit']);
	$gameInfos->timeAttackSynchStartPeriod = intval($options['timeAttackSynchStartPeriod']);

	// rounds
	$gameInfos->roundsForcedLaps = intval($options['roundsForcedLaps']);
	$gameInfos->roundsPointsLimit = intval($options['roundsPointsLimit']);
	$gameInfos->roundsPointsLimitNewRules = intval($options['roundsPointsLimitNewRules']);

	// team            
	$gameInfos->teamPointsLimit = intval($options['teamPointsLimit']);
	$gameInfos->teamPointsLimitNewRules = intval($options['teamPointsLimitNewRules']);

	$gameInfos->teamMaxPoints = intval($options['teamMaxPoints']);

	// cup

	$gameInfos->cupNbWinners = intval($options['cupNbWinners']);
	$gameInfos->cupPointsLimit = intval($options['cupPointsLimit']);
	$gameInfos->cupRoundsPerMap = intval($options['cupRoundsPerMap']);

	var_dump($gameInfos);

	$this->connection->setGameInfos($gameInfos);
	$this->Erase($login);
    }

    public function Cancel($login) {
	$this->Erase($login);
    }

    function destroy() {
	$this->connection = null;
	$this->storage = null;

	$this->btn_cup->destroy();
	$this->btn_laps->destroy();
	$this->btn_rounds->destroy();
	$this->btn_ta->destroy();
	$this->btn_team->destroy();

	foreach ($this->e as $item)
	    $item->destroy();
	$this->e = array();

	$this->frameContainer->clearComponents();
	$this->frameContainer->destroy();

	$this->frameTa->clearComponents();
	$this->frameTa->destroy();

	$this->frameRounds->clearComponents();
	$this->frameRounds->destroy();

	$this->frameTeam->clearComponents();
	$this->frameTeam->destroy();

	$this->frameCup->clearComponents();
	$this->frameCup->destroy();

	$this->frameGameMode->clearComponents();
	$this->frameGameMode->destroy();

	$this->frameGeneral->clearComponents();
	$this->frameGeneral->destroy();

	$this->clearComponents();
	parent::destroy();
    }

}
