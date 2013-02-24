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

    private $frameCb;
    private $frameInputbox, $frameLadder;
    private $buttonOK, $buttonCancel;
    private $connection;
    private $actionOK, $actionCancel, $actionTa, $actionRounds, $actionLaps, $actionCup, $actionTeam;

    function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->actionOK = ActionHandler::getInstance()->createAction(array($this, "Ok"));
        $this->actionCancel = ActionHandler::getInstance()->createAction(array($this, "Cancel"));
        $this->actionTA = ActionHandler::getInstance()->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_TIMEATTACK);
        $this->actionRounds = ActionHandler::getInstance()->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_ROUNDS);
        $this->actionLaps = ActionHandler::getInstance()->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_LAPS);
        $this->actionCup = ActionHandler::getInstance()->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_CUP);
        $this->actionTeam = ActionHandler::getInstance()->createAction(array($this, "setGamemode"), GameInfos::GAMEMODE_TEAM);

        $this->setTitle(_('Game Options'));
        $this->genGameModes();
    }

    // Generate all inputboxes
    private function genGameModes() {

        $this->frameGameMode = new \ManiaLive\Gui\Controls\Frame();
        $this->frameGameMode->setAlign("left", "top");
        $this->frameGameMode->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->frameGameMode->setSize(100, 11);

        $nextGameInfo = $this->connection->getNextGameInfo();

        $button = new myButton();
        $button->setText(_("Time Attack"));
        $button->setValue(GameInfos::GAMEMODE_TIMEATTACK);
        $button->setAction($this->actionTA);

        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_TIMEATTACK)
            $button->setActive();
        $this->frameGameMode->addComponent($button);

        $button = new myButton();
        $button->setText(_("Rounds"));
        $button->setAction($this->actionRounds);
        $button->setValue(GameInfos::GAMEMODE_ROUNDS);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_ROUNDS)
            $button->setActive();
        $this->frameGameMode->addComponent($button);

        $button = new myButton();
        $button->setText(_("Cup"));
        $button->setAction($this->actionCup);
        $button->setValue(GameInfos::GAMEMODE_CUP);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_CUP)
            $button->setActive();
        $this->frameGameMode->addComponent($button);

        $button = new myButton();
        $button->setText(_("Laps"));
        $button->setAction($this->actionLaps);
        $button->setValue(GameInfos::GAMEMODE_LAPS);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_LAPS)
            $button->setActive();
        $this->frameGameMode->addComponent($button);

        $button = new myButton();
        $button->setText(_("Team"));
        $button->setAction($this->actionTeam);
        $button->setValue(GameInfos::GAMEMODE_TEAM);
        if ($nextGameInfo->gameMode == GameInfos::GAMEMODE_TEAM)
            $button->setActive();
        $this->frameGameMode->addComponent($button);
        
        $this->frameGameMode->setPosition(4, -10);
        $this->frameGameMode->setScale(0.7);
        $this->mainFrame->addComponent($this->frameGameMode);
    }

    function onDraw() {
        parent::onDraw();
    }

    function destroy() {
        ActionHandler::getInstance()->deleteAction($this->actionCancel);
        ActionHandler::getInstance()->deleteAction($this->actionOK);
        ActionHandler::getInstance()->deleteAction($this->actionCup);
        ActionHandler::getInstance()->deleteAction($this->actionLaps);
        ActionHandler::getInstance()->deleteAction($this->actionRounds);
        ActionHandler::getInstance()->deleteAction($this->actionTa);
        ActionHandler::getInstance()->deleteAction($this->actionTeam);
        parent::destroy();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function setGameMode($login, $gameMode) {
        try {
            switch ($gameMode) {
                case GameInfos::GAMEMODE_TIMEATTACK:
                    $mode = _("Time Attack");
                    break;
                case GameInfos::GAMEMODE_CUP:
                    $mode = _("Cup");
                    break;
                case GameInfos::GAMEMODE_LAPS:
                    $mode = _("Laps");
                    break;
                case GameInfos::GAMEMODE_ROUNDS:
                    $mode = _("Rounds");
                    break;
                case GameInfos::GAMEMODE_TEAM:
                    $mode = _("Team");
                    break;
                default:
                    $mode = $gameMode;
            }
            $this->connection->setGameMode($gameMode);
            $this->connection->chatSendServerMessage(_('$fff Next Gamemode is now set to $o%s', $mode));
            $this->mainFrame->removeComponent($this->frameGameMode);
            $this->genGameModes();
            $this->redraw();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(_('$f00$oError! $o$fff%s', $e->getMessage()), $this->getRecipient());
        }
    }

    public function Ok($login) {
        $this->hide();
    }

    public function Cancel($login) {
        $this->hide();
    }

}
