<?php

namespace ManiaLivePlugins\eXpansion\MusicBox;

use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows\CurrentTrackWidget;
use ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows\MusicListWindow;

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
        Gui\Windows\MusicListWindow::$musicPlugin = $this;

        $command = $this->registerChatCommand("music", "mbox", 0, true);
        $command = $this->registerChatCommand("music", "mbox", 1, true);
        $command = $this->registerChatCommand("mlist", "mbox", 0, true); // xaseco
        $command = $this->registerChatCommand("mlist", "mbox", 1, true); // xaseco
    }

    function onUnload() {
        CurrentTrackWidget::EraseAll();
        $this->connection->setForcedMusic(false, "");
        parent::onUnload();
    }

    function download($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MusicBox [getter] ver 0.1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);

        if ($data === false) {
            echo "Server is down";
            return false;
        }

        if ($status["http_code"] !== 200) {
            if ($status["http_code"] == 301) {
                echo "Moved.";
                return false;
            }

            echo "Http statuscode: " . $status["http_code"];
            return false;
        }
        return $data;
    }

    function getMusicCsv() {


        $data = $this->download(rtrim($this->config->url, "/") . "/index.csv");
        if (!$data) {
            die("error");
        }
        $data = explode("\n", $data);

        $x = 0;
        $keys = array();
        $array = array();

        foreach ($data as $line) {
            $x++;
            if (empty($line))
                continue;
            if ($x == 1) {
                $keys = array_map(function($input) {
                    return ltrim($input, "\xEF\xBB\xBF");
                }, str_getcsv($line, ";"));
                continue;
            }
            $array[] = array_combine($keys, array_map('trim', str_getcsv($line, ";")));
        }
        return $array;
    }

    /*
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
            $this->connection->exp_chatSendServerMessage('MusicBox $fff»» #error#' . utf8_encode($e->getMessage()));
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
        if (!$this->enabled)
            return;
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

            $url = trim($this->config->url, "/") . $folder . rawurlencode($song->filename);

            $this->connection->setForcedMusic(true, $url);
            if ($wish) {
                $text = exp_getMessage('#variable# %1$s#music# by#variable#  %2$s #music# is been played next requested by #variable# %3$s ');
                $this->exp_chatSendServerMessage($text, null, array($song->title, $song->artist, \ManiaLib\Utils\Formatting::stripCodes($wish->player->nickName, "wos")));
            } else {
                $text = exp_getMessage('#music#Next song: $z$s#variable# %1$s #music#by#variable# %2$s');
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

    public function exp_unload() {
        parent::exp_unload();
        $this->connection->setForcedMusic(false, "");
    }

    public function getSongs() {
        return $this->songs;
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
        if (!$this->enabled)
            return;
        $music = $this->connection->getForcedMusic();

        $outsong = new Structures\Song();
        if (!empty($music->url)) {
            foreach ($this->songs as $id => $song) {

                $folder = urlencode($song->folder);
                $folder = str_replace("%2F", "/", $folder);

                $url = trim($this->config->url, "/") . $folder . rawurlencode($song->filename);

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
        if (!$this->enabled)
            return;
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
        if (!$this->enabled)
            return;

        $player = $this->storage->getPlayerObject($login);
        if ($number == 'list' || $number == null) {  // parametres redirect
            $this->musicList($login);
            return;
        }
        if (!is_numeric($number)) {  // check for numeric value
// show error
            $text = '#music#MusicBox $fff»» #error#Invalid song number!';
            $this->exp_chatSendServerMessage($text, $login);
            return;
        }

        $number = (int) $number - 1; // do type conversion


        if (sizeof($this->songs) == 0) {
            $text = '#music#MusicBox $fff»» #error#No songs at music MusicBox!';
            $this->exp_chatSendServerMessage($text, $login);
            return;
        }

        if (!array_key_exists($number, $this->songs)) {
            $text = '#music#MusicBox $fff»» #error#Number entered is not in music list';
            $this->exp_chatSendServerMessage($text, $player);
            return;
        }
        $song = $this->songs[$number];

        foreach ($this->wishes as $id => $wish) {
            if ($wish->player == $player) {
                unset($this->wishes[$id]);
                $this->wishes[] = new Structures\Wish($song, $player);
                $text = 'Dropped last entry and  #variable#' . $song->title . "#mucic# by #variable#" . $song->artist . ' $z$s#music# is added to the MusicBox by #variable#' . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '.';
                $this->exp_chatSendServerMessage($text, null);
                return;
            }
        }
        $this->wishes[] = new Structures\Wish($song, $player);
        $text = '#variable#' . $song->title . " #music# by #variable#" . $song->artist . '#music# is added to the MusicBox by #variable#' . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '.';
        $this->exp_chatSendServerMessage($text, null);
    }

    function musicList($login) {
        try {
            $info = Gui\Windows\MusicListWindow::Create($login);
            $info->setSize(180, 90);
            $info->centerOnScreen();
            $info->show();
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getFile() . ":" . $e->getLine();
        }
    }

}

?>