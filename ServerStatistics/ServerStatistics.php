<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\ActionHandler;

class ServerStatistics extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $serverStatAction = -1;
    public static $serverMemAction = -1;
    public static $serverCpuAction = -1;
    public static $serverPlayerAction = -1;

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
        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("\\ManiaLivePlugins\\eXpansion\\Database\\Database"));

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
        if ($this->isPluginLoaded('\ManiaLivePlugins\oliverde8\HudMenu\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }

        $this->startTime = time();

        $aHandler = ActionHandler::getInstance();
        self::$serverStatAction = $aHandler->createAction(array($this, 'showStats'));
        self::$serverCpuAction = $aHandler->createAction(array($this, 'showCpu'));
        self::$serverMemAction = $aHandler->createAction(array($this, 'showMemory'));
        self::$serverPlayerAction = $aHandler->createAction(array($this, 'showPlayers'));
    }

    public function exp_onLoad() {
        parent::exp_onLoad();
        $this->enableDedicatedEvents();
        Gui\Windows\StatsWindow::$mainPlugin = $this;
    }

    public function exp_onReady() {
        parent::exp_onReady();
        $this->enableTickerEvent();

        $this->registerChatCommand("serverstat", "showStats", 0, true);

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
            $this->db->execute($q);
        }

        //Checking the version if the table
        $version = $this->callPublicMethod('\ManiaLivePlugins\\eXpansion\\Database\\Database', 'getDatabaseVersion', 'exp_records');
        if (!$version) {
            $version = $this->callPublicMethod('\ManiaLivePlugins\\eXpansion\\Database\\Database', 'setDatabaseVersion', 'exp_records', 1);
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

        if ($this->ellapsed % 120 == 0) {
            $memory = $this->metrics->getFreeMemory();
            $q = 'INSERT INTO `exp_server_stats` (`server_login`, `server_gamemode`, `server_nbPlayers`, `server_nbSpec`
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

            $this->db->execute($q);
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

    public function showStats($login) {

        $data = array();

        //Statistics
        $data['avgPlayer'] = 'unknown';
        $data['avgSpec'] = 'unknown';
	$sql = 'SELECT AVG(server_nbPlayers) as avgPlayer, AVG(server_nbSpec) as avgSpec'
		. ' FROM exp_server_stats'
		. ' WHERE server_login = ' . $this->db->quote($this->storage->serverLogin);
	$result = $this->db->execute($sql)->fetchArrayOfObject();

	foreach ($result as $r) {
	    $data['avgPlayer'] = $r->avgPlayer;
	    $data['avgSpec'] = $r->avgSpec;
	}

        //NbPlayers & Nations
        $data['nbPlayer'] = 'unknown';
        $data['nbNation'] = 'unknown';
        $sql = 'SELECT COUNT(*) as nbPlayer, COUNT(DISTINCT player_nation) as nbNation'
                . ' FROM exp_players';
        $result = $this->db->execute($sql)->fetchArrayOfObject();
        foreach ($result as $r) {
            $data['nbPlayer'] = $r->nbPlayer;
            $data['nbNation'] = $r->nbNation;
        }
        $formatter = \ManiaLivePlugins\eXpansion\Gui\Formaters\LongDate::getInstance();
        $data['upTime'] = $formatter->format(time() - $this->startTime);

        $win = Gui\Windows\StatsWindow::Create($login);
        $win->setData($data, $this->storage);
        $win->setTitle(__('Welcome to : %1$s', $login, $this->storage->server->name));
        $win->setSize(75, 70);
        $win->show($login);
    }

    public function showPlayers($login) {
        $startTime = (time() - (24 * 60 * 60));

        Gui\Windows\PlotterWindow::Erase($login);
        $win = Gui\Windows\PlotterWindow::Create($login);
        $win->setTitle(__("Players", $login));
        $win->setSize(170, 110);
        $datas = $this->db->execute("SELECT `server_nbPlayers` as players, server_nbSpec as specs, "
                        . " server_updateDate as date "
                        . " FROM exp_server_stats "
                        . " WHERE server_updateDate > " . $startTime
                        . "     AND server_login = " . $this->db->quote($this->storage->serverLogin)
                        . " ORDER BY `server_updateDate` ASC")->fetchArrayOfObject();
        $win->setLineColor(0, "00f");
        $win->setLineColor(1, "f00");

        $i = 0;
        $out = array();
        $max = 0;

        foreach ($datas as $data) {
            while ($startTime + ($i * 120) - 60 < $data->date) {
                $out[0][] = 0;
                $out[1][] = 0;
                $i++;
            }
            $i++;
            $out[0][] = $data->players;
            $out[1][] = $data->specs;
            if ($max < $data->players)
                $max = $data->players;
            if ($max < $data->specs)
                $max = $data->specs;
        }
        $win->setLimit(12 * 60, (((int) ($max / 5)) + 1) * 5);
        $win->setDatas($out);
        $win->setXLabels($this->getXDateLabels($startTime));
        $win->show($login);
    }

    public function showMemory($login) {
        $startTime = (time() - (24 * 60 * 60));

        Gui\Windows\PlotterWindow::Erase($login);
        $win = Gui\Windows\PlotterWindow::Create($login);
        $win->setTitle(__("Memory usage", $login));
        $win->setSize(170, 110);
        $datas = $this->db->execute("SELECT `server_ramTotal` as total, "
                        . "`server_ramFree` as free, "
                        . "`server_phpRamUsage` as phpram, "
                        . "server_updateDate as date "
                        . " FROM exp_server_stats "
                        . " WHERE server_updateDate > " . $startTime
                        . "     AND server_login = " . $this->db->quote($this->storage->serverLogin)
                        . " ORDER BY `server_updateDate` ASC")->fetchArrayOfObject();

        $win->setLineColor(0, "f90");
        $win->setLineColor(1, "f00");
        $out = array();
        $i = 0;
        $memory_limit = ini_get('memory_limit');
        if ($memory_limit != -1) {
            if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
                if ($matches[2] == 'M') {
                    $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
                } else if ($matches[2] == 'K') {
                    $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
                }
            }
        }


        foreach ($datas as $data) {
            if ($memory_limit == -1) {
                $memory_limit = $data->total;
            }
            while ($startTime + ($i * 120) - 60 < $data->date) {
                $out[0][] = $memory_limit;
                $out[1][] = 0;
                $i++;
            }
            $i++;
            $out[0][] = $memory_limit;
            $out[1][] = $data->phpram;
        }

        $win->setLimit(12 * 60, $memory_limit);
        $win->setDatas($out);
        $win->setXLabels($this->getXDateLabels($startTime));

        $labels = array();
        for ($i = 0; $i < 5; $i++) {
            $labels[] = ((int) (($memory_limit - ($i * ($memory_limit / 5))) / (1024 * 1024))) . "M";
        }
        $win->setYLabels($labels);

        $win->show($login);
    }

    public function showCpu($login) {
        $startTime = (time() - (24 * 60 * 60));

        Gui\Windows\PlotterWindow::Erase($login);
        $win = Gui\Windows\PlotterWindow::Create($login);
        $win->setTitle(__("Cpu usage", $login));
        $win->setSize(170, 110);
        $datas = $this->db->execute("SELECT `server_load` as cpuload, server_updateDate as date"
                        . " FROM exp_server_stats "
                        . " WHERE server_updateDate > " . $startTime
                        . "     AND server_login = " . $this->db->quote($this->storage->serverLogin)
                        . " ORDER BY `server_updateDate` ASC")->fetchArrayOfObject();

        $out = array();
        $win->setLineColor(0, "f00");
        $i = 0;
        foreach ($datas as $data) {
            while ($startTime + ($i * 120) - 60 < $data->date) {
                $out[0][] = 0;
                $i++;
            }
            $i++;
            $out[0][] = $data->cpuload;
        }

        $win->setLimit(12 * 60, 100);
        $win->setDatas($out);
        $win->setXLabels($this->getXDateLabels($startTime));

        $win->show($login);
    }

    private function getXDateLabels($startTime) {
        $labels = array();
        for ($i = 0; $i < 4; $i++) {
            $labels[] = date("Y-m-d H:i", $startTime + ($i * ((24 * 60 * 60) / 3)));
        }
        return $labels;
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
        $player = \Maniaplanet\DedicatedServer\Structures\PlayerInfo::fromArray($playerInfo);
        $login = $player->login;

        $this->removePlayer($player->login);

        if ($player->pureSpectator) {
            $this->spectators[$login] = $login;
        } else {
            $this->players[$login] = $login;
        }
    }

    public function onOliverde8HudMenuReady($menu) {

        $parent = $menu->findButton(array("menu", "Statistics"));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Statistics";
            $parent = $menu->addButton("menu", "Statistics", $button);
        }

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "ProfileAdvanced";
        $parent = $menu->addButton($parent, "Server Stats", $button);

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Solo";
        $button["plugin"] = $this;
        $button["function"] = "showPlayers";
        $menu->addButton($parent, "nb Player Graph", $button);

        $button["style"] = "UIConstructionSimple_Buttons";
        $button["substyle"] = "Replay";
        $button["plugin"] = $this;
        $button["function"] = "showCpu";
        $menu->addButton($parent, "Cpu usage", $button);

        $button["style"] = "UIConstructionSimple_Buttons";
        $button["substyle"] = "Challenge";
        $button["plugin"] = $this;
        $button["function"] = "showMemory";
        $menu->addButton($parent, "Memory usage", $button);
    }

}

?>
