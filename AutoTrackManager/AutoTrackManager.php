<?php

/**
 *
 * @author    Willem 'W1lla' van den Munckhof <w1llaopgezwolle@gmail.com>
 *
 * @copyright 2013
 *
 * ---------------------------------------------------------------------
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
 * ---------------------------------------------------------------------
 * You are allowed to change things of use this in other projects, as
 * long as you leave the information at the top (name, date, version,
 * website, package, author, copyright) and publish the code under
 * the GNU General Public License version 3.
 * ---------------------------------------------------------------------
 */

namespace ManiaLivePlugins\eXpansion\AutoTrackManager;

use ManiaLive\DedicatedApi\Xmlrpc\Exception;
use ManiaLive\Utilities\String;

class AutoTrackManager extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public static $defaultTracklist = 'tracklist.txt';
    public static $showname = 'AutoTrackManager loaded successfully! Type /atm';
    private $rating = 0;
    private $ratingTotal = 0;
    public static $MINVotes = "10"; // Must be greater then 0. Best is to have a functional of 10.
    public static $integervalue = "0.6";  /* Ratio in percents (0.6 mean 60 

      % good/totalvotes => 40% bad!) the tracks will be sort out if a track is lower than this value
     */

    /**
     * onInit()
     * Function called on starting the plugin.
     *
     * @return void
     */
    public function expOnInit()
    {
        $this->setPublicMethod('getVersion');
        $this->config = Config::getInstance();
    }

    /**
     * onLoad()
     * Function called on loading the plugin.
     *
     * @return void
     */
    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
        $this->enableStorageEvents();
        $this->enableDatabase();

        $help = "Shows atm functions.";
        $cmd = $this->registerChatCommand("atmhelp", "atmhelp", 0, true);
        $cmd->help = $help;
    }


    /**
     * onPlayerConnect()
     * Function called on connecting a player.
     *
     * @return void
     */
    public function onPlayerConnect($login, $isSpectator)
    {
        $source_player = $this->storage->getPlayerObject($login);
        $msg = '' . self::$showname . ' ' . $self::$version . '';
        $this->connection->chatSendServerMessage($msg, $login);
    }

    /**
     * onatmhelp()
     * Function called when player types his/her /atmhelp Shows info what it really does.
     *
     * @return void
     */
    public function atmhelp($login)
    {
        $msg = 'AutoTrackManager lets you remove / delete tracks from tracklist if track karma got lower than a given value!';
        $this->connection->chatSendServerMessage($msg, $login);
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->autotrackmanager();
    }

    /**
     * autotrackmanager()
     * Function used to run the whole code basicly.
     *
     * @return void
     */
    public function autotrackmanager()
    {
        $q = $this->db->execute(
            "SELECT avg(rating) AS rating, COUNT(rating) AS ratingTotal 
FROM exp_ratings WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . ";"
        )->fetchObject();
        $this->rating = 0;
        $this->ratingTotal = 0;
        $votecount = 0;
        if ($q !== false) {
            $this->rating = $q->rating;
            $this->ratingTotal = $q->ratingTotal;
        }
        $rating = ($this->rating / 5) * 100;
        $rating = round($rating) . "%";
        $votecount = $this->rating + $this->ratingTotal;

        foreach ($this->storage->players as $player) {
            $login = $player->login;
            $this->connection->chatSendServerMessage(
                'Current Track Ratio is ' . (($votecount > 0) ? (round($rating / $votecount, 2)) : ('n/a'))
                . ' % AutoTrackManager will remove it if it has a percent difference of: '
                . ($this->config->integervalue) . ' ',
                $login
            );
        }

        foreach ($this->storage->spectators as $player) {
            $login = $player->login;
            $this->connection->chatSendServerMessage(
                'Current Track Ratio is ' . (($votecount > 0) ? (round($rating / $votecount, 2)) : ('n/a'))
                . ' % AutoTrackManager will remove it if it has a percent difference of: '
                . ($this->config->integervalue) . ' ',
                $login
            );
        }
        //Showing track karma status ATM Debug However should be enabled for admins to really see if its true or not.
        $this->console('[' . date('H:i:s') . '] [eXpansion] [ATM] Karma: ' . $rating . '');
        $this->console(
            'ATM Debug: RatingTotal: ' . $rating . ', Players Voted Total: ' . $this->ratingTotal
            . ', ratio: ' . (($votecount > 0) ? (round($rating / $votecount, 2)) : ('n/a'))
        );
        if ($votecount >= $this->config->MINVotes && $rating / $votecount <= $this->config->integervalue) {
            $this->console('[' . date('H:i:s') . '] [eXpansion] [ATM] Karma: removal test');
            $this->console('ATM Debug: Track too bad: ' . $this->storage->currentMap->name);
            /**
             * Here begins the real function of removing the track from and server and database.
             * */
            $dataDir = $this->connection->gameDataDirectory();
            $dataDir = str_replace('\\', '/', $dataDir);
            $matchsettings = $dataDir . "Maps/MatchSettings/";
            $tracklist = self::$defaultTracklist;
            $challenge = $this->connection->getCurrentMapInfo();
            $dataDir = $this->connection->gameDataDirectory();
            $dataDir = str_replace('\\', '/', $dataDir);
            $file = $challenge->fileName;
            $challengeFile = $dataDir . "/Maps/" . $file;
            try {
                $this->connection->removeMap($challengeFile);
            } catch (Exception $e) {
                $this->console("Error:\n" . $e->getMessage());
            }
            $this->connection->chatSendServerMessage('AutoTrackManager removed this track from playlist.');
            $this->console('[' . date('H:i:s') . '] [eXpansion] [ATM] Removed current track from the tracklist.');
            $this->connection->saveMatchSettings($tracklist);
            $file = fopen('ATMLog.txt', 'w');
            fwrite(
                $file,
                '[' . date('H:i:s') . '] [eXpansion] [ATM] Removed ' . $this->storage->currentMap->name
                . ' (UId ' . $this->storage->currentMap->uId . ') from the tracklist.\n'
            );
            fclose($file);
            $this->console(
                '[' . date('H:i:s') . '] [eXpansion] [ATM] Removing all data from database from '
                . $this->storage->currentMap->name . ''
            );
            $q = "DELETE FROM exp_maps WHERE challenge_uid = "
                . $this->db->quote($this->storage->currentMap->uId) . ";";
            $query = $this->db->execute($q);
            $q = "DELETE FROM exp_ranks WHERE rank_challengeuid = "
                . $this->db->quote($this->storage->currentMap->uId) . ";";
            $query = $this->db->execute($q);
            $q = "DELETE FROM karma WHERE karma_trackuid = " . $this->db->quote($this->storage->currentMap->uId) . ";";
            $query = $this->db->execute($q);
        }
    }
}
