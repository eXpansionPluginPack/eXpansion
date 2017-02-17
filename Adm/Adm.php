<?php

namespace ManiaLivePlugins\eXpansion\Adm;

use Exception;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\AdminPanel;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ForceScores;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\GameOptions;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\MatchSettings;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\RoundPoints;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ScriptSettings;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerControlMain;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerManagement;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerOptions;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Events\Event;
use ManiaLivePlugins\eXpansion\AdminGroups\Events\Listener;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\Config;
use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Core\I18n\Message;
use ManiaLivePlugins\eXpansion\Core\MetaData as CoreMetaData;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

class Adm extends ExpPlugin implements Listener
{
    /** @var Message Messages needed */
    private $msgScriptSettings;
    /** @var Message Messages needed */
    private $msgDatabasePlugin;
    /** @var Message Messages needed */
    private $msgForceScoreError;

    /**
     * @inheritdoc
     */
    public function eXpOnLoad()
    {
        $this->msgForceScoreError = eXpGetMessage("ForceScores can be used only with rounds or team mode");
        $this->msgScriptSettings = eXpGetMessage("ScriptSettings available only in script mode");
        $this->msgDatabasePlugin = eXpGetMessage("Database plugin not loaded!");

        $this->setPublicMethod('serverControlMain');

        if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups')) {
            Dispatcher::register(Event::getClass(), $this);
        }

        $cmd = AdminGroups::addAdminCommand('setting expansion', $this, 'showExpSettings', 'expansion_settings');
        $cmd->setHelp('Set up your expansion');
        AdminGroups::addAlias($cmd, "setexp"); // xaseco & fast
    }

    /**
     * @inheritdoc
     */
    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();

        ServerControlMain::$mainPlugin = $this;
        RoundPoints::$plugin = $this;
        ForceScores::$mainPlugin = $this;
        AdminPanel::$mainPlugin = $this;


        $cmd = AdminGroups::addAdminCommand(
            'server control',
            $this,
            'serverControlMain',
            Permission::SERVER_CONTROL_PANEL
        );
        $cmd->setHelp('Displays the main control panel for the server');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "server");
        AdminGroups::addAlias($cmd, "options");
        AdminGroups::addAlias($cmd, "control");

        foreach ($this->storage->players as $player) {
            $this->onPlayerConnect($player->login, false);
        }
        foreach ($this->storage->spectators as $player) {
            $this->onPlayerConnect($player->login, true);
        }

        $this->onBeginMap(null, null, null);
    }

    /**
     * Display eXpansion settings.
     *
     * @param string $login The login of the player
     */
    public function showExpSettings($login)
    {
        $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Core\Core', 'showExpSettings', $login);
    }

    /**
     * Called when an admin is added
     *
     * @param string $login The login of the player
     */
    public function eXpAdminAdded($login)
    {
        $this->onPlayerConnect($login, false);
    }

    /**
     * Called when a admin is removed
     *
     * @param string $login The login of the player
     */
    public function eXpAdminRemoved($login)
    {
        AdminPanel::Erase($login);
    }

    /**
     * @inheritdoc
     */
    public function onPlayerConnect($login, $isSpectator)
    {
        if ($this->expStorage->isRelay) {
            return;
        }

        if (AdminGroups::isInList($login)) {
            /** @var AdminPanel $widget */
            $widget = AdminPanel::Create($login);
            $widget->setSize(40, 7);
            $widget->setDisableAxis("x");
            $widget->show($login);
        }
    }

    /**
     * Display server options window
     *
     * @param string $login The login of the player
     */
    public function serverOptions($login)
    {
        if (AdminGroups::getAdmin($login) != null) {
            /** @var ServerOptions $window */
            $window = ServerOptions::Create($login);
            $window->setTitle(__('Server Options', $login));
            $window->centerOnScreen();
            $window->setSize(160, 100);
            $window->show();
        }
    }

    /**
     * Show windows to the set up forced scores
     *
     * @param string $login The login of the player
     */
    public function forceScores($login)
    {
        if (AdminGroups::hasPermission($login, Permission::GAME_SETTINGS)) {
            $gamemode = $this->storage->gameInfos->gameMode;
            if ($gamemode == GameInfos::GAMEMODE_ROUNDS
                || $gamemode == GameInfos::GAMEMODE_TEAM
                || GameInfos::GAMEMODE_CUP
            ) {
                /** @var ForceScores $window */
                $window = ForceScores::Create($login);
                $window->setTitle(__('Force Scores', $login));
                $window->centerOnScreen();
                $window->setSize(160, 80);
                $window->show();
            } else {
                $this->eXpChatSendServerMessage($this->msgForceScoreError, $login);
            }
        }
    }

    /**
     * Function to validated score change
     */
    public function forceScoresOk()
    {
        // @TODO Replace this by a proper event.
        $this->eXpChatSendServerMessage('Notice: Admin has altered the scores of current match!');
        if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\ESLcup\\ESLcup")) {
            $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\ESLcup\\ESLcup", "syncScores");
        }
    }

    /**
     * Show window for game options
     *
     * @param string $login The login of the player
     */
    public function gameOptions($login)
    {
        if (AdminGroups::hasPermission($login, Permission::GAME_SETTINGS)) {
            /** @var GameOptions $window */
            $window = GameOptions::Create($login);
            $window->setTitle(__('Game Options', $login));
            $window->setSize(160, 85);
            $window->centerOnScreen();
            $window->show();
        }
    }

    /**
     * Show the window for server management
     *
     * @param string $login The login of the player
     */
    public function serverManagement($login)
    {
        if (AdminGroups::hasPermission($login, Permission::SERVER_STOP_DEDICATED)
            || AdminGroups::hasPermission($login, Permission::SERVER_STOP_MANIALIVE)
        ) {
            /** @var ServerManagement $window */
            $window = ServerManagement::Create($login);
            $window->setTitle(__('Server Control', $login));
            $window->setSize(90, 30);
            $window->centerOnScreen();
            $window->show();
        }
    }

    /**
     * Show window to customized points
     *
     * @param string $login The login of the player
     */
    public function roundPoints($login)
    {
        if (AdminGroups::hasPermission($login, Permission::GAME_SETTINGS)) {
            /** @var RoundPoints $window */
            $window = RoundPoints::Create($login);
            $window->setTitle(__('Custom Round Points', $login));
            $window->setSize(160, 90);
            $window->centerOnScreen();
            $window->show();
        }
    }

    /**
     * Show window to access all server configurations.
     *
     * @param string $login The login of the player
     */
    public function serverControlMain($login)
    {
        if (AdminGroups::hasPermission($login, Permission::SERVER_CONTROL_PANEL)) {
            $window = ServerControlMain::Create($login);
            $window->setSize(140, 25);
            $window->show();
        }
    }

    /**
     * Show window that allows votes configuration
     *
     * @param string $login The login of the player
     */
    public function showVotesConfig($login)
    {
        if (AdminGroups::hasPermission($login, Permission::SERVER_VOTES)) {
            if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\Votes\Votes')) {
                $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Votes\Votes', 'showVotesConfig', $login);
            }
        }
    }

    /**
     * Show window that allows to start/stop plugins & see list of plugins
     *
     * @param string $login The login of the player
     */
    public function showPluginManagement($login)
    {
        if (AdminGroups::hasPermission($login, Permission::EXPANSION_PLUGIN_START_STOP)) {
            if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad')) {
                $this->callPublicMethod('\ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad', 'showPluginsWindow', $login);
            }
        }
    }

    /**
     * Show window to set up the match settings used
     *
     * @param string $login The login of the player
     *
     */
    public function matchSettings($login)
    {
        if (AdminGroups::hasPermission($login, Permission::GAME_MATCH_SAVE)
            || AdminGroups::hasPermission($login, 'game_matchDelete')
            || AdminGroups::hasPermission($login, 'game_match')
        ) {
            /** @var MatchSettings $window */
            $window = MatchSettings::Create($login);
            $window->setTitle(__('Match Settings', $login));
            $window->centerOnScreen();
            $window->setSize(160, 100);
            $window->show();
        }
    }

    /**
     * Show window for script game settings.
     *
     * @param string $login The login of the player
     */
    public function scriptSettings($login)
    {
        if (AdminGroups::hasPermission($login, Permission::GAME_SETTINGS)) {
            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
                /** @var ScriptSettings $window */
                $window = ScriptSettings::Create($login);
                $window->setTitle(__('Script Settings', $login));
                $window->centerOnScreen();
                $window->setSize(160, 100);
                $window->show();
            } else {
                $this->eXpChatSendServerMessage($this->msgScriptSettings, $login);
            }
        }
    }

    /**
     * Show the window for db tools
     *
     * @param string $login The login of the player
     */
    public function dbTools($login)
    {
        if (AdminGroups::hasPermission($login, Permission::SERVER_DATABASE)) {
            if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\Database\\Database")) {
                $this->callPublicMethod(
                    "\\ManiaLivePlugins\\eXpansion\\Database\\Database",
                    "showDbMaintenance",
                    $login
                );
            } else {
                $this->eXpChatSendServerMessage($this->msgDatabasePlugin, $login);
            }
        }
    }

    /**
     * Skip the current map action
     *
     * @param string $login The login of the player
     */
    public function skipMap($login)
    {
        if (AdminGroups::hasPermission($login, Permission::MAP_SKIP)) {
            if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\ChatAdmin\ChatAdmin")) {
                $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\ChatAdmin\ChatAdmin", "skipMap", $login);
            }
        }
    }

    /**
     * Restart current map action
     *
     * @param string $login The login of the player
     */
    public function restartMap($login)
    {
        if (AdminGroups::hasPermission($login, Permission::MAP_RES)) {
            if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\Maps\\Maps')) {
                $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\Maps\\Maps", "replayMap", $login);

                return;
            }

            $this->connection->restartMap($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP);
            $admin = $this->storage->getPlayerObject($login);
            $this->eXpChatSendServerMessage(
                '#admin_action#Admin#variable# %s #admin_action#restarts the challenge!',
                null,
                array($admin->nickName)
            );
        }
    }

    /**
     * Cancel vote action
     *
     * @param string $login The login of the player
     */
    public function cancelVote($login)
    {
        if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\ChatAdmin\\ChatAdmin")) {
            $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\ChatAdmin\\ChatAdmin", "cancelVote", $login);

            return;
        }
        $this->connection->cancelVote();
    }

    /**
     * End round action
     *
     * @param string $login The login of the player
     */
    public function endRound($login)
    {
        if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\ChatAdmin\\ChatAdmin")) {
            $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\ChatAdmin\\ChatAdmin", "forceEndRound", $login);

            return;
        }
        $this->connection->forceEndRound();
    }

    /**
     * Display admin groups windows to manage admins.
     *
     * @param string $login The login of the player
     */
    public function adminGroups($login)
    {
        AdminGroups::getInstance()->windowGroups($login);
    }

    /**
     * Set the current points for rounds action
     *
     * @param string $login The login of the player
     * @param        $points
     */
    public function setPoints($login, $points)
    {
        try {
            $nick = $this->storage->getPlayerObject($login)->nickName;
            $config = Config::getInstance();
            $intPoints = array();
            foreach ($points as $p) {
                $intPoints[] = intval($p);
            }

            $config->roundsPoints = $intPoints;

            $var = CoreMetaData::getInstance()->getVariable('roundsPoints');
            $var->setRawValue($intPoints);

            ConfigManager::getInstance()->check();


            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
                $points = $intPoints;
                foreach ($points as &$num) {
                    settype($num, 'string');
                }
                unset($num);
                $this->connection->triggerModeScriptEventArray('Rounds_SetPointsRepartition', $points);
                $this->connection->setRoundCustomPoints($intPoints);
            } else {
                $this->connection->setRoundCustomPoints($intPoints);
            }
            $msg = eXpGetMessage(
                '#admin_action#Admin %s $z$s#admin_action#sets custom ' . "round points to #variable#%s"
            );
            $this->eXpChatSendServerMessage($msg, null, array($nick, implode(",", $intPoints)));
        } catch (Exception $e) {
            $this->connection->chatSendServerMessage(__('#admin_error#Error: %s', $login, $e->getMessage()), $login);
        }
    }

    /**
     * @inheritdoc
     */
    public function eXpOnUnload()
    {
        parent::eXpOnUnload();
        AdminPanel::EraseAll();
        ForceScores::EraseAll();
        GameOptions::EraseAll();
        MatchSettings::EraseAll();
        RoundPoints::EraseAll();
        ScriptSettings::EraseAll();
        ServerControlMain::EraseAll();
        ServerManagement::EraseAll();
        ServerOptions::EraseAll();
    }
}
