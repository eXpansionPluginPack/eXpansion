<?php

namespace ManiaLivePlugins\eXpansion\MusicBox;

use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows\MusicBoxWindow;
use ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows\CurrentTrackWidget;

class MusicBox extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $config;
    private $songs = array();
    private $enabled = true;
    private $wishes = array();
    private $nextSong = null;

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->config = Config::getInstance();
        CurrentTrackWidget::$musicBoxPlugin = $this;
        $colors = \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance();
        $colors->registerCode("music", '$f0a');
        
        $command = $this->registerChatCommand("musicbox", "mbox", 0, true);
        $command = $this->registerChatCommand("musicbox", "mbox", 1, true);
        $command = $this->registerChatCommand("mbox", "mbox", 0, true);
        $command = $this->registerChatCommand("mbox", "mbox", 1, true);
    }

    function onUnload() {
        CurrentTrackWidget::EraseAll();
        $this->connection->setForcedMusic(false, "");
        parent::onUnload();
    }

    private function getMusicCsv() {
        $f = fopen($this->config->url . "/index.csv", "r");
        $array = array();
        $keys = fgetcsv($f, 0, ";");
        while (!feof($f)) {
            $array[] = array_combine($keys, array_map('trim', fgetcsv($f, 0, ";")));
        }
        fclose($f);
        return $array;
    }

    /*     * "
     * onReady()
     * Function called when ManiaLive is ready loading.
     *
     * @return void
     */

    function exp_onReady() {

        try {
            foreach ($this->getMusicCsv() as $music)
                $this->songs[] = Structures\Song::fromArray($music);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage('%server%MusicBox $fff»» %error%' . utf8_encode($e->getMessage()));
            echo $e->getMessage();
            $this->enabled = false;
        }
        
        foreach ($this->storage->players as $login => $player) {
            $this->showWidget($login);
        }
        foreach ($this->storage->spectators as $login => $player) {
            $this->showWidget($login);
        }
    }

    /**
     * onBeginChallenge()
     * Function called on begin of challenge.
     *
     * @param mixed $challenge
     * @param mixed $warmUp
     * @param mixed $matchContinuation
     * @return void
     */
    function onEndMatch($rankings, $winnerTeamOrMap) {
        try {
            $song = $this->songs[rand(0, sizeof($this->songs) - 1)];
            // check for same song, and randomize again
            if ($this->nextSong == $song) {
                $song = $this->songs[rand(0, sizeof($this->songs) - 1)];
            }

            $wish = false;

            if (sizeof($this->wishes) != 0) {
                $wish = array_shift($this->wishes);
                $song = $wish->song;
            }

            $this->nextSong = $song;
            $folder = urlencode($song->folder);
            $folder = str_replace("%2F", "/", $folder);
            $url = $this->config->url . $folder . "/" . rawurlencode($song->filename);

            $this->connection->setForcedMusic(true, $url);
            if ($wish) {
                $text = exp_getMessage('#variable# %1$s - %2$s $z$s#music# is been played next requested by %3$s #variable#');
                $this->exp_chatSendServerMessage($text, null, array($song->title, $song->artist, \ManiaLib\Utils\Formatting::stripCodes($wish->player->nickName, "wos")));
            } else {
                $text = exp_getMessage('#music# Next song: $z$s#variable# %1$s - %2$s ');
                $this->exp_chatSendServerMessage($text, null, array($song->title, $song->artist));
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . " \n" . $e->getLine();
        }
        foreach ($this->storage->players as $login => $player) {
            $this->showWidget($login);
        }
        foreach ($this->storage->spectators as $login => $player) {
            $this->showWidget($login);
        }
    }

    /**
     * showWidget()
     * Helper function, shows the widget.
     *
     * @param mixed $login
     * @param mixed $music
     * @return void
     */
    function showWidget($login) {
        $music = $this->connection->getForcedMusic();
        
        $outsong = new Structures\Song();
        if (!empty($music->url)) {
            foreach ($this->songs as $id => $song) {

                $folder = urlencode($song->folder);
                $folder = str_replace("%2F", "/", $folder);

                $url = $this->config->url . $folder . "/" . rawurlencode($song->filename);
                
                if ($url == $music->url) {
                    $outsong = $song;
                    break;
                }
            }
        }

        $window = CurrentTrackWidget::Create($login);
        $window->setSize(50, 10);
        $window->setSong($outsong);
        $pos = explode(",", $this->config->widgetPosition);

        $window->setPosition($pos[0], $pos[1]);
        $window->setValign("center");
        $window->show();
    }

    /**
     * onPlayerConnect()
     * Function called when a player connects.
     *
     * @param mixed $login
     * @param mixed $isSpectator
     * @return void
     */
    function onPlayerConnect($login, $isSpec) {
        $this->showWidget($login);
    }

    function onPlayerDisconnect($login, $reason = null) {
        CurrentTrackWidget::Erase($login);
    }

    /**
     * mbox()
     * Function providing the /mbox command.
     *
     * @param mixed $login
     * @param mixed $musicNumber
     * @return
     */
    function mbox($login, $number = null) {
        $player = $this->storage->getPlayerObject($login);
        if ($number == 'list' || $number == null) {  // parametres redirect
//$this->mjukeList($login);
            return;
        }
        if (!is_numeric($number)) {  // check for numeric value
// show error
            $text = '%server%MusicBox $fff»» %error%Invalid songnumber!';
            $this->exp_chatSendServerMessage($text, $login);
            return;
        }

        $number = (int) $number - 1; // do type conversion


        if (sizeof($this->songs) == 0) {
            $text = '%server%MusicBox $fff»» %error%No songs at music MusicBox!';
            $this->exp_chatSendServerMessage($text, $login);
            return;
        }

        if (!array_key_exists($number, $this->songs)) {
            $text = '%server%MusicBox $fff»» %error%Number entered is not in music list';
            $this->exp_chatSendServerMessage($text, $player);
            return;
        }
        $song = $this->songs[$number];

        foreach ($this->wishes as $id => $wish) {
            if ($wish->player == $player) {
                unset($this->wishes[$id]);
                $this->wishes[] = new Structures\Wish($song, $player);
                $text = 'Dropped last entry and  #variable#' . $song->title . "by " . $song->artist . ' $z$s#music# is added to the MusicBox by #variable#' . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '.';
                $this->exp_chatSendServerMessage($text, null);
                return;
            }
        }
        $this->wishes[] = new Structures\Wish($song, $player);
        $text = '#variable#' . $song->title . "by " . $song->artist . ' $z$s#music# is added to the MusicBox by #variable#' . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '.';
        $this->exp_chatSendServerMessage($text, null);
    }

    /**
     * mlist()
     * Function providing the /mlist command.
     * "
     * @param mixed $fromLogin
     * @param mixed $parameter
     * @return
     */
    function mlist($fromLogin, $parameter = NULL) {
        if ($parameter == "help") {
            $this->showHelp($fromLogin, $this->helpMlist);
            return;
        }

        $musiclist = $this->musicBox;

        if (count($musiclist) == 0) {
            $infoWindow = SimpleWindow::Create($fromLogin);
            $infoWindow->setTitle("Notice");
            $infoWindow->setText("The server doesn't have any music in the MusicBox. Ask kindly the server administrator to add some.");
            $infoWindow->setSize(40, 40);
            $infoWindow->centerOnScreen();
            $infoWindow->show();
            return;
        }

        if (!$this->enabled) {
            $infoWindow = SimpleWindow::Create($fromLogin);
            $infoWindow->setTitle("Notice");
            $infoWindow->setText("The music plugin is disabled.");
            $infoWindow->setSize(40, 40);
            $infoWindow->centerOnScreen();
            $infoWindow->show();
            return;
        }

        $window = MusicBoxWindow::Create($fromLogin);
        $window->setSize(210, 100);
        $window->clearAll();
// prepare cols ...
        $window->addColumn('Id', 0.1);
        $window->addColumn('Song', 0.6);
        $window->addColumn('Genre', 0.3);

// refresh records for this window ...
        $window->clearItems();
        $id = 1;
        $entry = NULL;
        foreach ($musiclist as $data) {
            if (empty($parameter)) {
                $entry = array
                    (
                    'Id' => array($id, NULL, false),
                    'Song' => array($data[1] . " - " . $data[0], $id, true),
                    'Genre' => array($data[2], NULL, false)
                );
            } else {
                $pros = 0;

                $awords = explode(" ", $data[0]);
                $swords = explode(" ", $data[1]);
                $gwords = explode(" ", $data[2]);

                $search = array_merge($awords, $swords, $gwords);

                foreach ($search as $word) {
                    similar_text($word, $parameter, $pros);
                    if ($pros >= 60) {
                        $entry = array
                            (
                            'Id' => array($id, NULL, false),
                            'Song' => array($data[1] . " - " . $data[0], $id, true),
                            'Genre' => array($data[3], NULL, false)
                        );
                        break;
                    }
                }
            }
            $id++;
            if ($entry !== NULL)
                $window->addAdminItem($entry, array($this, 'onClick'));

            $entry = NULL;
        }
// display or update window ...
        $window->centerOnScreen();
        $window->show();
    }

}

?>