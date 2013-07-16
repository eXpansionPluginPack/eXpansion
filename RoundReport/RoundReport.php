<?php
/**
 * RoundReport
 * Original Code: Xymph. Just perfected it to report Nobody Finished... and nice GUI...
 * @name Rounds
 * @data-made 05-02-2013
 * @date-finished 23-04-2013
 * @version 1.0
 * @package expansion
 *
 * @author Willem van den Munckhof
 * @copyright � 2013
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
 * You are allowed to change things or use this in other projects, as
 * long as you leave the information at the top (name, date, version,
 * website, package, author, copyright) and publish the code under
 * the GNU General Public License version 3.
 * ---------------------------------------------------------------------
 */

namespace ManiaLivePlugins\eXpansion\RoundReport;

use ManiaLive\Data\Storage;

class RoundReport extends \ManiaLive\PluginHandler\Plugin {

    private $rounds_count;

    private $round_times;

    private $round_pbs;

    private $mapsDone = 0;
    private $mapsscore = 0;
    private $mapsscore1 = 0;

    /*
    Used for the WINDOWS....
    */

    private $msgbuf = array();
    private $msglen = 21;
    private $linlen = 800;
    private $winlen = 1;

    /**
     * onInit()
     * Function called on initialisation of ManiaLive.
     *
     * @return void
     */
    function onInit() {
        $this->setVersion("0.1");
    }

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */

    function onLoad() {
        $this->enableDedicatedEvents();

    $this->registerChatCommand("end", "chatEnd", 0, true);
    }

     public function onReady() {
     $this->reset_rounds();
     }


    public function chatEnd($login) {
     if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "server_admin")) {
            $this->connection->chatSendServerMessage('No Permission to fiddle with MapsScores!',$login);
            return;
        }
        $admin = Storage::GetInstance()->getPlayerObject($login);
        $this->mapsscore = 0; //Mapscore team Red
        $this->mapsscore1 = 0; //Mapscore team
        $this->connection->chatSendServerMessage('$ff0>> $f80Admin Set Mapscores back to 0!!!!', $login);
    }

    function onBeginMap($map, $warmUp, $matchContinuation) {
    $this->reset_rounds();
    }

    function onBeginRound() {
        $gameMode = $this->connection->getGameMode();
        //var_dump($gameMode);
    if($gameMode == 3){
    // rankings and scores.
    $Page = '<manialinks>';
    $Page .= '<manialink id="121212121212123">';
    $Rankings = $this->connection->getCurrentRanking(300, 0);
    $GetTeamPointsLimit = $this->connection->GetTeamPointsLimit();
    //var_dump($GetTeamPointsLimit['CurrentValue']);

    //var_dump($Rankings);
    if ($Rankings[0]->score == $GetTeamPointsLimit['CurrentValue']){
    $this->mapsscore++;
    }
    if ($Rankings[1]->score == $GetTeamPointsLimit['CurrentValue']){
    $this->mapsscore1++;
    }
    $Page .= '<frame posn="0 0 0" id="FrameShow">';
    $Page .= '<quad posn="-26 43 -5" sizen="60 15" style="UiSMSpectatorScoreBig" substyle="TableBgVert" />';
    $Page .= '<frame>';
    $Page .= '<quad posn="1 39 10" sizen="10 10" halign="left" style="Emblems" substyle="#1" /> ';
    $Page .= '<label id="team1name" posn="17 37 10" sizen="40 4" halign="center" text="$o'.$Rankings[0]->nickName.'" />';
    $Page .= '<label posn="16 33 10 " halign="right" text="'.$Rankings[0]->score.'" style="TextValueSmallSm" />';
    $Page .= '</frame>';
    $Page .= '<label posn="1.5 32 10 " halign="right" text="'.$this->mapsscore1.':'.$this->mapsscore.'" style="TextValueSmallSm" />';
    $Page .= '<frame>';
    $Page .= '<quad posn="-1 39 10" sizen="10 10" halign="right" style="Emblems" substyle="#2" />  ';
    $Page .= '<label id="team2name" posn="-11 37 10" sizen="40 4" halign="right" text="$o'.$Rankings[1]->nickName.'" />';
    $Page .= '<label posn="-16 33 20 " halign="right" text="'.$Rankings[1]->score.'" style="TextValueSmallSm" />';
    $Page .= '</frame>';
    $Page .= '</frame>';
    $Page .= '<script><!--
    main () {
        declare FrameRules  <=> Page.GetFirstChild("FrameShow");
        declare ShowRules = True;

        while(True) {
            yield;

            if (ShowRules) {
                FrameRules.Show();
            } else {
                FrameRules.Hide();
            }

            foreach (Event in PendingEvents) {
                switch (Event.Type) {
                    case CMlEvent::Type::MouseClick :
                    {
                        if (Event.ControlId == "FrameRules") ShowRules = !ShowRules;
                    }

                    case CMlEvent::Type::KeyPress:
                    {
                        if (Event.CharPressed == "2424832") ShowRules = !ShowRules; // F1
                    }
                }
            }
        }
    }
--></script>';
    $Page .= '</manialink>';
    $Page .= '</manialinks>';

    //var_dump($Page);
    foreach ($this->storage->players as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $Page, 0, true, true);
}
    foreach ($this->storage->spectators as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $Page, 0, true, true);
}
    }
}

    function onEndMatch($rankings, $winnerTeamOrMap) {
    //var_dump($rankings);
    //var_dump($winnerTeamOrMap);
    //$this->report_match();
    }

    function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
    if($restartMap == true){
    $this->reset_rounds();
    }
    $this->reset_rounds();
    }

    function onEndRound() {
    $this->report_round();
    }

    function reset_rounds() {

    // reset counter, times & PBs
    $this->rounds_count = 0;
    $this->round_times = array();
    $this->round_pbs = array();
    $this->mapsDone = 0;
    }  // reset_rounds

    /*function report_match(){
    $this->mapsDone == count(\ManiaLive\Data\Storage::getInstance()->maps);
    $this->mapsDone++;
    $message = '$0f3Maps done: [$fff '.$this->mapsDone.' $39f]';
    $this->connection->chatSendServerMessage($message);
    //var_dump($this->mapsDone);
    }*/

    function report_round() {
    // if nobody finished make a report
    $config = Config::getInstance();
    if (empty($this->round_times)){
    $message = '$O$39fR:$fff'.$this->rounds_count.' $39f Nobody Finished!!!';
    $this->rounds_count++;
    if ($config->message == true){
    $this->connection->chatSendServerMessage($message);
    }
    if ($config->window == true){
    $this->send_window_message($message);
    }
    }
    // if someone finished (in Rounds/Team/Cup mode), then report this round
    if (!empty($this->round_times)) {
        // sort by times, PBs & PIDs
        $this->round_scores = array();

        ksort($this->round_times);
        foreach($this->round_times as &$item){
            // sort only times which were driven more than once
            if (count($item) > 1) {
                $scores = array();
                $pbs = array();
                $pids = array();
                foreach ($item as $key => &$row) {
                    $scores[$key] = $row['score'];
                    $pbs[$key] = $this->round_pbs[$row['login']];
                    $pids[$key] = $row['playerid'];
                }
                // sort order: SCORE, PB and PID, like the game does
                array_multisort($scores, SORT_NUMERIC, $pbs, SORT_NUMERIC, $pids, SORT_NUMERIC, $item);
            }
            // merge all score arrays
            $this->round_scores = array_merge($this->round_scores, $item);
        }
    $pos = 1;
        $message = '$O$39fR: $fff'.$this->rounds_count.'$39f ';
        $this->rounds_count++;
        // report all new records, first 'show_min_recs' w/ time, rest w/o
        foreach ($this->round_scores as $tm) {
            // check if player still online
            if ($player = $this->storage->getPlayerObject($tm['login']))
                $nick = $player->nickName;
            else  // fall back on login
                $nick = $tm['login'];
            $new = false;

            // to-do: go through each record on map....
            // will be added in a later stage
            $message .= '$0f3 '.$pos.' $39f.$fff '.$nick.' $39f[$fff '.\ManiaLive\Utilities\Time::fromTM($tm['score']).' $39f]';
            //var_dump($pos);
            $pos++;
            if ($tm['score'] <= 0){
            $message .=  '$0f3.$39f$fff'.$nick.'$39f[$fffDNF$39f]';
            }
        }
            if ($config->message == true){
    $this->connection->chatSendServerMessage($message);
    }
    if ($config->window == true){
    $this->send_window_message($message);
    }
        // reset times
        $this->round_times = array();
    }
    $gameMode = $this->connection->getGameMode();
    if($gameMode == 5){
    $Frame = '<manialinks>';
    $Frame .= '<manialink id="121212121212124">';
    $Rankings = $this->connection->getCurrentRanking(300, 0);
    $Limit = $this->connection->getCupPointsLimit();
    $Score = $Rankings[0]->score;
    $Frame .= '<frame posn="0 0 0" id="FrameShow">';
    $Frame .= '<quad posn="-26 43 -5" sizen="60 15" style="UiSMSpectatorScoreBig" substyle="TableBgVert" />';
    $Frame .= '<frame>';
        if ($Score == $Limit['CurrentValue']) {
            $Frame .= '<label id="team1name" posn="17 37 10" sizen="40 4" halign="center" text="$o$F00$oFinalist!" />';
        } else if ($Score > $Limit['CurrentValue']) {
            $Frame .= '<label id="team1name" posn="17 37 10" sizen="40 4" halign="center" text="$o$FF0Winner!" />';
        } else {
            $Frame .= '<label id="team1name" posn="17 37 10" sizen="40 4" halign="center" text="$o'.$Score.'" />';
        }
    $Frame .= '<label id="team2name" posn="-11 37 10" sizen="40 4" halign="right" text="$o'.$Rankings[0]->nickName.'" />';
    $Frame .= '</frame>';
    $Frame .= '</frame>';
    $Frame .= '<script><!--
    main () {
        declare FrameRules  <=> Page.GetFirstChild("FrameShow");
        declare ShowRules = True;

        while(True) {
            yield;

            if (ShowRules) {
                FrameRules.Show();
            } else {
                FrameRules.Hide();
            }

            foreach (Event in PendingEvents) {
                switch (Event.Type) {
                    case CMlEvent::Type::MouseClick :
                    {
                        if (Event.ControlId == "FrameRules") ShowRules = !ShowRules;
                    }

                    case CMlEvent::Type::KeyPress:
                    {
                        if (Event.CharPressed == "2424832") ShowRules = !ShowRules; // F1
                    }
                }
            }
        }
    }
--></script>';
    $Frame .= '</manialink>';
    $Frame .= '</manialinks>';
    //var_dump($Frame);
    foreach ($this->storage->players as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $Frame, 0, true, true);
        }
    foreach ($this->storage->spectators as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $Frame, 0, true, true);
        }
    }
    if($gameMode == 3){
    $Page = '<manialinks>';
    // rankings and scores.
    $Page .= '<manialink id="121212121212123">';
    $Rankings = $this->connection->getCurrentRanking(300, 0);
    $GetTeamPointsLimit = $this->connection->GetTeamPointsLimit();
    //var_dump($GetTeamPointsLimit['CurrentValue']);

    //var_dump($Rankings);
    if ($Rankings[0]->score == $GetTeamPointsLimit['CurrentValue']){
    $this->mapsscore++;
    }
    if ($Rankings[1]->score == $GetTeamPointsLimit['CurrentValue']){
    $this->mapsscore1++;
    }
    $Page .= '<frame posn="0 0 0" id="FrameShow">';
    //$Page .= '<quad posn="-50 35 0" sizen="20 10" halign="center" style="TitleLogos" substyle="Title"/>';
    $Page .= '<quad posn="-26 43 -5" sizen="60 15" style="UiSMSpectatorScoreBig" substyle="TableBgVert" />';
    $Page .= '<frame>';
    $Page .= '<quad posn="1 39 10" sizen="10 10" halign="left" style="Emblems" substyle="#1" /> ';
    $Page .= '<label id="team1name" posn="17 37 10" sizen="40 4" halign="center" text="$o'.$Rankings[0]->nickName.'" />';
    $Page .= '<label posn="16 33 10 " halign="right" text="'.$Rankings[0]->score.'" style="TextValueSmallSm" />';
    $Page .= '</frame>';
    $Page .= '<label posn="1.5 32 10 " halign="right" text="'.$this->mapsscore1.':'.$this->mapsscore.'" style="TextValueSmallSm" />';
    $Page .= '<frame>';
    $Page .= '<quad posn="-1 39 10" sizen="10 10" halign="right" style="Emblems" substyle="#2" />  ';
    $Page .= '<label id="team2name" posn="-11 37 10" sizen="40 4" halign="right" text="$o'.$Rankings[1]->nickName.'" />';
    $Page .= '<label posn="-16 33 20 " halign="right" text="'.$Rankings[1]->score.'" style="TextValueSmallSm" />';
    $Page .= '</frame>';
    $Page .= '</frame>';
$Page .= '<script><!--
    main () {
        declare FrameRules  <=> Page.GetFirstChild("FrameShow");
        declare ShowRules = True;

        while(True) {
            yield;

            if (ShowRules) {
                FrameRules.Show();
            } else {
                FrameRules.Hide();
            }

            foreach (Event in PendingEvents) {
                switch (Event.Type) {
                    case CMlEvent::Type::MouseClick :
                    {
                        if (Event.ControlId == "FrameRules") ShowRules = !ShowRules;
                    }

                    case CMlEvent::Type::KeyPress:
                    {
                        if (Event.CharPressed == "2424832") ShowRules = !ShowRules; // F1
                    }
                }
            }
        }
    }
--></script>';
    $Page .= '</manialink>';

    $Page .= '</manialinks>';
    //var_dump($Page);
    foreach ($this->storage->players as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $Page, 0, true, true);
}
    foreach ($this->storage->spectators as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $Page, 0, true, true);
}
    }

}  // report_round


function onPlayerFinish($playerUid, $login, $timeOrScore) {
    //var_dump($timeOrScore);
    $this->store_time($playerUid, $login, $timeOrScore);
}
// called @ onPlayerFinish
function store_time($playerUid, $login, $timeOrScore) {
//var_dump($timeOrScore);
    // if Rounds/Team/Cup mode & actual finish, then store time & PB
    $gameMode = $this->connection->getGameMode();
    //var_dump($gameMode);
    if (($gameMode == 3 ||
         $gameMode == 4 ||
         $gameMode == 5) &&  $timeOrScore > 0) {
        $this->round_times[ $timeOrScore][] = array(
            'playerid' => $playerUid,
            'login' => $login,
            'score' =>  $timeOrScore,
        );
        if (isset($this->round_pbs[$login])) {
            if ($this->round_pbs[$login] > $timeOrScore) {
                $this->round_pbs[$login] = $timeOrScore;
            }
        } else {
            $this->round_pbs[$login] =  $timeOrScore;
        }
    }
}  // store_time

function send_window_message($message) {

    // append message line(s) to history
    $message = explode(LF, $message);
    foreach ($message as $item) {
        // break up long (report) lines into chunks
        $multi = explode(LF, wordwrap('$z$s' . $item, $this->linlen, LF . '$z$s$n'));
        foreach ($multi as $line) {
            // drop oldest message line if buffer full
            if (count($this->msgbuf) >= $this->msglen) {
                array_shift($this->msgbuf);
            }
            $this->msgbuf[] = $line;
            $timeout = 6*1000;
        }
    }
    $this->lines = array_slice($this->msgbuf, -$this->winlen);
    $this->display_msgwindow($this->lines, $timeout);
}

/**
 * Displays the Message window
 *
 * $msgs   : lines to be displayed
 * $timeout: timeout for window in msec
 */
function display_msgwindow($msgs, $timeout) {

    $cnt = count($msgs);
    $xml = '<manialink id="7"><frame posn="-30 -30 0" scale="0.9">' . LF .
         '<quad sizen="93 ' . (1.5 + $cnt*2.5) . '" style="BgsPlayerCard" substyle="BgCardSystem" id="8" scale="0.9" />' . LF;
    $pos = -1;
    foreach ($msgs as $msg) {
        $xml .= '<label posn="1 ' . $pos . ' 1" sizen="91 1" style="TextRaceChat" text="' . $msg . '"/>' . LF .
        $pos -= 2.5;
    }
    $xml .= '</frame></manialink>';
foreach ($this->storage->players as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $xml, 0, true, true);
}
foreach ($this->storage->spectators as $login => $player) { // get players
    $this->connection->sendDisplayManialinkPage($player->login, $xml, 0, true, true);
}
}  // display_msgwindow



}
?>