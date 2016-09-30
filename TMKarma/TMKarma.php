<?php

namespace ManiaLivePlugins\eXpansion\TMKarma;

use ManiaLive\Application\ErrorHandling;
use ManiaLivePlugins\eXpansion\TMKarma\Gui\Windows\Widget;

class TMKarma extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    /**
     * These are public static values
     * which means, that they can be overriden from
     * the config file.
     */
    public static $posX = 159;
    public static $posY = 52;
    public static $scale = 1;
    public static $countryCode = "";
    public static $login = null;
    private $config = null;

    /** @var Structures\Vote[] */
    private $newVotes = array();

    /**
     * @var \ManiaLivePlugins\eXpansion\TMKarma\Structures\Karma
     */
    protected $karma;

    /**
     * Values that are set for
     * each different vote step.
     */
    const VOTE_FANTASTIC = 3;
    const VOTE_BEAUTIFUL = 2;
    const VOTE_GOOD = 1;
    const VOTE_BAD = -1;
    const VOTE_POOR = -2;
    const VOTE_WASTE = -3;

    public function eXpOnInit()
    {
        $this->config = Config::getInstance();

        // by default we set the server login as authentication login
        self::$login = $this->storage->serverLogin;
        Service::$login = $this->storage->serverLogin;
    }

    public function eXpOnReady()
    {
        // check whether the location has been set in the config
        try {
            if (!empty($this->config->countryCode)) {
                Service::forceCountryCode($this->config->countryCode);
                $this->writeConsole('Your location has been taken from the config: ' . Service::getLocationInfo());
            } else {
                $this->writeConsole('Your location has been detected: ' . Service::getLocationInfo());
            }

            // try to authenticate at tm-karma
            if (Service::Authenticate($this->storage->server->name, self::$login, 'Maniaplanet')) {
                // we are authenticated!
                $this->writeConsole('Successfully authenticated at the tm-karma webservice!');


                $this->enableDedicatedEvents();
                // fake call the begin challenge
                $this->onBeginMap(null, null, null);

                // enable the dedicated server events
                // if these are not enabled, you will not be notified
                // of new players connecting etc.
            } else {
                // if authentication fails, we print a little message
                $this->writeConsole('ERROR: Could not authenticate at the tm-karma webservice!');
            }
        } catch (\Exception $e) {
            ErrorHandling::displayAndLogError($e);

            return;
        }
    }

    /**
     * A new player is connecting to the server.
     *
     * @see libraries/ManiaLive/PluginHandler/ManiaLive\PluginHandler.Plugin::onPlayerConnect()
     */
    public function onPlayerConnect($login, $isSpectator)
    {
        // display a widget with information
        $this->displayWidget($login);
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        // load the new track's karma and stats
        $this->newVotes = array();
        $this->karma = Service::GetChallengeKarma($this->storage->currentMap, $this->storage->players);

        // display the new challenge's karma to all players
        foreach ($this->storage->players as $login => $player) {
            $this->displayWidget($login);
        }
        foreach ($this->storage->spectators as $login => $player) {
            $this->displayWidget($login);
        }
    }

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {
        if ($playerUid == 0) {
            return;
        }

        if ($text == "0/5") {
            $this->doVote($login, -3);
        }
        if ($text == "1/5") {
            $this->doVote($login, -3);
        }
        if ($text == "2/5") {
            $this->doVote($login, -1);
        }
        if ($text == "3/5") {
            $this->doVote($login, 1);
        }
        if ($text == "4/5") {
            $this->doVote($login, 2);
        }
        if ($text == "5/5") {
            $this->doVote($login, 3);
        }

        if ($text == "---") {
            $this->doVote($login, -3);
        }
        if ($text == "--") {
            $this->doVote($login, -2);
        }
        if ($text == "-") {
            $this->doVote($login, -1);
        }
        if ($text == "+") {
            $this->doVote($login, 1);
        }
        if ($text == "++") {
            $this->doVote($login, 2);
        }
        if ($text == "+++") {
            $this->doVote($login, 3);
        }
    }

    /**
     * Displays the Karma Widget to the given player.
     *
     * @param string $login
     */
    protected function displayWidget($login)
    {
        // get the player's widget instance
        // configure and display
        $widget = Widget::Create($login);
        $widget->setKarma($this->karma);
        $widget->setPlugin($this);
        $widget->challengeData = $this->storage->currentMap;
        $widget->setPosition(self::$posX, self::$posY);
        $widget->setScale(self::$scale);
        $widget->show();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        Service::SendVotes($this->storage->currentMap, $this->newVotes);
    }

    /**
     * Processes a vote of a player.
     *
     * @param string $login
     * @param integer $vote
     */
    public function doVote($login, $vote)
    {
        // we don't need to insert the same vote twice
        if (isset($this->karma->votes[$login]) && $this->karma->votes[$login] == $vote) {
            return;
        }

        // update local map score
        $this->karma->votes[$login] = $vote;

        $this->newVotes[$login] = new Structures\Vote($login, $vote);
        $msg = eXpGetMessage('#rank#$iVote Registered!!');
        $this->eXpChatSendServerMessage($msg, $login);

        // redraw the widget for this player
        $widget = Widget::Create($login);
        $widget->setKarma($this->karma);
        $widget->setPlugin($this);
        $widget->challengeData = $this->storage->currentMap;
        $widget->setPosition(self::$posX, self::$posY);
        $widget->setScale(self::$scale);
        $widget->show();
    }

    /**
     * When this plugin is unloaded by ManiaLive.
     */
    public function eXpOnUnload()
    {
        // erase all widgets
        Widget::EraseAll();
    }
}
