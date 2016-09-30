<?php

set_time_limit(0);
error_reporting(E_ALL ^ E_DEPRECATED);

// launch process
$Ximporter = new Ximporter();

class Ximporter
{

    private $config;
    private $conn;

    function __construct()
    {
        $this->welcome();
        $this->readconfig();
        $this->connectdb();
        $this->dothejob();
        $this->disconnect();
        $this->theEnd();
    }

    function welcome()
    {
        $this->hr();
        $this->c("from Xaseco to eXpansion", true);
        $this->hr();
    }

    function readconfig()
    {
        if (!file_exists("xaseco_migration.ini")) {
            die("Cannot locate main configuration file: xaseco_migration.ini.");
        }
        $this->config = parse_ini_file("xaseco_migration.ini");
        if ($this->config === false) {
            die("# Fatal error reading configuration file. Check .ini syntax");
        }
    }

    function connectdb()
    {
        $this->c(" Connecting to database.... hold on...");
        $this->conn = mysql_connect(
            $this->config['host'] . ':' . $this->config['port'],
            $this->config['login'],
            $this->config['password']
        );
        if (!$this->conn) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_set_charset("utf8", $this->conn);
        $this->c(' Connected successfully');
    }

    function disconnect()
    {
        mysql_close($this->conn);
        $this->hr();
        $this->c("* All done! * ", true);
    }

    function dothejob()
    {
        $this->hr();
        $this->c("* By now you are aware that this is ONE TIME operation * ", true);
        $this->c("* Once completed, do not run again! * ", true);
        $this->c("* WARNING * ", true);
        $this->c("* Only way to rollback is to start new eXpansion database * ", true);
        $this->c("* Once started, do not interupt the process *", true);
        $this->hr();
        print "Type \"DO IT\" and press enter to continue: ";
        $input = fgets(STDIN);
        if (trim($input) != "DO IT") {
            die();
        }

        $this->hr();
        $this->c("DO NOT INTERRUPT THE PROCESS", true);
        $this->hr();


        $this->query("USE " . $this->config['xaseco_db'] . ";", $this->conn);

        $records = $this->query(
            "Select
p.Login as record_playerlogin, c.Uid as record_challengeuid,
r.score as record_score, UNIX_TIMESTAMP(r.Date) as record_date,
r.Checkpoints as record_checkpoints,
r.score as record_avgScore
FROM maps c, players p, records r where r.Playerid = p.id and r.mapid = c.id;",
            $this->conn
        );
        while ($xaseco_recs[] = mysql_fetch_object($records)) ;
        unset($records);
        $this->query("USE " . $this->config['exp_db'] . ";", $this->conn);


        $mplayers = $this->query("SELECT * FROM exp_players p;", $this->conn);
        while ($exp_players[] = mysql_fetch_assoc($mplayers)) ;

        $total = count($xaseco_recs);
        $x = 1;
        $y = 0;
        $this->c("* Migrating localrecords *", true);
        $buffer = "";
        foreach ($xaseco_recs as $data) {
            if (empty($data)) {
                continue;
            }
            // do query every 50 values
            if ($y % 100 == 0 && $y > 0) {
                $buffer = trim($buffer, ",");
                $this->query(
                    "INSERT INTO exp_records (`record_challengeuid`, `record_playerlogin`, `record_nbLaps`,
 `record_score`, `record_nbFinish`, `record_avgScore`, `record_checkpoints`, `record_date`) VALUES $buffer;",
                    $this->conn
                );
                $buffer = "";
                // for pretty output to user :)
                $percentage = round(($x / $total) * 100, 0);
                $this->c($percentage . "%...");
            }
            $buffer .= "('" . mysql_escape_string($data->record_challengeuid)
                . "','" . mysql_escape_string($data->record_playerlogin) . "','1', '"
                . mysql_escape_string($data->record_score) . "','1','" . mysql_escape_string($data->record_score)
                . "','" . mysql_escape_string($data->record_checkpoints)
                . "','" . mysql_escape_string($data->record_date) . "' ),";

            $x++;
            $y++;
        }
        // if buffer had some values, write them..
        $buffer = trim($buffer, ",");
        $this->query(
            "INSERT INTO exp_records (`record_challengeuid`, `record_playerlogin`, `record_nbLaps`, `record_score`,
 `record_nbFinish`, `record_avgScore`, `record_checkpoints`, `record_date`) VALUES $buffer;",
            $this->conn
        );
        $buffer = "";
        $this->c("Done!");

        unset($xaseco_recs);

        $this->query("USE " . $this->config['xaseco_db'] . ";", $this->conn);


        $xplayers = $this->query(
            "SELECT Login as player_login, NickName as player_nickname, UNIX_TIMESTAMP(UpdatedAt) as player_updated,
 Wins as player_wins, TimePlayed as player_timeplayed FROM players p;",
            $this->conn
        );

        while ($xaseco_players[] = mysql_fetch_object($xplayers)) ;
        unset($xplayer);

        $this->query("USE " . $this->config['exp_db'] . ";", $this->conn);

        $this->c("* Migrating Players *", true);
        $total = count($xaseco_players);
        $x = 1;
        $y = 0;
        $buffer = "";
        // do the players sort
        foreach ($xaseco_players as $data) {
            if (empty($data)) {
                continue;
            }
            if ($y % 100 == 0 && $y > 0) {
                $buffer = trim($buffer, ",");
                $this->query(
                    "INSERT INTO exp_players (`player_login`, `player_nickname`, `player_updated`, `player_wins`,
 `player_timeplayed`) VALUES $buffer;",
                    $this->conn
                );
                $buffer = "";
                // for pretty output to user :)
                $percentage = round(($x / $total) * 100, 0);
                $this->c($percentage . "%...");
            }
            $buffer .= "('" . mysql_real_escape_string($data->player_login, $this->conn)
                . "','" . mysql_real_escape_string($data->player_nickname, $this->conn)
                . "', '" . $data->player_updated . "','" . $data->player_wins . "','"
                . $data->player_timeplayed . "'),";

            $x++;
            $y++;
        } // outer foreach

        $buffer = trim($buffer, ",");
        $this->query(
            "INSERT INTO exp_players (`player_login`, `player_nickname`, `player_updated`, `player_wins`,
 `player_timeplayed`) VALUES $buffer;",
            $this->conn
        );
        $buffer = "";
        $this->c($percentage . "Done.");



        $this->query("USE " . $this->config['xaseco_db'] . ";", $this->conn);

        $xkarma = $this->query(
            "SELECT p.login as login, Score as rating, c.uid as uid FROM maps c, players p, rs_karma r 
where r.Playerid = p.id and r.MapId = c.id;",
            $this->conn
        );
        while ($xaseco_karma[] = mysql_fetch_object($xkarma)) ;
        unset($xkarma);
        $this->query("USE " . $this->config['exp_db'] . ";", $this->conn);

        $total = count($xaseco_karma);
        $x = 1;
        $y = 0;
        $buffer = "";
        $this->c("* Migrating karma *", true);
        foreach ($xaseco_karma as $data) {
            if (empty($data)) {
                continue;
            }

            if ($y % 100 == 0 && $y > 0) {
                $buffer = trim($buffer, ",");
                $this->query("INSERT INTO exp_ratings (`login`, `uid`, `rating`) VALUES $buffer;", $this->conn);
                $buffer = "";
                // for pretty output to user :)
                $percentage = round(($x / $total) * 100, 0);
                $this->c($percentage . "%...");
            }

            $karma = 3;
            switch ($data->rating) {
                case -3:
                    $karma = 0;
                    break;
                case -2:
                    $karma = 1;
                    break;
                case -1:
                    $karma = 2;
                    break;
                case 1:
                    $karma = 3;
                    break;
                case 2:
                    $karma = 4;
                    break;
                case 3:
                    $karma = 5;
                    break;
            }
            $buffer .= "('" . mysql_escape_string($data->login) . "','"
                . mysql_escape_string($data->uid) . "','" . mysql_escape_string($karma) . "'),";
            $x++;
            $y++;
        }
        $buffer = trim($buffer, ",");
        $this->query("INSERT INTO exp_ratings (`login`, `uid`, `rating`) VALUES $buffer;", $this->conn);
        $buffer = "";
        $this->c("done.");


        ///////////////////////////
        /// DONATIONS MIGRATION ///
        ///////////////////////////
        $this->query("USE " . $this->config['xaseco_db'] . ";", $this->conn);
        $xdons = $this->query(
            "SELECT p.login AS transaction_fromLogin, Donations AS transaction_amount
FROM players p, players_extra r
WHERE r.Playerid = p.id
AND Donations >0;",
            $this->conn
        );
        while ($xaseco_dons[] = mysql_fetch_object($xdons)) ;
        unset($xdons);
        $this->query("USE " . $this->config['exp_db'] . ";", $this->conn);
        $total = count($xaseco_dons);
        $x = 1;
        $y = 0;
        $buffer = "";
        $this->c("* Migrating donations *", true);
        foreach ($xaseco_dons as $data) {
            if (empty($data)) {
                continue;
            }

            if ($y % 100 == 0 && $y > 0) {
                $buffer = trim($buffer, ",");
                $this->query(
                    "INSERT INTO exp_planet_transaction (`transaction_fromLogin`, `transaction_toLogin`, 
`transaction_plugin`, `transaction_subject`, `transaction_amount`) VALUES $buffer;",
                    $this->con
                );
                $buffer = "";
                // for pretty output to user :)
                $percentage = round(($x / $total) * 100, 0);
                $this->c($percentage . "%...");
            }
            $buffer .= "('" . mysql_escape_string($data->transaction_fromLogin)
                . "','" . mysql_escape_string($this->config['transaction_toLogin'])
                . "','eXpansion\DonatePanel','server_donation','"
                . mysql_escape_string($data->transaction_amount) . "'),";

            $x++;
            $y++;
        }
        $buffer = trim($buffer, ",");
        $this->query(
            "INSERT INTO exp_planet_transaction (`transaction_fromLogin`, `transaction_toLogin`, `transaction_plugin`, 
`transaction_subject`, `transaction_amount`) VALUES $buffer;",
            $this->conn
        );
        $buffer = "";
        $this->c("done.");
    }

    function query($query, $link)
    {
        $result = mysql_query($query, $link);
        if (!$result) {
            $message = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Query:' . $query;
            die($message);
        }

        return $result;
    }

    function theEnd()
    {
        $this->hr();
    }

    function hr()
    {
        for ($x = 0; $x < 79; $x++) {
            print "#";
        }
        print "\n";
    }

    function c($string, $center = false)
    {
        if ($center) {
            $len = (80 / 2) - (strlen($string) / 2);
            for ($x = 0; $x < $len; $x++) {
                print " ";
            }
            print $string . "\n";
        } else {
            print $string . "\n";
        }
    }
}
