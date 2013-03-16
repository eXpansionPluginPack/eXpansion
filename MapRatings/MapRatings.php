<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\RatingsWidget;

class MapRatings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $rating = 0;

    function exp_onInit() {
         if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
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
    
    public function hudRateMap($login, $param){
        $this->onPlayerChat(1, $login, $param, false);
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
    }

    function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        
    }

    function onPlayerConnect($login, $isSpectator) {
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

