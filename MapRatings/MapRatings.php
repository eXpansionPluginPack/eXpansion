<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\RatingsWidget;

class MapRatings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $rating = 0;
    private $ratingTotal = 0;
    private $config;
    private $msg_rating;
    private $msg_noRating;
    private $displayWidget = true;

    function exp_onInit() {
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
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
        $this->onPlayerConnect(null, true);
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

    public function reload() {
        $database = $this->db->query("SELECT avg(rating) AS rating, COUNT(rating) AS ratingTotal FROM exp_ratings WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . ";")->fetchObject();
        $this->rating = 0;
        $this->ratingTotal = 0;
        if ($database !== false) {
            $this->rating = $database->rating;
            $this->ratingTotal = $database->ratingTotal;
        }
    }

    public function saveRating($login, $rating) {
        $uid = $this->db->quote($this->storage->currentMap->uId);
        try {
            $test = $this->db->query("SELECT * FROM exp_ratings WHERE `uid`= " . $uid . "  AND `login` = " . $this->db->quote($login) . " LIMIT 1;")->fetchObject();

            if ($test === false) {
                $query = "INSERT INTO exp_ratings (`uid`, `login`, `rating`  ) VALUES (" . $uid . "," . $this->db->quote($login) . "," . $this->db->quote($rating) . ") ON DUPLICATE KEY UPDATE `rating`=" . $this->db->quote($rating) . ";";
                $this->db->execute($query);
            } else {
                $query = "UPDATE exp_ratings set `rating` = $rating WHERE `login` = " . $this->db->quote($login) . " AND `uid` = $uid;";
                $this->db->execute($query);
            }
            $this->reload();
            $this->displayWidget(null);
            
            if ($this->displayWidget) {
                $msg = exp_getMessage('#rank#$iVote Registered!!');
                $this->exp_chatSendServerMessage($msg, $login);
                $this->sendRating($login, $rating);
            }
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error in MapRating: " . $e->getMessage());
        }
    }

    function sendRating($login, $playerRating) {
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
            $rating = (($this->rating - 1) / 4) * 100;
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
            $this->sendRating($login, null);
        }
    }

    function displayWidget($login = null) {
        if (!$this->displayWidget)
            return;
        try {
            foreach (RatingsWidget::GetAll() as $window) {
                $window->setStars($this->rating);
                $window->redraw();
            }
        } catch (\Exception $e) {
            // do silent exception;
        }
    }

    function onBeginMap($map, $warmUp, $matchContinuation) {
        $this->reload();
        $this->displayWidget();
        //send msg
        if ($this->config->sendBeginMapNotices) {
            if ($this->ratingTotal == 0) {
                $this->exp_chatSendServerMessage($this->msg_noRating, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
            } else {
                foreach ($this->storage->players as $login => $player) {
                    $this->sendRating($login, null);
                }
                foreach ($this->storage->spectators as $login => $player) {
                    $this->sendRating($login, null);
                }
            }
        }
    }

    function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        
    }

    function onPlayerConnect($login, $isSpectator) {
        if (!$this->displayWidget)
            return;

        if ($login == null) {
            RatingsWidget::EraseAll();
            $info = RatingsWidget::Create();
        } else {
            RatingsWidget::Erase($login);
            $info = RatingsWidget::Create($login);
        }
        $info->setSize(30, 6);
        $info->setPosition(158, 81);
        $info->setStars($this->rating);
        $info->show();
        $this->sendRating($login, null);
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