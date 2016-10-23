<?php

namespace ManiaLivePlugins\eXpansion\LoadScreen;

use Exception;
use ManiaLive\DedicatedApi\Callback\Event;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Core\DataAccess;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\LoadScreen\Gui\Windows\LScreen;
use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap;

/*
 * Copyright (C) 2014 Reaby
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of Widgets_Speedometer
 *
 * @author Reaby
 */
class LoadScreen extends ExpPlugin
{
    private $startTime = 0;
    private $isActive = false;
    private $mxImage = "";

    /** @var DataAccess */
    private $dataAccess;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->enableTickerEvent();
        $this->disableDedicatedEvents(Event::ON_END_MATCH);
        Dispatcher::register(Event::getClass(), $this, Event::ON_END_MATCH, 10);

        $this->dataAccess = DataAccess::getInstance();
        $config = Config::getInstance();
        foreach ($config->screens as $url) {
            Gui::preloadImage($url);
        }
        Gui::preloadUpdate();

        if (Config::getInstance()->screensMx) {
            $this->syncMxImage();
        }
    }

    public function onSettingsChanged(Variable $var)
    {
        if ($var->getName() == "screens") {
            $config = Config::getInstance();
            foreach ($config->screens as $url) {
                Gui::preloadImage($url);
            }
        }
        Gui::preloadUpdate();
    }

    public function onTick()
    {

        $delay = intval(Config::getInstance()->screensDelay);


        if ($this->isActive == true && time() > ($this->startTime + $delay)) {

            $url = "";
            $this->isActive = false;
            $this->startTime = 0;
            $config = Config::getInstance();
            if (count($config->screens) > 0) {
                $index = mt_rand(0, (count($config->screens) - 1));
                $url = $config->screens[$index];
            }

            if (Config::getInstance()->screensMx) {
                if (!empty($this->mxImage)) {
                    $url = $this->mxImage;
                }
            }

            $widget = LScreen::Create(null);
            $widget->setName("loading Screen");
            $widget->setImage($url);
            $widget->show();
        }
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        $this->startTime = time();
        $this->isActive = true;
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->isActive = false;
        LScreen::EraseAll();
    }

    public function onBeginMatch()
    {

        $this->isActive = false;
        LScreen::EraseAll();
        Gui::preloadRemove($this->mxImage);
        Gui::preloadUpdate();

        if (Config::getInstance()->screensMx) {
            $this->syncMxImage();
        }
    }

    private function syncMxImage()
    {
        $uid = urlencode($this->storage->nextMap->uId);

        switch ($this->expStorage->simpleEnviTitle) {
            case "SM":
                $query = 'https://sm.mania-exchange.com/api/tracks/get_track_info/uid/' . $uid;
                break;
            case "TM":
                $query = 'https://tm.mania-exchange.com/api/tracks/get_track_info/uid/' . $uid;
                break;
        }
        $options = array(CURLOPT_HTTPHEADER => array(
            "X-ManiaPlanet-ServerLogin" => $this->storage->serverLogin,
            "Content-Type" => 'application/json',
        ));
        $this->dataAccess->httpCurl($query, array($this, 'xGetImage'), array(), $options);
    }

    public function xGetImage($job, $jobData)
    {
        $info = $job->getCurlInfo();
        $code = $info['http_code'];
        if ($code != 200) {
            return;
        }
        try {
            $json = json_decode($job->getResponse(), true);
            if ($json === null) {
                $this->mxImage = "";
                return;
            }

            $map = MxMap::fromArray($json);
            $game = strtolower($this->expStorage->simpleEnviTitle);

            if ($map->hasScreenshot) {
                $this->mxImage = "http://" . $game . ".mania-exchange.com/tracks/screenshot/normal/"
                    . $map->trackID . "?.png";
                Gui::preloadImage($this->mxImage);
                Gui::preloadUpdate();
            } else {
                $this->mxImage = "";
            }
        } catch (Exception $e) {
            Helper::logError("LoadScreen error: " . $e->getMessage() . " at line " . $e->getLine());
            $this->mxImage = "";
        }
    }

    public function eXpOnUnload()
    {
        Dispatcher::unregister(Event::getClass(), $this, Event::ON_END_MATCH);
    }
}
