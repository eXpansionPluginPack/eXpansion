<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\RatingsWidget;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\EndMapRatings;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Windows\MapRatingsManager;
use ManiaLivePlugins\eXpansion\MapRatings\Structures\PlayerVote;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;

class MapRatings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    private $rating = 0;

    private $ratingTotal = 0;

    /** @var Config */
    private $config;

    private $msg_rating;

    private $msg_noRating;

    private $displayWidget = true;

    private $pendingRatings = array();

    private $oldRatings = array();

    private $previousMap = null;

    function expOnInit()
    {
        EndMapRatings::$parentPlugin = $this;
        \ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\RatingsWidget::$parentPlugin = $this;
        $actionFinal = ActionHandler::getInstance()->createAction(array($this, "autoRemove"));
        Gui\Windows\MapRatingsManager::$removeId = \ManiaLivePlugins\eXpansion\Gui\Gui::createConfirm($actionFinal);
        $this->config = Config::getInstance();
    }

    function eXpOnLoad()
    {
        $this->enableDatabase();
        $this->enableDedicatedEvents();
        $this->msg_rating = exp_getMessage('#rating#Map Approval Rating: #variable#%2$s#rating# (#variable#%3$s #rating#votes).  Your Rating: #variable#%4$s#rating# / #variable#5');  // '%1$s' = Map Name, '%2$s' = Rating %, '%3$s' = # of Ratings, '%4$s' = Player's Rating);
        $this->msg_noRating = exp_getMessage('#rating# $iMap has not been rated yet!');
        if (!$this->db->tableExists("exp_ratings")) {
            $this->db->execute('CREATE TABLE IF NOT EXISTS `exp_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` text NOT NULL,
  `login` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;');
        }

        $cmd = $this->registerChatCommand("rate", "chatRate", 1, true);
        $cmd->help = '/rate +++, /rate ++, /rate +-, /rate --, /rate --- or /rate 5, /rate 4, /rate 3. /rate 2, /rate 1';
        $cmd = $this->registerChatCommand("rating", "chatRating", 1, true);
        $cmd->help = '/rating Map Rating Approval';

        $this->setPublicMethod("getRatings");
        $this->setPublicMethod("showRatingsManager");
    }

    public function eXpOnReady()
    {
        $this->reload();
        if ($this->isPluginLoaded("eXpansion\TMKarma")) {
            $this->displayWidget = false;
        }

        if ($this->displayWidget) {
            $info = RatingsWidget::Create(null);
            $info->setSize(34, 12);
            $info->setStars($this->rating, $this->ratingTotal);
            $info->show();
        }

        $this->previousMap = $this->storage->currentMap;

        //$this->registerChatCommand("test", "onEndMatch", 0, false);
        $this->affectAllRatings();
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified)
    {
        //$this->affectAllRatings();
    }

    public function getRatings()
    {
        $ratings = $this->db->execute("SELECT uid, avg(rating) AS rating, COUNT(rating) AS ratingTotal FROM exp_ratings GROUP BY uid;")->fetchArrayOfObject();
        $out = array();
        foreach ($ratings as $rating) {
            $out[$rating->uid] = new Structures\Rating($rating->rating, $rating->ratingTotal, $rating->uid);
        }

        return $out;
    }

    /**
     * Will affect the rating to all the maps in the storage
     */
    public function affectAllRatings()
    {
        $uids = "";
        $mapsByUid = array();
        foreach ($this->storage->maps as $map) {
            $uids .= $this->db->quote($map->uId) . ",";
            $mapsByUid[$map->uId] = $map;
        }
        $uids = trim($uids, ",");

        $ratings = $this->db->execute("SELECT uid, avg(rating) AS rating, COUNT(rating) AS ratingTotal "
            . " FROM exp_ratings "
            . " WHERE uid IN ($uids)"
            . " GROUP BY uid;")->fetchArrayOfObject();
        $out = array();
        foreach ($ratings as $rating) {
            $mapsByUid[$rating->uid]->mapRating = new Structures\Rating($rating->rating, $rating->ratingTotal, $rating->uid);
        }
    }

    /**
     *
     * @param null|string|\Maniaplanet\DedicatedServer\Structures\Map $uId
     *
     * @return PlayerVote[]
     */
    public function getVotesForMap($uId = null)
    {
        if ($uId == null) {
            $uId = $this->storage->currentMap->uId;
        } else if ($uId instanceof \Maniaplanet\DedicatedServer\Structures\Map) {
            $uId = $uId->uId;
        }

        $ratings = $this->db->execute("SELECT login, rating FROM exp_ratings WHERE `uid` = " . $this->db->quote($uId) . ";")->fetchArrayOfAssoc();

        $out = array();
        foreach ($ratings as $data) {
            $vote = PlayerVote::fromArray($data);
            $out[$vote->login] = $vote;
        }

        return $out;
    }

    public function reload()
    {
        $database = $this->db->execute("SELECT avg(rating) AS rating, COUNT(rating) AS ratingTotal"
            . " FROM exp_ratings"
            . " WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . ";")->fetchObject();
        $this->rating = 0;
        $this->ratingTotal = 0;
        if ($database) {
            $this->rating = $database->rating;
            $this->ratingTotal = $database->ratingTotal;
            $this->oldRatings = $this->getVotesForMap($this->storage->currentMap->uId);
            foreach ($this->storage->maps as $map) {
                if ($map->uId == $this->storage->currentMap->uId) {
                    $map->mapRating =
                        new Structures\Rating($database->rating, $database->ratingTotal, $this->storage->currentMap->uId);
                }
            }
        }
    }

    public function saveRatings($uid)
    {
        try {

            if (empty($this->pendingRatings))
                return;

            $sqlInsert = "INSERT INTO exp_ratings (`uid`, `login`, `rating`  ) VALUES ";
            $loginList = "";
            $i = 0;
            foreach ($this->pendingRatings as $login => $rating) {
                if ($i != 0) {
                    $sqlInsert .= ", ";
                }
                $i++;
                $sqlInsert .= "(" . $this->db->quote($uid) . "," . $this->db->quote($login) . "," . $this->db->quote($rating) . ")";
                $loginList .= $this->db->quote($login) . ",";
            }
            $loginList = rtrim($loginList, ",");

            $this->db->execute("DELETE FROM exp_ratings "
                . " WHERE `uid`= " . $this->db->quote($uid) . " "
                . " AND `login` IN (" . $loginList . ")");

            $this->db->execute($sqlInsert);
            $this->pendingRatings = array();
        } catch (\Exception $e) {
            $this->pendingRatings = array();
            $this->console("Error in MapRating: " . $e->getMessage());
        }
    }

    public function saveRating($login, $rating)
    {
        EndMapRatings::Erase($login);

        $oldRating = 0;
        $sum = $this->rating * $this->ratingTotal;

        if (isset($this->pendingRatings[$login])) {
            $oldRating = $this->pendingRatings[$login];
        } else if (isset($this->oldRatings[$login])) {
            $oldRating = $this->oldRatings[$login]->rating;
        } else {
            $this->ratingTotal++;
        }

        if ($this->ratingTotal == 0) {
            $this->rating = $rating;
        } else {
            $this->rating = ($sum - $oldRating + $rating) / $this->ratingTotal;
        }

        $this->pendingRatings[$login] = $rating;

        if ($this->displayWidget) {
            $this->displayWidget(null);
            /* reaby disabled, no need to show vote registered text :/
              $msg = exp_getMessage('#rank#$iVote Registered!!');
              $this->eXpChatSendServerMessage($msg, $login); */
            $this->sendRatingMsg($login, $rating);
        }
    }

    function sendRatingMsg($login, $playerRating)
    {
        if ($login != null) {
            if ($this->ratingTotal == 0) {
                $this->eXpChatSendServerMessage($this->msg_noRating, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));

                return;
            }
            if ($playerRating === null) {
                $query = $this->db->execute("SELECT rating AS playerRating FROM exp_ratings WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . " AND `login`=" . $this->db->quote($login) . ";")->fetchObject();
                if (!$query || !isset($query->playerRating)) {
                    $playerRating = '-';
                } else {
                    $playerRating = $query->playerRating;
                }
            }

            // $rating = (($this->rating - 1) / 4) * 100;
            $rating = ($this->rating / 5) * 100;
            $rating = round($rating) . "%";
            $this->eXpChatSendServerMessage($this->msg_rating, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'), $rating, $this->ratingTotal, $playerRating));
        }
    }

    function autoRemove($login)
    {
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::MAP_REMOVE_MAP)) {

            $filenames = array();
            foreach ($this->autoMapManager_getMaps() as $rating) {
                $filenames[] = $rating->map->fileName;
            }

            try {
                $this->connection->removeMapList($filenames);
                $this->eXpChatSendServerMessage(exp_getMessage("Maps with bad rating removed successfully."));
                Gui\Windows\MapRatingsManager::Erase($login);
            } catch (\Exception $e) {
                $this->eXpChatSendServerMessage("#error#Error: %s", $login, array($e->getMessage()));
            }
        }
    }

    /**
     *
     * @return \ManiaLivePlugins\eXpansion\MapRatings\MapRating[]
     */
    function autoMapManager_getMaps()
    {
        $items = array();
        foreach ($this->getRatings() as $uid => $rating) {
            $value = round(($rating->rating / 5) * 100);
            if ($rating->totalvotes >= $this->config->minVotes && $value <= $this->config->removeTresholdPercentage) {
                $map = \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::getObjbyPropValue($this->storage->maps, "uId", $uid);
                if ($map) {
                    $items[] = new Structures\MapRating($rating, $map);
                }
            }
        }

        return $items;
    }

    function showRatingsManager($login)
    {
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::MAP_REMOVE_MAP)) {
            $window = Gui\Windows\MapRatingsManager::Create($login);
            $window->setTitle(__("Ratings Manager", $login));
            $window->setSize(120, 90);
            $window->setRatings($this->autoMapManager_getMaps());
            $window->show();
        }
    }

    function chatRating($login = null)
    {
        if ($login !== null) {
            $this->sendRatingMsg($login, null);
        }
    }

    function displayWidget($login = null)
    {
        if (!$this->displayWidget)
            return;
        try {
            foreach (RatingsWidget::GetAll() as $window) {
                $window->setStars($this->rating, $this->ratingTotal);
                $window->redraw();
            }
        } catch (\Exception $e) {
            // do silent exception;
        }
    }

    function onBeginMap($var, $var2, $var3)
    {
        if ($this->previousMap != null) {
            $this->saveRatings($this->previousMap->uId);

            //Updating ratings in map object
            if (!isset($this->previousMap->mapRating))
                $this->previousMap->mapRating = new Structures\Rating($this->rating, $this->ratingTotal, $this->previousMap->uId);
            else {
                $this->previousMap->mapRating->rating = $this->rating;
                $this->previousMap->mapRating->totalvotes = $this->ratingTotal;
            }
            $this->previousMap = $this->storage->currentMap;
        }

        $this->reload();

        EndMapRatings::EraseAll();
        $this->displayWidget();
        //send msg
        if ($this->config->sendBeginMapNotices) {
            if ($this->ratingTotal == 0) {
                $this->eXpChatSendServerMessage($this->msg_noRating, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
            } else {
                foreach ($this->storage->players as $login => $player) {
                    $this->sendRatingMsg($login, null);
                }
                foreach ($this->storage->spectators as $login => $player) {
                    $this->sendRatingMsg($login, null);
                }
            }
        }
    }

    public function onBeginMatch()
    {
        $this->saveRatings($this->storage->currentMap->uId);
        $this->reload();

        EndMapRatings::EraseAll();
        $this->displayWidget();
    }

    function onEndMatch($rankings = "", $winnerTeamOrMap = "")
    {

        if ($this->config->showPodiumWindow) {
            $this->reload();
            $ratings = $this->getVotesForMap(null);

            $logins = array();
            foreach ($this->storage->players as $login => $player) {
                if (array_key_exists($login, $ratings) == false) {
                    $logins[$login] = $login;
                }
                if (array_key_exists($login, $this->pendingRatings))
                    unset($logins[$login]);
            }
            foreach ($this->storage->spectators as $login => $player) {
                if (array_key_exists($login, $ratings) == false) {
                    $logins[$login] = $login;
                }
                if (array_key_exists($login, $this->pendingRatings))
                    unset($logins[$login]);
            }


            if (sizeof($logins) > 0) {
                \ManiaLive\Gui\Group::Erase("mapratings");
                $group = \ManiaLive\Gui\Group::Create("mapratings", $logins);
                EndMapRatings::EraseAll();
                $widget = EndMapRatings::Create(null);
                $widget->setMap($this->storage->currentMap);
                $widget->show($group);
            }
        }
    }

    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {
        if ($playerUid == 0)
            return;
        if ($text == "0/5")
            $this->saveRating($login, 0);
        if ($text == "1/5")
            $this->saveRating($login, 1);
        if ($text == "2/5")
            $this->saveRating($login, 2);
        if ($text == "3/5")
            $this->saveRating($login, 3);
        if ($text == "4/5")
            $this->saveRating($login, 4);
        if ($text == "5/5")
            $this->saveRating($login, 5);

        if ($text == "---")
            $this->saveRating($login, 0);
        if ($text == "--")
            $this->saveRating($login, 1);
        if ($text == "-")
            $this->saveRating($login, 2);
        if ($text == "+")
            $this->saveRating($login, 3);
        if ($text == "++")
            $this->saveRating($login, 4);
        if ($text == "+++")
            $this->saveRating($login, 5);
    }

    function eXpOnUnload()
    {
        EndMapRatings::EraseAll();
        RatingsWidget::EraseAll();
        MapRatingsManager::EraseAll();
    }

}

?>