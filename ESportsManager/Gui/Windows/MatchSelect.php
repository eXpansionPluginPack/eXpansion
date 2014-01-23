<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Gui\Windows;

use \ManiaLivePlugins\eXpansion\ESportsManager\ESportsManager;

/**
 *
 * @author Reaby
 */
class MatchSelect extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $label_halt, $btn_apply, $btn_cancel, $label_reason, $frame, $line;
    private $organizers = array();
    private $rulesDir, $dir;
    private $settingsFile = "";

    /** @var  \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    /** @var GameInfos */
    private $nextGameInfo;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \Maniaplanet\DedicatedServer\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->rulesDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "rules" . DIRECTORY_SEPARATOR;
        $this->setTitle(__("Select Match", $login));

        $this->line = new \ManiaLive\Gui\Controls\Frame(0, -6);
        $this->line->setLayout(new \ManiaLib\Gui\Layouts\Column(120, 6));
        $this->line->setAlign("center", "top");
        $this->dir = $this->rulesDir;
        $this->addComponent($this->line);

        $this->btn_apply = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_apply->setText(__("Apply", $login));
        $this->btn_apply->setAction($this->createAction(array($this, "apply")));
        $this->addComponent($this->btn_apply);

        $this->btn_cancel = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_cancel->setText(__("Cancel", $login));
        $this->btn_cancel->setAction($this->createAction(array($this, "cancel")));
        $this->addComponent($this->btn_cancel);
    }

    public function apply($login) {
        if (empty($this->settingsFile))
            return;
        $this->readSettingsFile($login, $this->settingsFile);
        $this->Erase($login);
    }

    public function cancel($login) {
        $this->Erase($login);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->line->setPosX($this->sizeX / 2);
        $this->btn_apply->setPosition(($this->sizeX / 2) - 10, -$this->sizeY + 6);
        $this->btn_cancel->setPosition(($this->sizeX / 2) + 10, -$this->sizeY + 6);
    }

    public function setDirectory($login, $dir) {
        $this->settingsFile = '';
        $this->dir = $dir;
        $this->redraw($this->getRecipient());
    }

    public function setFile($login, $dir) {
        $this->settingsFile = $dir;
        $this->redraw($this->getRecipient());
    }

    public function readSettingsFile($login, $file) {
        $ini = parse_ini_file($file, true);
        // return on error
        if (!$ini) {
            $login = $this->getRecipient();
            $this->connection->chatSendServerMessage(__("Error while parsing match file.", $login), $login);
            return;
        }
        try {
            ESportsManager::$nextMatchSettings = new \ManiaLivePlugins\eXpansion\ESportsManager\Structures\MatchSetting();
            ESportsManager::$nextMatchSettings->gameInfos = $this->parseGameInfos($ini);
            ESportsManager::$nextMatchSettings->matchTitle = $ini['Match']['name'];
            ESportsManager::$nextMatchSettings->matchOrganizer = $ini['Match']['matchOrganizer'];
            ESportsManager::$nextMatchSettings->rulesText = $ini['Match']['rulesText'];
            $out = $ini['SendChatLines']['line'];
            if (!is_array($ini['SendChatLines']['line']))
                $out = array($ini['SendChatLines']['line']);
            ESportsManager::$nextMatchSettings->adminCommands = $out;
            ESportsManager::$nextMatchSettings->gameMode = ESportsManager::$nextMatchSettings->gameInfos->gameMode;
        } catch (\Exception $e) {
            $login = $this->getRecipient();
            \ManiaLive\Utilities\Console::println($e->getMessage() . ":" . $e->getFile() . ":" . $e->getLine());
            $this->connection->chatSendServerMessage(__("Error while assigning ini values.", $login), $login);
        }
    }

    public function parseGameInfos($ini) {
        $infos = $this->connection->getCurrentGameInfo()->toArray();

        foreach ($ini['GameInfos'] as $setting => $value) {
            if ($value !== null) {
                if (empty($value))
                    continue;

                $newVal = null;
                if ($setting == "gameMode") {
                    $newVal = $this->parseGameMode($value);
                } else {
                    $newVal = $this->parseValue($value);
                }

                if ($newVal !== null) {
                    $infos[$setting] = $newVal;
                }
            }
        }

        $out = \Maniaplanet\DedicatedServer\Structures\GameInfos::fromArray($infos);
        return $out;
    }

    public function parseGameMode($var) {
        switch (strtolower($var)) {
            case "team":
                return \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM;
                break;
            case "ta":
            case "timeattack":
                return \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK;
                break;
            case "rounds":
                return \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS;
                break;
            case "laps":
                return \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS;
                break;
            case "cup":
                return \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP;
                break;
            case "stunts":
                return \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_STUNTS;
                break;
            case "script":
                return \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT;
                break;
        }
        return null;
    }

    public function parseValue($var) {

        if (strpos($var, ':') !== false) {
            return \ManiaLivePlugins\eXpansion\Helpers\TimeConversion::MStoTM($var);
        }
        if (is_numeric($var)) {
            return intval($var);
        }

        $boolCheck = filter_var($var, FILTER_VALIDATE_BOOLEAN);
        if ($boolCheck !== null)
            return $boolCheck;

        return null;
    }

    function onDraw() {
        $this->line->clearComponents();
        $this->line->addComponent(new \ManiaLivePlugins\eXpansion\ESportsManager\Gui\Controls\DirectoryItem(new \SplFileInfo($this->rulesDir), $this, "", 50));
        $dirs = new \DirectoryIterator($this->dir);
        foreach ($dirs as $dir) {
            if (!($dir instanceof \SplFileInfo))
                continue;
            if ($dir->isDot())
                continue;
            $this->line->addComponent(new \ManiaLivePlugins\eXpansion\ESportsManager\Gui\Controls\DirectoryItem($dir, $this, $this->settingsFile, 50));
        }

        parent::onDraw();
    }

}
