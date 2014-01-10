<?php
set_time_limit(0);
error_reporting(E_ALL ^ E_DEPRECATED);

// launch process
$Ximporter = new Ximporter();

class Ximporter {

    private $config;
    private $conn;

    function __construct() {
        $this->welcome();
        $this->readconfig();
        $this->connectdb();
        $this->dothejob();
        $this->disconnect();
        $this->theEnd();
    }

    function welcome() {
        $this->hr();
        $this->c("from Xaseco to eXpansion", true);
        $this->hr();
    }

    function readconfig() {
        if (!file_exists("xaseco_migration.ini"))
            die("Cannot locate main configuration file: xaseco_migration.ini.");
        $this->config = parse_ini_file("xaseco_migration.ini");
        if ($this->config === false) {
            die("# Fatal error reading configuration file. Check .ini syntax");
        }
    }

    function connectdb() {
        $this->c(" Connecting to database.... hold on...");
        $this->conn = mysql_connect($this->config['host'] . ':' . $this->config['port'], $this->config['login'], $this->config['password']);
        if (!$this->conn) {
            die('Could not connect: ' . mysql_error());
        }
        $this->c(' Connected successfully');
    }

    function disconnect() {
        mysql_close($this->conn);
        $this->hr();
        $this->c("* All done! * ", true);        
    }

    function dothejob() {
        $this->hr();
        $this->c("* By now you are aware that this is ONE TIME operation * ", true);
        $this->c("* Once completed, do not run again! * ", true);
        $this->c("* WARNING * ", true);
        $this->c("* Only way to rollback is to start new MLEPP database * ", true);
        $this->c("* Once started, do not interupt the process *", true);
        $this->hr();
        print "Type \"DO IT\" and press enter to continue: ";
        $input = fgets(STDIN);
        if (trim($input) != "DO IT")
            die();

        $this->hr();
        $this->c("DO NOT INTERRUPT THE PROCESS", true);
        $this->hr();



        $this->query("USE " . $this->config['xaseco_db'] . ";", $this->conn);
        $records = $this->query("Select
p.Login as record_playerlogin, c.Uid as record_mapuid,
r.score as record_score, r.Date as record_date,
r.Checkpoints as record_checkpoints,
r.score as record_avgscore
FROM maps c, players p, records r where r.Playerid = p.id and r.mapid = c.id;", $this->conn);
        while ($xaseco_recs[] = mysql_fetch_object($records));
        unset($records);
        $this->query("USE " . $this->config['exp_db'] . ";", $this->conn);

        $mplayers = $this->query("SELECT * FROM players p;", $this->conn);
        while ($exp_players[] = mysql_fetch_assoc($mplayers));


        $total = count($xaseco_recs);
        $x = 1;
        $y = 0;
        $this->c("* Migrating localrecords *", true);
        foreach ($xaseco_recs as $data) {
            if (empty($data))
                continue;
            $this->query("INSERT INTO localrecords (`record_challengeuid`, `record_playerlogin`, `record_score`, `record_nbFinish`, `record_avgScore`, `record_checkpoints`, `record_date`, `record_nbLaps`) VALUES ('" . mysql_escape_string($data->record_mapuid) . "','" . mysql_escape_string($data->record_playerlogin) . "','1', '" . mysql_escape_string($data->record_score) . "','" . mysql_escape_string($data->record_score) . "','" . mysql_escape_string($data->record_checkpoints) . "','" . mysql_escape_string($data->record_date) . "','1' );", $this->conn);

            // for pretty output to user :)
            $percentage = round(($x / $total) * 100, 0);
            if ($y == round($total / 20, 0)) {
                $this->c($percentage . "% done...");
                $y = 0;
            }
            $x++;
            $y++;
        }

        unset($xaseco_recs);

        $this->query("USE " . $this->config['xaseco_db'] . ";", $this->conn);
        $xplayers = $this->query("SELECT Login as player_login, NickName as player_nickname, UpdatedAt as player_updated, Wins as player_wins, TimePlayed as player_timeplayed FROM players p;", $this->conn);
        while ($xaseco_players[] = mysql_fetch_assoc($xplayers));
        unset($xplayer);
        $this->query("USE " . $this->config['exp_db'] . ";", $this->conn);

        $this->c("* Migrating Players *", true);
        $total = count($xaseco_players);
        $x = 1;
        $y = 0;
        // do the players sort
        foreach ($xaseco_players as $data) {
            if (empty($data))
                continue;
            $percentage = round(($x / $total) * 100, 0);
            if ($y == round($total / 20, 0)) {
                $this->c($percentage . "% done...");
                $y = 0;
            }
            foreach ($exp_players as $mdata) {
                if ($data['player_login'] != $mdata['player_login']) {
                    $nick = $data['player_nickname'];
                    $this->query("INSERT INTO players (`player_login`, `player_nickname`, `player_updated`, `player_wins`, `player_timeplayed`) VALUES ('" . mysql_escape_string($data['player_login']) . "','" . mysql_escape_string($nick) . "', '" . $data['player_updated'] . "','" . $data['player_wins'] . "','" . $data['player_timeplayed'] . "');", $this->conn);
                    break;
                }
            } // inner foreach
            $x++;
            $y++;
        } // outer foreach
        //do double encoded utf-8 from latin-1 back to proper utf-8
        $this->query("SET NAMES latin1;", $this->conn);
        $this->query("ALTER TABLE players MODIFY COLUMN player_nickname TEXT CHARACTER SET latin1; ", $this->conn);
        $this->query("ALTER TABLE players MODIFY COLUMN player_nickname blob; ", $this->conn);
        $this->query("ALTER TABLE players MODIFY COLUMN player_nickname VARCHAR(100) CHARACTER SET utf8; ", $this->conn);
        $this->query("SET NAMES utf8;", $this->conn);

        /*
          $this->query("USE " . $this->config['xaseco_db'] . ";", $this->conn);
          $xkarma = $this->query("SELECT p.login as karma_playerlogin, Score as karma_value, c.uid as karma_trackuid FROM challenges c, players p, rs_karma r where r.Playerid = p.id and r.ChallengeId = c.id;", $this->conn);
          while ($xaseco_karma[] = mysql_fetch_object($xkarma));
          unset($xkarma);
          $this->query("USE " . $this->config['exp_db'] . ";", $this->conn);
          $total = count($xaseco_karma);
          $x = 1;
          $y = 0;
          $this->c("* Migrating karma *", true);
          foreach ($xaseco_karma as $data) {
          if (empty($data))
          continue;
          if ($data->karma_value == -1)
          $karma = 0;
          if ($data->karma_value == 1)
          $karma = 5;
          $this->query("INSERT INTO karma (`karma_playerlogin`, `karma_trackuid`, `karma_value`) VALUES ('" . mysql_escape_string($data->karma_playerlogin) . "','" . mysql_escape_string($data->karma_trackuid) . "','" . mysql_escape_string($karma) . "');", $this->conn);

          // for pretty output to user :)
          $percentage = round(($x / $total) * 100, 0);
          if ($y == round($total / 20, 0)) {
          $this->c($percentage . "% done...");
          $y = 0;
          }
          $x++;
          $y++;
          } */
    }

    function query($query, $link) {
        $result = mysql_query($query, $link);
        if (!$result) {
            $message = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Query:' . $query;
            die($message);
        }
        return $result;
    }

    function theEnd() {
        $this->hr();
    }

    function hr() {
        for ($x = 0; $x < 79; $x++) {
            print "#";
        }
        print "\n";
    }

    function c($string, $center = false) {
        if ($center) {
            $len = (80 / 2) - (strlen($string) / 2);
            for ($x = 0; $x < $len; $x++)
                print " ";
            print $string . "\n";
        } else {
            print $string . "\n";
        }
    }

}

?>