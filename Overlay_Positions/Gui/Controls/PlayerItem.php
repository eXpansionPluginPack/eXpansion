<?php

namespace ManiaLivePlugins\eXpansion\Overlay_Positions\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use \ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class PlayerItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $label_index, $label_nickname, $label_time, $label_diff, $label_points;
    protected $frame;

    function __construct($indexNumber, \ManiaLivePlugins\eXpansion\Core\Structures\ExpPlayer $player, $login, $gamemode, $sizeX)
    {
        $sizeY = 4.5;

        $this->bg = new \ManiaLib\Gui\Elements\Quad();
        $this->bg->setStyle(\ManiaLib\Gui\Elements\Bgs1::Bgs1InRace);
        $this->bg->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgCardList);
        $this->bg->setAlign("left", "center");
        $this->bg->setSize($sizeX, $sizeY);
        $this->bg->setPosX(-3);
        $this->addComponent($this->bg);

        $color = 'eee';
        if ($player->teamId === 0) {
            $color = '2cf';
        }
        if ($player->teamId === 1) {
            $color = 'f22';
        }
        if ($player->login == $login)
            $color = '5d5';

        if ($player->teamId === 0 && $player->login == $login) {
            $color = "0bf";
        }
        if ($player->teamId === 1 && $player->login == $login) {
            $color = "f00";
        }

        if ($player->hasRetired && !$player->isFinished)
            $color = '999';
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label_index = new \ManiaLib\Gui\Elements\Label(8, 4);
        $this->label_index->setAlign('left', 'center');
        $this->label_index->setText(($player->position + 1) . ".");
        $this->label_index->setScale(0.8);
        $this->label_index->setTextColor($color);
        $this->frame->addComponent($this->label_index);

        $this->label_nickname = new \ManiaLib\Gui\Elements\Label(36, 4);
        $this->label_nickname->setAlign('left', 'center');
        $nickname = ($player->nickName);
        if ($player->hasRetired)
            $nickname = Formatting::stripColors($player->nickName);
        $this->label_nickname->setText($nickname);
        $this->label_nickname->setScale(0.8);
        $this->label_nickname->setTextColor($color);
        $this->frame->addComponent($this->label_nickname);

        $spacer = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->frame->addComponent($spacer);

        $this->label_time = new \ManiaLib\Gui\Elements\Label(16, 4);
        $this->label_time->setAlign('left', 'center');
        $time = \ManiaLive\Utilities\Time::fromTM($player->time);
        if ($player->hasRetired && !$player->isFinished)
            $time = "";
        if (substr($time, 0, 3) === "0:0") {
            $time = substr($time, 3);
        } else if (substr($time, 0, 2) === "0:") {
            $time = substr($time, 2);
        }

        $this->label_time->setText($time);
        $this->label_time->setScale(0.8);
        $this->label_time->setTextColor($color);
        $this->frame->addComponent($this->label_time);

        $spacer = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->frame->addComponent($spacer);

        $this->label_diff = new \ManiaLib\Gui\Elements\Label(16, 4);
        $this->label_diff->setAlign('left', 'center');
        $diff = \ManiaLive\Utilities\Time::fromTM($player->deltaTimeTop1);
        if (substr($diff, 0, 3) === "0:0") {
            $diff = substr($diff, 3);
        } else if (substr($diff, 0, 2) === "0:") {
            $diff = substr($diff, 2);
        }
        $diff = "+" . $diff;
        if ($player->deltaCpCountTop1 > 0)
            $diff = "+" . $player->deltaCpCountTop1 . "cp";
        if ($player->hasRetired && !$player->isFinished)
            $diff = "Out";
        if ($player->deltaTimeTop1 == -1)
            $diff = "Err";

        $this->label_diff->setText($diff);
        $this->label_diff->setScale(0.8);
        $this->label_diff->setTextColor($color);
        $this->frame->addComponent($this->label_diff);

        $this->label_points = new \ManiaLib\Gui\Elements\Label(16, 4);
        $this->label_points->setAlign('left', 'center');

        $score = $player->score;
        if ($gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM)
            $score = $player->matchScore;
        if (empty($score))
            $score = 0;
        if ($gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS) {
            $this->label_points->setText("lap " . $player->curLap);
        } else {
            $this->label_points->setText($score . " pts");
        }
        $this->label_points->setScale(0.8);
        $this->label_points->setTextColor($color);
        $this->frame->addComponent($this->label_points);


        $this->setSize($sizeX, $sizeY);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->erase();
    }

// manialive 3.1 override to do nothing.
    function destroy()
    {
        $this->erase();
    }

    /*
     * custom function to remove contents.
     */

    function erase()
    {
        $this->destroyComponents();
        parent::destroy();
    }

}

?>

