<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\RatingsWidget;

class MapRatings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $rating = 0;

    function exp_onInit() {
        
    }

    function exp_onLoad() {
        $this->enableDatabase();
        $this->enableDedicatedEvents();

        if (!$this->db->tableExists("exp_ratings")) {
            $this->db->execute('CREATE TABLE IF NOT EXISTS `exp_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` text NOT NULL,
  `login` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;');
        }
    }

    public function exp_onReady() {
        $this->reload();
        $this->displayWidget(null);
    }

    public function reload() {
        $database = $this->db->query("SELECT avg(rating) AS rating FROM exp_ratings WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . ";")->fetchObject();
        $this->rating = 0;
        if ($database !== false) {
            $this->rating = $database->rating;
        }
    }

    public function saveRating($login, $rating) {

        $uid = $this->db->quote($this->storage->currentMap->uId);

        try {
            $test = $this->db->query("SELECT * FROM exp_ratings WHERE `uid`= " . $uid . "  AND `login` = " . $this->db->quote($login) . " LIMIT 1;")->fetchObject();


            if ($test === false) {
                $query = $query = "INSERT INTO exp_ratings (`uid`, `login`, `rating`  ) VALUES (" . $uid . "," . $this->db->quote($login) . "," . $this->db->quote($rating) . ") ON DUPLICATE KEY UPDATE `rating`=" . $this->db->quote($rating) . ";";
                $this->db->execute($query);
            } else {
                $query = "UPDATE exp_ratings set `rating` = $rating WHERE `login` = " . $this->db->quote($login) . " AND `uid` = $uid;";
                $this->db->execute($query);
            }
            $this->exp_chatSendServerMessage("You rated the map for " . $rating . "/5, vote registered.", $login);
            $this->reload();
            $this->displayWidget(null);
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error in MapRating: " . $e->getMessage());
        }
    }

    function displayWidget($login = null) {
        $info = null;
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
    }

    function onBeginMap($map, $warmUp, $matchContinuation) {
        $this->reload();
        $this->displayWidget(null);
    }

    function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        RatingsWidget::EraseAll();
    }

    function onPlayerConnect($login, $isSpectator) {
        $this->displayWidget($login);
    }

    function onPlayerDisconnect($login) {
        RatingsWidget::Erase($login);
    }

    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        if ($playerUid == 0)
            return;
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
            $this->saveRating($login, 1);
        if ($text == "--")
            $this->saveRating($login, 2);
        if ($text == "-" || $text == "+")
            $this->saveRating($login, 3);
        if ($text == "++")
            $this->saveRating($login, 4);
        if ($text == "+++")
            $this->saveRating($login, 5);
    }

}
?>

