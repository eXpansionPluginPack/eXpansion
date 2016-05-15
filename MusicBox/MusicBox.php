<?php

namespace ManiaLivePlugins\eXpansion\MusicBox;

use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows\CurrentTrackWidget;
use ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows\MusicListWindow;

class MusicBox extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    private $config;
    private $songs = array();
    private $enabled = true;
    private $wishes = array();
    private $nextSong = null;
    private $music = null;

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
        $this->config = Config::getInstance();
        CurrentTrackWidget::$musicBoxPlugin = $this;
        Gui\Windows\MusicListWindow::$musicPlugin = $this;

        $command = $this->registerChatCommand("music", "mbox", 0, true);
        $command = $this->registerChatCommand("music", "mbox", 1, true);
        $command = $this->registerChatCommand("mlist", "mbox", 0, true); // xaseco
        $command = $this->registerChatCommand("mlist", "mbox", 1, true); // xaseco
    }

    function eXpOnUnload()
    {
        CurrentTrackWidget::EraseAll();
        MusicListWindow::EraseAll();

        CurrentTrackWidget::$musicBoxPlugin = null;
        Gui\Windows\MusicListWindow::$musicPlugin = null;

        $this->connection->setForcedMusic(false, "");
    }

    function download($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MusicBox [getter] ver 0.1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);

        if ($data === false) {
            Helper::log("[MusicBox]Server is down");

            return false;
        }

        if ($status["http_code"] !== 200) {
            if ($status["http_code"] == 301) {
                Helper::log("[MusicBox]Link has moved");

                return false;
            }

            Helper::log("[MusicBox]Http status : " . $status["http_code"]);

            return false;
        }

        return $data;
    }

    function getMusicCsv()
    {


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
                $keys = array_map(function ($input) {
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

    function eXpOnReady()
    {

        try {
            foreach ($this->getMusicCsv() as $music)
                $this->songs[] = Structures\Song::fromArray($music);
        } catch (\Exception $e) {
            $this->eXpChatSendServerMessage('MusicBox $fff»» #error#' . utf8_encode($e->getMessage()));
            $this->enabled = false;
        }

        $this->music = $this->connection->getForcedMusic();
        $this->showWidget();
    }

    public function onBeginMatch()
    {
        $this->music = $this->connection->getForcedMusic();
        $this->showWidget();
    }

    function onEndMatch($rankings, $winnerTeamOrMap)
    {
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
                $text = eXpGetMessage('#variable# %1$s#music# by#variable#  %2$s #music# is been played next requested by #variable# %3$s ');
                $this->eXpChatSendServerMessage($text, null, array($song->title, $song->artist, \ManiaLib\Utils\Formatting::stripCodes($wish->player->nickName, "wos")));
            } else {
                $text = eXpGetMessage('#music#Next song: $z$s#variable# %1$s #music#by#variable# %2$s');
                $this->eXpChatSendServerMessage($text, null, array($song->title, $song->artist));
            }
        } catch (\Exception $e) {
            Helper::log("[MusicBox]On EndMatch Error : " . $e->getMessage());
        }
    }

    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * showWidget()
     * Helper function, shows the widget.
     *
     * @param mixed $login
     * @param mixed $music
     *
     * @return void
     */
    function showWidget()
    {
        if (!$this->enabled)
            return;

        $music = $this->music;
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

        $window = CurrentTrackWidget::Create(null);
        $window->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
        $window->setVisibleLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
        $window->setPosition(0, 80);
        /*	if ($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT) {
                $window->setPosition(0, 50);
            } */
        $window->setSize(100, 10);
        $window->setSong($outsong);
        //$window->setValign("center");
        $window->show();
    }

    /**
     * mbox()
     * Function providing the /mbox command.
     *
     * @param mixed $login
     * @param mixed $musicNumber
     *
     * @return
     */
    function mbox($login, $number = null)
    {
        if (!$this->enabled)
            return;

        $player = $this->storage->getPlayerObject($login);
        if ($number == 'list' || $number == null) { // parametres redirect
            $this->musicList($login);

            return;
        }
        if (!is_numeric($number)) { // check for numeric value
// show error
            $text = '#music#MusicBox $fff»» #error#Invalid song number!';
            $this->eXpChatSendServerMessage($text, $login);

            return;
        }

        $number = (int)$number - 1; // do type conversion


        if (sizeof($this->songs) == 0) {
            $text = '#music#MusicBox $fff»» #error#No songs at music MusicBox!';
            $this->eXpChatSendServerMessage($text, $login);

            return;
        }

        if (!array_key_exists($number, $this->songs)) {
            $text = '#music#MusicBox $fff»» #error#Number entered is not in music list';
            $this->eXpChatSendServerMessage($text, $player);

            return;
        }
        $song = $this->songs[$number];

        foreach ($this->wishes as $id => $wish) {
            if ($wish->player == $player) {
                unset($this->wishes[$id]);
                $this->wishes[] = new Structures\Wish($song, $player);
                $text = 'Dropped last entry and  #variable#' . $song->title . "#mucic# by #variable#" . $song->artist . ' $z$s#music# is added to the MusicBox by #variable#' . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '.';
                $this->eXpChatSendServerMessage($text, null);

                return;
            }
        }
        $this->wishes[] = new Structures\Wish($song, $player);
        $text = '#variable#' . $song->title . " #music# by #variable#" . $song->artist . '#music# is added to the MusicBox by #variable#' . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '.';
        $this->eXpChatSendServerMessage($text, null);
    }

    function musicList($login)
    {
        try {
            $info = Gui\Windows\MusicListWindow::Create($login);
            $info->setSize(180, 90);
            $info->centerOnScreen();
            $info->show();
        } catch (\Exception $e) {
            Helper::log("[MusicBox]On EndMatch Error : " . $e->getMessage());
        }
    }

}

?>