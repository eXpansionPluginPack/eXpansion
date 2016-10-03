<?php

namespace ManiaLivePlugins\eXpansion\Database;

use ManiaLib\Utils\Singleton;
use ManiaLive\Data\Storage;
use ManiaLive\Database\Connection;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class SettingsBag extends Singleton implements \ManiaLive\Event\Listener
{

    private $bagSettingsDefault = array();
    private $bagSettingsByLogin = array();
    private $loginMetaData = array();

    /** @var Connection */
    private $db;

    public function __construct()
    {
        $config = \ManiaLive\Database\Config::getInstance();
        $this->db = Connection::getConnection(
            $config->host,
            $config->username,
            $config->password,
            $config->database,
            $config->type,
            $config->port
        );

        $this->initDb();
        $this->loadDefaultSettings();

        $storage = Storage::getInstance();

        foreach ($storage->players as $login => $player) {
            $this->onPlayerConnect($login, false);
        }
        foreach ($storage->spectators as $login => $player) {
            $this->onPlayerConnect($login, true);
        }

        Dispatcher::register(\ManiaLive\DedicatedApi\Callback\Event::getClass(), $this, \ManiaLive\DedicatedApi\Callback\Event::ON_PLAYER_CONNECT, 10);
        Dispatcher::register(\ManiaLive\DedicatedApi\Callback\Event::getClass(), $this, \ManiaLive\DedicatedApi\Callback\Event::ON_PLAYER_DISCONNECT, 10);
    }

    /**
     * set
     * sets key-value pair for this class, saves to database
     *
     * @param class $class
     * @param string $key
     * @param mixed $value
     * @param string|null $login login of player, null saves new default value to everybody
     * @throws \Exception
     */
    public function set($class, $key, $value, $login = null)
    {
        if (get_class($class) === false) {
            throw new \Exception("class not found");
        }
        if (!is_string($key)) {
            throw new \Exception('key is not string');
        }

        if ($login) {
            $this->bagSettingsByLogin[$login][get_class($class)][$key] = $value;
        } else {
            $this->bagSettingsDefault[get_class($class)][$key] = $value;
        }
    }

    /**
     * @param $class
     * @param $key
     * @param null $login
     * @return null|mixed
     * @throws \Exception
     */

    public function get($class, $key, $login = null)
    {
        if (get_class($class) === false) {
            throw new \Exception("class not found");
        }
        if (!is_string($key)) {
            throw new \Exception('key is not string');
        }

        if ($login) {
            if (isset($this->$this->bagSettingsByLogin[$login][get_called_class()][$key])) {
                return $this->$this->bagSettingsByLogin[$login][get_called_class()][$key];
            } else {
                if (isset($this->$this->bagSettingsDefault[get_called_class()][$key])) {
                    return $this->$this->bagSettingsDefault[get_called_class()][$key];
                }
            }
        } else {
            if (isset($this->$this->bagSettingsDefault[get_called_class()][$key])) {
                return $this->$this->bagSettingsDefault[get_called_class()][$key];
            }
        }
        return null;
    }

    public function onPlayerConnect($login, $isSpec)
    {
        $login = strval($login);
        if (!isset($this->bagSettingsByLogin[$login])) {
            $query = "SELECT * FROM `exp_settingsbag` WHERE `settingsbag_login` = " . $this->db->quote($login) . ";";
            $data = $this->db->execute($query);
            $count = $data->recordCount();
            $obj = $data->fetchObject();
            $this->loginMetaData[$login] = $count;
            if ($obj) {
                $this->bagSettingsByLogin[$login] = json_decode($obj->settingsbag_data);
            }
        } else {
            $this->loginMetaData[$login] = 0;
        }
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        $login = strval($login);
        if (isset($this->bagSettingsByLogin[$login])) {

            $settings = json_encode($this->bagSettingsByLogin[$login]);

            if ($this->loginMetaData[$login] == 0) {
                try {
                    $query = "INSERT INTO `exp_settingsbag` (`settingsbag_login`,`settingsbag_data`) VALUES (" .
                        $this->db->quote($login) . "," . $this->db->quote($settings) . ");";

                    $this->db->execute($query);
                    $this->loginMetaData[$login] = 1;
                } catch (\Exception $ex) {
                    Helper::logger('Error while saving settings.' . $ex->getMessage());
                }
            } else {
                try {
                    $query = "UPDATE `exp_settingsbag` (`settingsbag_data`) VALUES (" . $this->db->quote($settings) .
                        ") WHERE `settingsbag_login` = " . $this->db->quote($login) . ";";
                    $this->db->execute($query);
                } catch (\Exception $ex) {
                    Helper::logger('Error while saving settings.' . $ex->getMessage());
                }
            }
            unset($this->bagSettingsByLogin[$login]);
            unset($this->loginMetaData[$login]);
        }
    }

    private function initDb()
    {
        if (!$this->db->tableExists('exp_settingsbag')) {
            $q = "CREATE TABLE `exp_settingsbag` (
                    `settingsbag_id` INTEGER NOT NULL AUTO_INCREMENT,
                    `settingsbag_login` varchar(75) NOT NULL,                   
                    `settingsbag_data` TEXT NOT NULL,                    
                     PRIMARY KEY (`settingsbag_id`),
                     KEY(`settingsbag_login`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM;";
            $this->db->execute($q);
        }
    }

    private function loadDefaultSettings()
    {
        $query = "SELECT * FROM `exp_settingsbag` WHERE `settingsbag_data` = " . $this->db->quote('#default') . ";";
        $data = $this->db->execute($query);
        $count = $data->recordCount();
        $obj = $data->fetchObject();
        $this->loginMetaData['#default'] = $count;
        if ($obj) {
            $this->bagSettingsDefault = json_decode($obj->settingsbag_data);
        }
    }

    public function saveDefaults()
    {
        $settings = json_encode($this->bagSettingsDefault);

        if ($this->loginMetaData['#default'] == 0) {
            $query = "INSERT INTO `exp_settingsbag` (`settingsbag_login`,`settingsbag_data`) VALUES (" .
                $this->db->quote('#default') . "," . $this->db->quote($settings) . ");";

            $this->db->execute($query);
            $this->loginMetaData['#default'] = 1;
        } else {
            $query = "UPDATE `exp_settingsbag` (`settingsbag_data`) VALUES (" . $this->db->quote($settings) .
                ") WHERE `settingsbag_login` = " . $this->db->quote('#default') . ";";
            $this->db->execute($query);
        }

    }
}