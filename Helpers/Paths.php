<?php

namespace ManiaLivePlugins\eXpansion\Helpers;


class Paths
{

    private $mapsDirectory;

    function __construct()
    {
        $connection          = Helper::getSingletons()->getDediConnection();
        $this->mapsDirectory = $connection->getMapsDirectory();
    }


    protected function getBaseMap()
    {
        /**
         * @var \ManiaLivePlugins\eXpansion\Core\Config $config
         */
        $config = \ManiaLivePlugins\eXpansion\Core\Config::getInstance();

        return ($config->mapBase == "" ? "" : $config->mapBase . '/');
    }

    public function getMapPath()
    {
        return $this->mapsDirectory . $this->getBaseMap();
    }

    public function getDefaultMapPath()
    {
        return $this->mapsDirectory;
    }

    public function getMatchSettingPath()
    {
        return $this->mapsDirectory . $this->getBaseMap() . 'MatchSettings/';
    }

    public function getDownloadMapsPath()
    {
        return $this->mapsDirectory . $this->getBaseMap() . 'Downloaded/';
    }

    public function fileHasExtension($fileName, $extension)
    {
        return $extension === "" || substr($fileName, -strlen($extension)) === $extension;
    }
} 