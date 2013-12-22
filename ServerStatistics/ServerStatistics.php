<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics;

use ManiaLive\Event\Dispatcher;

class ServerStatistics extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $os;
    private $settings;
    private $startTime;
    private $ellapsed = 0;
    public $nbPlayerMax = 0;
    public $nbPlayer = 0;
    public $nbSpecMax = 0;
    public $nbSpec = 0;
    private $lastInfo;
    private $players = array();
    private $spectators = array();

    function exp_onInit() {
        global $lang;
        //The Database plugin is needed. 
        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("eXpansion\Database"));

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
        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }

        $this->startTime = time();

        // Timer
        define('TIME_START', microtime(true));

        // Are we running from the CLI?
        if (isset($argc) && is_array($argv))
            define('LINFO_CLI', true);

        // Version
        define('AppName', 'Linfo');
        define('VERSION', '1.9');

        // Anti hack, as in allow included files to ensure they were included
        define('IN_INFO', true);

        // Configure absolute path to local directory
        define('LOCAL_PATH', dirname(__FILE__) . '/');

        // Configure absolute path to stored info cache, for things that take a while
        // to find and don't change, like hardware devcies
        define('CACHE_PATH', dirname(__FILE__) . '/cache/');

        // It exists; just include it
        require_once LOCAL_PATH . 'config.inc.php';

        // This is essentially the only extension we need, so make sure we have it
        if (!extension_loaded('pcre') && !function_exists('preg_match') && !function_exists('preg_match_all')) {
            $message = 'ServerStatistics needs the `pcre` extension to be loaded. http://us2.php.net/manual/en/book.pcre.php';
            $this->dumpException($message, new \Exception('`pcre` is missing'));
            exit(1);
        }

        // Make sure these are arrays
        $settings['hide']['filesystems'] = is_array($settings['hide']['filesystems']) ? $settings['hide']['filesystems'] : array();
        $settings['hide']['storage_devices'] = is_array($settings['hide']['storage_devices']) ? $settings['hide']['storage_devices'] : array();

        // Make sure these are always hidden
        $settings['hide']['filesystems'][] = 'rootfs';
        $settings['hide']['filesystems'][] = 'binfmt_misc';

        // Load libs
        require_once LOCAL_PATH . 'lib/functions.init.php';
        require_once LOCAL_PATH . 'lib/functions.misc.php';
        require_once LOCAL_PATH . 'lib/functions.display.php';
        require_once LOCAL_PATH . 'lib/class.LinfoTimer.php';
        require_once LOCAL_PATH . 'lib/interface.LinfoExtension.php';
        require_once LOCAL_PATH . 'lib/class.LinfoTimer.php';
        require_once LOCAL_PATH . 'lib/class.LinfoError.php';

        // Default timeformat
        $settings['dates'] = array_key_exists('dates', $settings) ? $settings['dates'] : 'm/d/y h:i A (T)';
        $settings['language'] = 'en';
        $settings['compress_content'] = false;

        require_once LOCAL_PATH . 'lang/' . $settings['language'] . '.php';

        $this->os = determineOS();
        $this->settings = $settings;
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
          `server_load` INT( 3 ) NOT NULL,
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
                $this->nbPlayer++;
                $this->players[$player->login] = true;
            }
        }
        foreach ($this->storage->spectators as $player) {
            if ($player->isConnected) {
                $this->nbSpec++;
                $this->spectators[$player->login] = true;
            }
        }
        $this->nbSpecMax = $this->nbSpec;
        $this->nbPlayerMax = $this->nbPlayer;
    }

    public function onTick() {
        parent::onTick();

        if ($this->ellapsed % 120 == 0) {
            $getter = parseSystem($this->os, $this->settings);
            $info = $getter->getAll();

            // calculate uptime timestamp
            $values = array();
            preg_match_all('/([0-9]+) (years|months|days|hours|minutes|seconds)/', $info['UpTime'], $values);
            $str = "";
            foreach ($values[0] as $value) {
                $str .= $value . " ";
            }
            $uptime = strtotime($str) - time();

            $q = 'INSERT INTO `exp_server_stats` (`server_login`, `server_gamemode`, `server_nbPlayers`, server_nbSpec
                            ,`server_mlRunTime`, `server_upTime`, `server_load`, `server_ramTotal`, `server_ramFree`
                            , `server_phpRamUsage`, `server_updateDate` )
                        VALUES(' . $this->db->quote($this->storage->serverLogin) . ',
                            ' . $this->db->quote($this->storage->gameInfos->gameMode) . ',
                            ' . $this->db->quote($this->nbPlayerMax) . ',
                            ' . $this->db->quote($this->nbSpecMax) . ',
                            ' . $this->db->quote(time() - $this->startTime) . ',
                            ' . $this->db->quote($uptime) . ',
                            ' . $this->db->quote(str_replace('%', '', $info['Load'])) . ',
                            ' . $this->db->quote($info['RAM']['total']) . ',
                            ' . $this->db->quote($info['RAM']['free']) . ',
                            ' . $this->db->quote(memory_get_usage()) . ',
                            ' . $this->db->quote(time()) . '
                        )';
            $this->nbPlayerMax = $this->nbPlayer;
            $this->nbSpecMax = $this->nbSpec;

            $this->lastInfo = $info;
            $this->db->query($q);
        }

        $this->ellapsed = ($this->ellapsed + 1) % 120;
    }

    public function onPlayerConnect($login, $isSpectator) {
        parent::onPlayerConnect($login, $isSpectator);

        if ($isSpectator) {
            $this->nbSpec++;
            if ($this->nbSpec > $this->nbSpecMax)
                $this->nbSpecMax = $this->nbSpec;
        }
        else {
            $this->nbPlayer++;
            if ($this->nbPlayer > $this->nbPlayerMax)
                $this->nbPlayerMax = $this->nbPlayer;
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason) {
        $player = $this->storage->getPlayerObject($login);

        if ($player->isSpectator)
            $this->nbSpec--;
        else
            $this->nbPlayer--;
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        parent::onBeginMap($map, $warmUp, $matchContinuation);

        $this->nbPlayer = 0;
        foreach ($this->storage->players as $player) {
            if ($player->isConnected) {
                $this->nbPlayer++;
                $this->players[$player->login] = true;
            }
        }

        $this->nbSpec = 0;
        foreach ($this->storage->spectators as $player) {
            if ($player->isConnected) {
                $this->nbSpec++;
                $this->spectators[$player->login] = true;
            }
        }
        $this->nbSpecMax = $this->nbSpec;
        $this->nbPlayerMax = $this->nbPlayer;
    }

    public function onOliverde8HudMenuReady($menu) {
        
    }

}

?>
