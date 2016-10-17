<?php

namespace ManiaLivePlugins\eXpansion\Helpers;

class Paths
{

    private $mapsDirectory;
    private $gameDataDirectory;

    /**
     * @var \ManiaLib\Utils\Path $path ;
     */
    private $path;

    public function __construct()
    {
        $connection = Helper::getSingletons()->getDediConnection();
        $this->mapsDirectory = $connection->getMapsDirectory();
        $this->gameDataDirectory = $connection->gameDataDirectory();
        $this->path = \ManiaLib\Utils\Path::getInstance();
    }

    /**
     * gets manialive root directory
     *
     * @return string
     */
    public function getManialiveRoot()
    {
        return $this->path->getRoot();
    }

    protected function getBaseMap()
    {
        /**
         * @var \ManiaLivePlugins\eXpansion\Core\Config $config
         */
        $config = \ManiaLivePlugins\eXpansion\Core\Config::getInstance();

        return ($config->mapBase == "" ? "" : $config->mapBase . DIRECTORY_SEPARATOR);
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
        return $this->mapsDirectory . $this->getBaseMap() . 'MatchSettings' . DIRECTORY_SEPARATOR;
    }

    public function getDownloadMapsPath()
    {
        return $this->mapsDirectory . $this->getBaseMap() . 'Downloaded'. DIRECTORY_SEPARATOR;
    }

    /**
     * Gets gamedata path
     *
     * @return string
     */
    public function getGameDataPath()
    {
        return $this->gameDataDirectory;
    }

    public function fileHasExtension($fileName, $extension)
    {
        return $extension === "" || substr($fileName, -strlen($extension)) === $extension;
    }
}
