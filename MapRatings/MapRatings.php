<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\RatingsWidget;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\EndMapRatings;
use ManiaLivePlugins\eXpansion\MapRatings\Structures\PlayerVote;

class MapRatings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $rating = 0;
    private $ratingTotal = 0;

    /** @var Config */
    private $config;
    private $msg_rating;
    private $msg_noRating;
    private $displayWidget = true;
    private $pendingRatings = array();
    private $oldRatings = array();
    private $previousUid = null;

    function exp_onInit() {
        if ($this->isPluginLoaded('ManiaLivePlugins\oliverde8\HudMenu\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
        EndMapRatings::$parentPlugin = $this;
        $this->config = Config::getInstance();
    }

    function exp_onLoad() {
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
        $cmd = $this->registerChatCommand("rating", "chatRating", 0, true);
        $cmd->help = '/rating Map Rating Approval';

        $this->setPublicMethod("getRatings");
    }

    public function exp_onReady() {
        $this->reload();
        if ($this->isPluginLoaded("eXpansion\TMKarma")) {
            $this->displayWidget = false;
        }

        foreach ($this->storage->players as $login => $player)
            $this->onPlayerConnect($login, false);
        foreach ($this->storage->spectators as $login => $player)
            $this->onPlayerConnect($login, true);

        $this->previousUid = $this->storage->currentMap->uId;
        // $this->onEndMatch("", "");
    }

    public function onOliverde8HudMenuReady($menu) {
        $button["style"] = "UIConstructionSimple_Buttons";
        $button["substyle"] = "Drive";

        $parent = $menu->findButton(array('menu', 'Maps'));
        if (!$parent) {
            $parent = $menu->addButton('menu', "Maps", $button);
        }

        $button["style"] = "BgRaceScore2";
        $button["substyle"] = "Fame";
        $parent = $menu->addButton($parent, "Rate Map", $button);

        $button["plugin"] = $this;
        $button["function"] = 'hudRateMap';

        $button["params"] = "---";
        $menu->addButton($parent, "Terrible (---)", $button);

        $button["params"] = "--";
        $menu->addButton($parent, "Bad (--)", $button);

        $button["params"] = "+";
        $menu->addButton($parent, "Average (+/-)", $button);

        $button["params"] = "++";
        $menu->addButton($parent, "Good (++)", $button);

        $button["params"] = "+++";
        $menu->addButton($parent, "Fantastic (+++)", $button);
    }

    public function hudRateMap($login, $param) {
        $this->onPlayerChat(1, $login, $param, false);
    }

    public function getRatings() {
        $ratings = $this->db->query("SELECT uid, avg(rating) AS rating, COUNT(rating) AS ratingTotal FROM exp_ratings GROUP BY uid;")->fetchArrayOfObject();
        $out = array();
        foreach ($ratings as $rating) {
            $out[$rating->uid] = new Structures\Rating($rating->rating, $rating->ratingTotal);
        }
        return $out;
    }

    /**
     * 
     * @param null|string|\Maniaplanet\DedicatedServer\Structures\Map $uId
     * @return PlayerVote[]
     */
    public function getVotesForMap($uId = null) {
        if ($uId == null)
            $uId = $this->storage->currentMap->uId;

        if ($uId instanceof \Maniaplanet\DedicatedServer\Structures\Map)
            $uId = $uid->uId;

        $ratings = $this->db->query("SELECT login, rating FROM exp_ratings WHERE `uid` = " . $this->db->quote($uId) . ";")->fetchArrayOfAssoc();

        $out = array();
        foreach ($ratings as $data) {
            $vote = PlayerVote::fromArray($data);
            $out[$vote->login] = $vote;
        }
        return $out;
    }

    public function reload() {
        $database = $this->db->query("SELECT avg(rating) AS rating, COUNT(rating) AS ratingTotal"
                        . " FROM exp_ratings"
                        . " WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . ";")->fetchObject();
        $this->rating = 0;
        $this->ratingTotal = 0;
        if ($database !== false) {
            $this->rating = $database->rating;
            $this->ratingTotal = $database->ratingTotal;
            $this->oldRatings = $this->getVotesForMap($this->storage->currentMap->uId);
        }
    }

    public function saveRatings($uid) {
        try {
            
            if(empty($this->pendingRatings))
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
                $loginList .= $this->db->quote($login);
            }

            $this->db->query(
                    "DELETE FROM exp_ratings "
                    . " WHERE `uid`= " . $this->db->quote($uid) . " "
                    . " AND `login` IN (" . $loginList . ")");

            $this->db->query($sqlInsert);
            $this->pendingRatings = array();
            
        } catch (\Exception $e) {
            $this->console("Error in MapRating: " . $e->getMessage());
        }
    }

    public function saveRating($login, $rating) {
        EndMapRatings::Erase($login);

        $oldRating = 0;
        $sum = $this->rating * $this->ratingTotal;

        if (isset($this->pendingRatings[$login])) {
            $oldRating = $this->pendingRatings[$login];
        } else if (isset($this->oldRatings[$login])) {
            $oldRating = $this->oldRatings[$login]->vote;
        } else {
            $this->ratingTotal++;
        }

        $this->rating = ($sum - $oldRating + $rating) / $this->ratingTotal;
        $this->pendingRatings[$login] = $rating;

        if ($this->displayWidget) {
            $this->displayWidget(null);
            /* reaby disabled, no need to show vote registered text :/
              $msg = exp_getMessage('#rank#$iVote Registered!!');
              $this->exp_chatSendServerMessage($msg, $login); */
            $this->sendRatingMsg($login, $rating);
        }

    }

    function sendRatingMsg($login, $playerRating) {
        if ($login != null) {
            if ($this->ratingTotal == 0) {
                $this->exp_chatSendServerMessage($this->msg_noRating, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
                return;
            }
            if ($playerRating === null) {
                $query = $this->db->query("SELECT rating AS playerRating FROM exp_ratings WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . " AND `login`=" . $this->db->quote($login) . ";")->fetchObject();
                if ($query === false) {
                    $playerRating = '-';
                } else {
                    $playerRating = $query->playerRating;
                }
            } 

            // $rating = (($this->rating - 1) / 4) * 100;
            $rating = ($this->rating / 5) * 100;
            $rating = round($rating) . "%";
            $this->exp_chatSendServerMessage($this->msg_rating, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'), $rating, $this->ratingTotal, $playerRating));
        }
    }

    function chatRate($login, $arg, $param = null) {
        if ($login != null) {
            switch ($arg) {
                case "+++":
                    $this->saveRating($login, 5);
                    break;
                case "++":
                    $this->saveRating($login, 4);
                    break;
                case "+-":
                    $this->saveRating($login, 3);
                    break;
                case "--":
                    $this->saveRating($login, 2);
                    break;
                case "---":
                    $this->saveRating($login, 1);
                    break;
                case "5":
                    $this->saveRating($login, 5);
                    break;
                case "4":
                    $this->saveRating($login, 4);
                    break;
                case "3":
                    $this->saveRating($login, 3);
                    break;
                case "2":
                    $this->saveRating($login, 2);
                    break;
                case "1":
                    $this->saveRating($login, 1);
                    break;
                case "help":
                default:
                    $msg = exp_getMessage('#rank# $iUsage /rate #, where number is 1-5..');
                    $this->exp_chatSendServerMessage($msg, $login);
                    break;
            }
        }
    }

    function chatRating($login = null) {
        if ($login != null) {
            $this->sendRatingMsg($login, null);
        }
    }

    function displayWidget($login = null) {
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

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        
    }
    
    function onBeginMatch() {
        if($this->previousUid != null)
            $this->saveRatings($this->previousUid);
        
        $this->previousUid = $this->storage->currentMap->uId;
        
        $this->reload();
        
        EndMapRatings::EraseAll();
        $this->displayWidget();
        //send msg
        if ($this->config->sendBeginMapNotices) {
            if ($this->ratingTotal == 0) {
                $this->exp_chatSendServerMessage($this->msg_noRating, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
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
    
    function onEndMatch($rankings, $winnerTeamOrMap) {
        if ($this->config->showPodiumWindow) {
            $ratings = $this->getVotesForMap(null);
            foreach ($this->storage->players as $login => $player) {
                if (!array_key_exists($login, $ratings) && !isset($this->pendingRatings[$login])) {
                    $widget = EndMapRatings::Create($login, true);
                    $widget->show();
                }
            }
        }
    }

    function onPlayerConnect($login, $isSpectator) {
        if (!$this->displayWidget)
            return;

        RatingsWidget::Erase($login);
        $info = RatingsWidget::Create($login);
        $info->setSize(34, 12);
        $info->setPosition(128, 76);
        $info->setStars($this->rating, $this->ratingTotal);
        $info->show();        
    }

    function onPlayerDisconnect($login, $reason = null) {
        RatingsWidget::Erase($login);
    }

    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
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

}

?>