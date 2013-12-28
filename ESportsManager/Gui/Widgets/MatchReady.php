<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Gui\Widgets;

use ManiaLivePlugins\eXpansion\ESportsManager\ESportsManager;
use ManiaLivePlugins\eXpansion\ESportsManager\Gui\Controls\PlayerStatusItem;

/**
 * Description of MatchReady
 *
 * @author Reaby
 */
class MatchReady extends \ManiaLive\Gui\Window {

    protected $background, $lbl_nextMatch, $lbl_info, $btn_ready, $btn_wait, $btn_continue;

    /** @var \ManiaLive\Gui\Controls\Frame */
    protected $frame_team_blue, $frame_team_red, $frame_rounds;
    private $gameMode;
    public static $actions;

    public function onConstruct() {
        parent::onConstruct();
        echo "ready window opened!\n";
        $login = $this->getRecipient();

        $this->background = new \ManiaLib\Gui\Elements\Quad();
        $this->background->setAlign("left", "top");
        $this->background->setStyle("Bgs1");
        $this->background->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgCard1);
        //$this->addComponent($this->background);


        $this->lbl_nextMatch = New \ManiaLib\Gui\Elements\Label(60, 6);
        $this->lbl_nextMatch->setStyle("TextRaceMessageBig");
        $this->lbl_nextMatch->setAlign("center", "top");
        $this->lbl_nextMatch->setText(ESportsManager::$matchSettings->matchTitle);
        $this->addComponent($this->lbl_nextMatch);

        $this->lbl_info = New \ManiaLib\Gui\Elements\Label(60, 6);
        $this->lbl_info->setText(ESportsManager::$matchSettings->rulesText);
        $this->lbl_info->setAlign("center");
        $this->addComponent($this->lbl_info);

        $this->frame_team_blue = new \ManiaLive\Gui\Controls\Frame(0, 0);
        $this->frame_team_blue->setLayout(new \ManiaLib\Gui\Layouts\Column(60, 6));
        $this->frame_team_blue->setAlign("center");
        $this->addComponent($this->frame_team_blue);

        $this->frame_team_red = new \ManiaLive\Gui\Controls\Frame();
        $this->frame_team_red->setLayout(new \ManiaLib\Gui\Layouts\Column(60, 6));
        $this->frame_team_red->setAlign("center");
        $this->addComponent($this->frame_team_red);

        $this->frame_rounds = new \ManiaLive\Gui\Controls\Frame();
        $this->frame_rounds->setLayout(new \ManiaLib\Gui\Layouts\VerticalFlow(60, 40));
        $this->frame_rounds->setAlign("center");
        $this->addComponent($this->frame_rounds);

        $this->btn_ready = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_ready->setAction(self::$actions['ready']);
        $this->btn_ready->setText(__("Ready", $login));
        $this->btn_ready->colorize("0d0");
        $this->btn_ready->setAlign("center");
        $this->addComponent($this->btn_ready);

        $this->btn_wait = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_wait->setAction(self::$actions['notReady']);
        $this->btn_wait->setText(__("Not Ready", $login));
        $this->btn_wait->colorize("dd0");
        $this->btn_wait->setAlign("center");
        $this->addComponent($this->btn_wait);

        $this->btn_continue = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_continue->setAction(self::$actions['forceContinue']);
        $this->btn_continue->setText(__("Force Go", $login));
        $this->btn_continue->colorize("d00");
        $this->btn_continue->setAlign("center");
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "esports_admin")) {
            $this->addComponent($this->btn_continue);
        }
        $this->setAlign("center", "center");
    }

    public function onDraw() {
        $this->frame_team_blue->clearComponents();
        $this->frame_team_red->clearComponents();
        $this->frame_rounds->clearComponents();
        $sizeX = 60;
        $red = new \ManiaLib\Gui\Elements\Label($sizeX, 6);
        $red->setText('$f00Red Team');
        $red->setAlign("center", "center");
        $blue = new \ManiaLib\Gui\Elements\Label($sizeX, 6);
        $blue->setText('$00FBlue Team');
        $blue->setAlign("center", "center");

        $this->frame_team_blue->addComponent($blue);
        $this->frame_team_red->addComponent($red);

        $x = 0;
        foreach (ESportsManager::$playerStatuses as $login => $player) {
            if ($player === null)
                continue;

            if ($this->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM) {
                if ($player->player->teamId == 0) {
                    $this->frame_team_blue->addComponent(new PlayerStatusItem($x, $player, $sizeX));
                }
                if ($player->player->teamId == 1) {
                    $this->frame_teams_red->addComponent(new PlayerStatusItem($x, $player, $sizeX));
                }
            } else {
                $this->frame_rounds->addComponent(new PlayerStatusItem($x, $player, $sizeX));
            }
            $x++;
        }
        parent::onDraw();
    }

    public function setGamemode($gamemode) {
        $this->gameMode = $gamemode;
    }

    public function onResize($oldX, $oldY) {

        $sX = $this->getSizeX();
        $sY = $this->getSizeY();
        //    $this->background->setSize($sX, $sY);
        $this->lbl_nextMatch->setPosition(($sX / 2), 0);
        $this->lbl_info->setPosition(($sX / 2), -10);
        $this->btn_ready->setPosition(($sX / 2) - 30, -$sY + 18);
        $this->btn_continue->setPosition(($sX / 2), -$sY + 18);
        $this->btn_wait->setPosition(($sX / 2) + 30, -$sY + 18);

        $this->frame_team_blue->setPosition(($sX / 2) - 60, -24);
        $this->frame_team_red->setPosition(($sX / 2) + 60, -24);
        $this->frame_rounds->setPosition(($sX / 2), -24);
        parent::onResize($oldX, $oldY);
    }

}
