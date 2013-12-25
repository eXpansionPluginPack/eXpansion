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

    protected $background, $lbl_nextMatch, $lbl_info, $btn_ready, $btn_wait;

    /** @var \ManiaLive\Gui\Controls\Frame */
    protected $frame_team_blue, $frame_teams_red, $frame_rounds;
    public static $actions;

    public function onConstruct() {
        parent::onConstruct();
        echo "onReady\n";
        $login = $this->getRecipient();

        $this->background = new \ManiaLib\Gui\Elements\Quad();
        $this->background->setAlign("left", "top");
        $this->addComponent($this->background);


        $this->lbl_nextMatch = New \ManiaLib\Gui\Elements\Label(60, 6);
        $this->lbl_nextMatch->setStyle(\ManiaLib\Gui\Elements\Format::TextTitle3Header);
        $this->lbl_nextMatch->setAlign("center", "top");
        $this->lbl_nextMatch->setText(ESportsManager::$matchSettings->matchTitle);
        $this->addComponent($this->lbl_nextMatch);

        $this->lbl_info = New \ManiaLib\Gui\Elements\Label(60, 6);
        $this->lbl_info->setText(ESportsManager::$matchSettings->rulesText);
        $this->lbl_info->setAlign("left");
        $this->addComponent($this->lbl_info);

        $this->frame_team_blue = new \ManiaLive\Gui\Controls\Frame(0, 0);
        $this->frame_team_blue->setLayout(new \ManiaLib\Gui\Layouts\Column(60, 6));
        $this->addComponent($this->frame_team_blue);

        $this->frame_team_red = new \ManiaLive\Gui\Controls\Frame();
        $this->frame_team_red->setLayout(new \ManiaLib\Gui\Layouts\Column(60, 6));
        $this->addComponent($this->frame_team_red);

        $this->frame_rounds = new \ManiaLive\Gui\Controls\Frame();
        $this->frame_rounds->setLayout(new \ManiaLib\Gui\Layouts\VerticalFlow(60, 40));
        $this->addComponent($this->frame_rounds);

        $this->btn_ready = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_ready->setAction(self::$actions['ready']);
        $this->btn_ready->setText(__("Ready", $login));
        $this->btn_ready->colorize("0d0");
        $this->addComponent($this->btn_ready);

        $this->btn_wait = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_wait->setAction(self::$actions['notReady']);
        $this->btn_wait->setText(__("Not Ready", $login));
        $this->btn_wait->colorize("dd0");
        $this->addComponent($this->btn_wait);
    }

    public function onDraw() {
        $this->frame_team_blue->clearComponents();
        $this->frame_team_red->clearComponents();
        $this->frame_rounds->clearComponents();
        $sizeX = 90;
        $x = 0;
        foreach (ESportsManager::$playerStatuses as $login => $player) {
            if (ESportsManager::$matchSettings->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM) {
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

    public function onResize($oldX, $oldY) {

        $sX = $this->getSizeX();
        $sY = $this->getSizeY();
        $this->background->setSize($sX, 0);
        $this->lbl_nextMatch->setPosition(0, 0);
        $this->lbl_nextMatch->setPosition(0, - 6);
        $this->btn_ready->setPosition(($sX / 2) - 30, -$sY - 18);
        $this->btn_wait->setPosition(($sX / 2) + 30, -$sY - 18);

        $this->frame_team_blue->setPosition(($sX / 2) - 60, -24);
        $this->frame_team_red->setPosition(($sX / 2) + 60, -24);
        $this->frame_rounds->setPosition(0, $sY - 24);
        parent::onResize($oldX, $oldY);
    }

}
