<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use \ManiaLive\Gui\Controls\Pager;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLive\Gui\ActionHandler;
use \DedicatedApi\Structures\GameInfos;

class GameOptions extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $connection, $storage;
    private $actionOK, $actionCancel, $actionTa, $actionRounds, $actionLaps, $actionCup, $actionTeam;
    private $btn_ta, $btn_rounds, $btn_cup, $btn_team, $btn_laps;
    private $frameGameMode;

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

        $this->setTitle(__('Game Options'));
        $this->genGameModes();
    }

    // Generate all inputboxes
    private function genGameModes() {

        $this->frameGameMode = new \ManiaLive\Gui\Controls\Frame();
        $this->frameGameMode->setAlign("left", "top");
        $this->frameGameMode->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->frameGameMode->setSize(100, 11);

        $nextGameInfo = $this->connection->getNextGameInfo();

        $this->btn_ta = new myButton();
        $this->btn_ta->setText(__("Time Attack"));
        $this->btn_ta->setValue(GameInfos::GAMEMODE_TIMEATTACK);
        $this->btn_ta->setAction($this->actionTA);

        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_TIMEATTACK)
            $this->btn_ta->setActive();
        $this->frameGameMode->addComponent($this->btn_ta);

        $this->btn_rounds = new myButton();
        $this->btn_rounds->setText(__("Rounds"));
        $this->btn_rounds->setAction($this->actionRounds);
        $this->btn_rounds->setValue(GameInfos::GAMEMODE_ROUNDS);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_ROUNDS)
            $this->btn_rounds->setActive();
        $this->frameGameMode->addComponent($this->btn_rounds);

        $this->btn_cup = new myButton();
        $this->btn_cup->setText(__("Cup"));
        $this->btn_cup->setAction($this->actionCup);
        $this->btn_cup->setValue(GameInfos::GAMEMODE_CUP);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_CUP)
            $this->btn_cup->setActive();
        $this->frameGameMode->addComponent($this->btn_cup);

        $this->btn_laps = new myButton();
        $this->btn_laps->setText(__("Laps"));
        $this->btn_laps->setAction($this->actionLaps);
        $this->btn_laps->setValue(GameInfos::GAMEMODE_LAPS);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_LAPS)
            $this->btn_laps->setActive();
        $this->frameGameMode->addComponent($this->btn_laps);

        $this->btn_team = new myButton();
        $this->btn_team->setText(__("Team"));
        $this->btn_team->setAction($this->actionTeam);
        $this->btn_team->setValue(GameInfos::GAMEMODE_TEAM);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_TEAM)
            $this->btn_team->setActive();
        $this->frameGameMode->addComponent($this->btn_team);

        $this->frameGameMode->setPosition(4, -10);
        $this->frameGameMode->setScale(0.7);
        $this->mainFrame->addComponent($this->frameGameMode);
    }

    function onDraw() {
        parent::onDraw();
    }

    function destroy() {
        
        $this->btn_cup->destroy();
        $this->btn_laps->destroy();
        $this->btn_rounds->destroy();
        $this->btn_ta->destroy();
        $this->btn_team->destroy();
        $this->frameGameMode->clearComponents();
        $this->frameGameMode->destroy();
        $this->clearComponents();

        parent::destroy();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function setGameMode($login, $gameMode) {
        try {
            switch ($gameMode) {
                case GameInfos::GAMEMODE_TIMEATTACK:
                    $mode = __("Time Attack");
                    break;
                case GameInfos::GAMEMODE_CUP:
                    $mode = __("Cup");
                    break;
                case GameInfos::GAMEMODE_LAPS:
                    $mode = __("Laps");
                    break;
                case GameInfos::GAMEMODE_ROUNDS:
                    $mode = __("Rounds");
                    break;
                case GameInfos::GAMEMODE_TEAM:
                    $mode = __("Team");
                    break;
                default:
                    $mode = $gameMode;
            }
            $this->connection->setGameMode($gameMode);
            $this->connection->chatSendServerMessage(__('$fff Next Gamemode is now set to $o%s', $this->getRecipient(), $mode));
            $this->mainFrame->removeComponent($this->frameGameMode);
            $this->genGameModes();
            $this->redraw();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError! $o$fff%s', $this->getRecipient(), $e->getMessage()), $this->getRecipient());
        }
    }

    public function Ok($login) {
        $this->Erase($login);
    }

    public function Cancel($login) {
        $this->Erase($login);
    }

}
