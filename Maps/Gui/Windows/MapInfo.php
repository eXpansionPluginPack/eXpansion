<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use Exception;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj;
use ManiaLivePlugins\eXpansion\Helpers\GbxReader\Map;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;
use ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Controls\InfoLine;
use Maniaplanet\DedicatedServer\Connection;

/**
 * Description of MapInfo
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class MapInfo extends Window
{
    /** @var  Frame */
    protected $frame;
    protected $frame2;

    /** @var Connection */
    protected $connection;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->frame = new Frame();
        $this->frame->setLayout(new Column(80, 100));
        $this->frame->setScale(.8);
        $this->mainFrame->addComponent($this->frame);

        $this->frame2 = clone $this->frame;
        $this->frame2->setPosition(93);
        $this->mainFrame->addComponent($this->frame2);
    }

    public function setMap($uid = null)
    {

        $storage = Storage::getInstance();
        if ($uid == null) {
            $uid = $storage->currentMap->uId;
        }

        $this->frame->clearComponents();
        $this->frame2->clearComponents();

        $map = ArrayOfObj::getObjbyPropValue($storage->maps, "uId", $uid);
        $map->{"nick"} = "n/a";

        if ($map === false) {
            return false;
        }

        $this->setTitle('Map Information', $map->name);

        try {
            $connection = Singletons::getInstance()->getDediConnection();
            $mapPath = $connection->getMapsDirectory();
            $gbxInfo = Map::read($mapPath . DIRECTORY_SEPARATOR . $map->fileName);
            if ($gbxInfo) {
                $model = $gbxInfo->playerModel;
                $map->mood = $gbxInfo->mood;
                $map->nbLaps = $gbxInfo->nbLaps;
                $map->authorTime = $gbxInfo->authorTime;
                $map->silverTime = $gbxInfo->silverTime;
                $map->bronzeTime = $gbxInfo->bronzeTime;
                $map->playerModel = $gbxInfo->playerModel;
                $map->{"nick"} = $gbxInfo->author->nickname;
            }
        } catch (Exception $ex) {
            \ManiaLive\Utilities\Console::println("Info: Map not found or error while reading gbx info for map.");
        }

        /*
         * First columns of data
         */
        $this->frame->addComponent(new InfoLine(5, "Unique Id", $map->uId, 0, 80, false, true));
        $mapData = array(
            "fileName" => "File Name",
            "name" => "Name",
            "author" => "Author",
            "nick" => "Author Nick",
            "mood" => "Mood",
            "mapStyle" => "Map Style",
            "mapType" => "Map Type",
            "environnement" => "Environment"
        );

        foreach ($mapData as $field => $descr) {
            $this->frame->addComponent(new InfoLine(5, $descr, $map->{$field}, 0, 80, false));
        }

        /*
         * Put data in second frame
         */
        $mapData =  array(
            'playerModel' => 'Car Type',
            'addTime' => 'Add Date',
        );
        foreach ($mapData as $field => $descr) {
            $this->frame2->addComponent(new InfoLine(5, $descr, $map->{$field}, 0, 40));
        }

        // time datas
        $mapData = array(
            "authorTime" => "Author Time",
            "goldTime" => "Gold Time",
            "silverTime" => "Silver Time",
            "bronzeTime" => "Bronze Time"
        );
        foreach ($mapData as $field => $descr) {
            $this->frame2->addComponent(new InfoLine(5, $descr, Time::fromTM($map->{$field}), 0, 40));
        }

        // integer values
        $mapData = array(
            "nbCheckpoint" => "Checkpoints",
            "nbLap" => "Laps",
            "copperPrice" => "Display Cost"
        );
        foreach ($mapData as $field => $descr) {
            $this->frame2->addComponent(new InfoLine(5, $descr, strval($map->{$field}), 0, 40));
        }

        return true;
    }

    protected function onHide()
    {
        parent::onHide();
        $this->connection = null;
    }
}
