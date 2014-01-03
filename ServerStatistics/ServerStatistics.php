<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics;

use ManiaLive\Event\Dispatcher;

class ServerStatistics extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $startTime;
    private $ellapsed = 0;
    public $nbPlayerMax = 0;
    public $nbSpecMax = 0;
    private $players = array();
    private $spectators = array();

    /** @var Stats\StatsWindows */
    private $metrics;

    function exp_onInit() {
        global $lang;
        //The Database plugin is needed. 
        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("eXpansion\Database"));

        // Make sure pcre and php_com_dotnet are loaded :)
        if (!extension_loaded('pcre') && !function_exists('preg_match') && !function_exists('preg_match_all')) {
            $message = 'ServerStatistics needs the `pcre` extension to be loaded. http://us2.php.net/manual/en/book.pcre.php';
            $this->dumpException($message, new \Exception('`pcre` is missing'));
            exit(1);
        }
        if (!class_exists("COM")) {
            $extensions = get_loaded_extensions();
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                if (!in_array("php_com_dotnet", $extensions)) {
                    try {
                        if (!(bool) ini_get("enable_dl") || (bool) ini_get("safe_mode")) {
                            $phpPath = get_cfg_var('cfg_file_path');
                            $this->dumpException("Autoloading extensions is not enabled in php.ini.\n\n`php_com_dotnet` extension needs to be enabled for Windows-based systems.\n\nEdit following file $phpPath and set:\n\nenable_dl = On\n\nor add this line:\n\nextension=php_com_dotnet.dll", new \Maniaplanet\WebServices\Exception("Loading extensions is not permitted."));
                            exit(1);
                        }
                        dl("php_com_dotnet");
                    } catch (\Exception $e) {
                        $this->dumpException("Error while trying to autoload extension `php_com_dotnet`", $e);
                        exit(1);
                    }
                }
            }
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->metrics = new Stats\StatsWindows();
        } else {
            $this->metrics = new Stats\StatsLinux();
        }
        //liverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }

        $this->startTime = time();
    }

    public function exp_onLoad() {
        parent::exp_onLoad();
        $this->enableDedicatedEvents();
    }

    public function exp_onReady() {
        parent::exp_onReady();
        $this->enableTickerEvent();
        try {
            $this->enableDatabase();
        } catch (\Exception $e) {
            $this->dumpException("Error while establishing MySQL connection!", $e);
            exit(1);
        }

        if (!$this->db->tableExists("exp_server_stats")) {
            $q = "CREATE TABLE `exp_server_stats` (
          `server_login` VARCHAR( 30 ) NOT NULL,
          `server_gamemode` INT( 2 ) NOT NULL,
          `server_nbPlayers` INT( 3 ) NOT NULL,
          `server_nbSpec` INT( 3 ) NOT NULL,
          `server_mlRunTime` INT( 9 ) NOT NULL,
          `server_upTime` INT( 9 ) NOT NULL,
          `server_load` FLOAT(6,4) NOT NULL,
          `server_ramTotal` BIGINT( 15 ) NOT NULL,
          `server_ramFree` BIGINT( 15 ) NOT NULL,
          `server_phpRamUsage` BIGINT( 15 ) NOT NULL,
          `server_updateDate` INT( 9 ) NOT NULL,
          KEY(`server_login`)
          ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = MYISAM ;";
            $this->db->query($q);
        }

        //Checking the version if the table
        $version = $this->callPublicMethod('eXpansion\Database', 'getDatabaseVersion', 'exp_records');
        if (!$version) {
            $version = $this->callPublicMethod('eXpansion\Database', 'setDatabaseVersion', 'exp_records', 1);
        }

        $this->nbPlayer = 0;
        foreach ($this->storage->players as $player) {
            if ($player->isConnected) {
                $this->players[$player->login] = $player->login;
            }
        }
        foreach ($this->storage->spectators as $player) {
            if ($player->isConnected) {
                $this->spectators[$player->login] = $player->login;
            }
        }
        $this->nbSpecMax = sizeof($this->spectators);
        $this->nbPlayerMax = sizeof($this->players);
    }

    public function onTick() {
        parent::onTick();

        if ($this->ellapsed % 120 == 0) {
            $memory = $this->metrics->getFreeMemory();
            $q = 'INSERT INTO `exp_server_stats` (`server_login`, `server_gamemode`, `server_nbPlayers`, server_nbSpec
          ,`server_mlRunTime`, `server_upTime`, `server_load`, `server_ramTotal`, `server_ramFree`
          , `server_phpRamUsage`, `server_updateDate` )
          VALUES(' . $this->db->quote($this->storage->serverLogin) . ',
          ' . $this->db->quote($this->storage->gameInfos->gameMode) . ',
          ' . $this->db->quote($this->nbPlayerMax) . ',
          ' . $this->db->quote($this->nbSpecMax) . ',
          ' . $this->db->quote(time() - $this->startTime) . ',
          ' . $this->db->quote($this->metrics->getUptime()) . ',
          ' . $this->db->quote($this->metrics->getAvgLoad()) . ',
          ' . $this->db->quote($memory->total) . ',
          ' . $this->db->quote($memory->free) . ',
          ' . $this->db->quote(memory_get_usage()) . ',
          ' . $this->db->quote(time()) . '
          )';

            $this->nbPlayerMax = sizeof($this->players);
            $this->nbSpecMax = sizeof($this->spectators);

            $this->db->query($q);
        }

        $this->ellapsed = ($this->ellapsed + 1) % 120;
    }

    public function onPlayerConnect($login, $isSpectator) {
        if ($isSpectator) {
            $this->spectators[$login] = $login;
            if (sizeof($this->spectators) > $this->nbSpecMax)
                $this->nbSpecMax = sizeof($this->spectators);
        }
        else {
            $this->players[$login] = $login;
            if (sizeof($this->players) > $this->nbPlayerMax)
                $this->nbPlayerMax = sizeof($this->players);
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
        $this->removePlayer($login);
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {

        $this->players = array();
        foreach ($this->storage->players as $player) {
            if ($player->isConnected) {
                $this->nbPlayer++;
                $this->players[$player->login] = $player->login;
            }
        }

        $this->spectators = array();
        foreach ($this->storage->spectators as $player) {
            if ($player->isConnected) {
                $this->spectators[$player->login] = $player->login;
            }
        }
        $this->nbPlayerMax = sizeof($this->players);
        $this->nbSpecMax = sizeof($this->spectators);
    }

    private function removePlayer($login) {
        if (array_key_exists($login, $this->spectators))
            unset($this->spectators[$login]);
        if (array_key_exists($login, $this->players))
            unset($this->players[$login]);
    }

    public function onPlayerInfoChanged($playerInfo) {
        $player = \DedicatedApi\Structures\Player::fromArray($playerInfo);
        $login = $player->login;

        $this->removePlayer($player->login);

        if ($player->pureSpectator) {
            $this->spectators[$login] = $login;
        } else {
            $this->players[$login] = $login;
        }
    }

    public function onOliverde8HudMenuReady($menu) {
        
    }

}

?>
