<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use Exception;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj;
use ManiaLivePlugins\eXpansion\Helpers\GbxReader\Map;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;
use Maniaplanet\DedicatedServer\Connection;

/**
 * Description of MapInfo
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class MapInfo extends Window
{
    protected $frame, $frame2;

    /** @var Connection */
    protected $connection;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->frame = new Frame();
        $this->frame->setPosition(-35, -6);

        $this->addComponent($this->frame);

        $this->frame2 = clone $this->frame;
        $this->frame2->setPosition(40);
        $this->addComponent($this->frame);
        $this->addComponent($this->frame2);
    }

    public function setMap($uid = null)
    {

        $storage = Storage::getInstance();
        if ($uid == null) {
            $uid = $storage->currentMap->uId;
        }

        $x = 35;
        $y = 0;
        $this->frame->clearComponents();
        $this->frame2->clearComponents();

        $map           = ArrayOfObj::getObjbyPropValue($storage->maps, "uId", $uid);
        $map->{"nick"} = "n/a";

        if ($map === false) {
            return false;
        }

        $this->setTitle("Map Info", $map->name);
        $lbl = new Label($x, 6);
        $lbl->setPosition($x, $y);
        $lbl->setText("Unique id");
        $this->frame->addComponent($lbl);
        $lbl = new Inputbox("", $x, 5);
        $lbl->setPosition($x * 2, $y);
        $lbl->setText($map->uId);

        $this->frame->addComponent($lbl);

        $model = "commonCar";
        try {
            $connection = Singletons::getInstance()->getDediConnection();
            $mapPath    = $connection->getMapsDirectory();
            $gbxInfo    = Map::read($mapPath.DIRECTORY_SEPARATOR.$map->fileName);
            if ($gbxInfo) {
                $model           = $gbxInfo->playerModel;
                $map->mood       = $gbxInfo->mood;
                $map->nbLaps     = $gbxInfo->nbLaps;
                $map->authorTime = $gbxInfo->authorTime;
                $map->silverTime = $gbxInfo->silverTime;
                $map->bronzeTime = $gbxInfo->bronzeTime;
                //   $map->nbCheckpoints = $gbxInfo->nbCheckpoints;
                $map->{"nick"}   = $gbxInfo->author->nickname;
            }
        } catch (Exception $ex) {
            \ManiaLive\Utilities\Console::println("Info: Map not found or error while reading gbx info for map.");
        }

        $y-=5;
        $mapData = array("fileName" => "File Name", "name" => "Name", "author" => "Author", "nick" => "Author Nick",
            "mood" => "Mood", "mapStyle" => "Map Style", "mapType" => "Map Type", "environnement" => "Environment");


        foreach ($mapData as $field => $descr) {
            $lbl = new Label($x, 6);
            $lbl->setPosition($x, $y);
            $lbl->setText($descr);
            $this->frame->addComponent($lbl);
            $lbl = new Label("", $x, 6);
            $lbl->setPosition($x * 2, $y);
            $lbl->setText($map->{$field});
            $this->frame->addComponent($lbl);
            $y-=5;
        }

        // player model
        $lbl = new Label($x, 6);
        $lbl->setPosition($x, $y);
        $lbl->setText("Car type");
        $this->frame->addComponent($lbl);
        $lbl = new Label("", $x, 6);
        $lbl->setPosition($x * 2, $y);
        $lbl->setText($gbxInfo->playerModel);
        $this->frame->addComponent($lbl);

// frame 2
        $y = 0;

// add time
        $lbl  = new Label($x, 6);
        $lbl->setPosition($x, $y);
        $lbl->setText("Add Date");
        $this->frame2->addComponent($lbl);
        $lbl  = new Label("", $x, 6);
        $lbl->setPosition($x * 2, $y);
        $date = new \DateTime(now);
        $date->setTimestamp((int) $map->addTime);

        $lbl->setText($date->format("d.m.Y"));
        $this->frame2->addComponent($lbl);
        $y-=5;

        // time datas
        $mapData = array("authorTime" => "Author Time", "goldTime" => "Gold Time", "silverTime" => "Silver Time", "bronzeTime" => "Bronze Time");
        foreach ($mapData as $field => $descr) {
            $lbl = new Label($x, 6);
            $lbl->setPosition($x, $y);
            $lbl->setText($descr);
            $this->frame2->addComponent($lbl);
            $lbl = new Label("", $x, 6);
            $lbl->setPosition($x * 2, $y);
            $lbl->setText(Time::fromTM($map->{$field}));
            $this->frame2->addComponent($lbl);
            $y-=5;
        }

        // integer values
        $mapData = array("nbCheckpoint" => "Checkpoints", "nbLap" => "Laps", "copperPrice" => "Display Cost");
        foreach ($mapData as $field => $descr) {
            $lbl = new Label($x, 6);
            $lbl->setPosition($x, $y);
            $lbl->setText($descr);
            $this->frame2->addComponent($lbl);
            $lbl = new Label("", $x, 6);
            $lbl->setPosition($x * 2, $y);
            $lbl->setText(strval($map->{$field}));
            $this->frame2->addComponent($lbl);
            $y-=5;
        }
        return true;
    }

    protected function onHide()
    {
        parent::onHide();
        $this->connection = null;
    }
}