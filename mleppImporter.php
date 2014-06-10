<?php

set_time_limit(0);
error_reporting(E_ALL ^ E_DEPRECATED);

// launch process
$Ximporter = new Mimporter();

class Mimporter
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
        $this->c("from Mlepp to eXpansion", true);
        $this->hr();
    }

    function readconfig()
    {
        if (!file_exists("mlepp_migration.ini"))
            die("Cannot locate main configuration file: mlepp_migration.ini.");
        $this->config = parse_ini_file("mlepp_migration.ini");
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
        /*if (trim($input) != "DO IT")
            die();*/

        $this->hr();
        $this->c("DO NOT INTERRUPT THE PROCESS", true);
        $this->hr();

        //$this->query('SET AUTOCOMMIT=0', $this->conn);
        //$this->query('START TRANSACTION;', $this->conn);

        $map = array('player_login'=>'player_login',
            'player_nickname'=>'player_nickname',
            'player_updated2'=> 'player_updated',
            'player_wins'=> 'player_wins',
            'player_timeplayed'=> 'player_timeplayed',
            'player_onlinerights'=> 'player_onlinerights',
            'player_ip'=> 'player_ip'
        );
        $this->merge($this->config['mlepp_db'].'.players', $this->config['exp_db'].'.exp_players', $map, array('player_updated2' => 'UNIX_TIMESTAMP(player_updated) as player_updated2'));

        //$this->query('COMMIT;', $this->conn);
    }

    function merge($tableName1, $tableName2, $map, $specials = array()){

        $data1 = array();
        $data2 = array();

        $select = '';

        foreach($map as $t1=>$t2){
            if(isset($specials[$t1])){
                $select .= $specials[$t1].',';
            }else{
                $select .= $t1.',';
            }
        }
        $select = trim($select, ",");

        $query1 = $this->query("SELECT $select FROM $tableName1", $this->conn);
        echo "SELECT $select FROM $tableName1\n\n";

        while ($data1[] = mysql_fetch_array($query1));

        $i = 0;
        $columns = implode(', ',array_values($map));
        $total = count($data1);
        $buffer = "";
        foreach ($data1 as $data) {
            if (empty($data))
                continue;
            if($i > 0 && $i%5 == 0){
                $buffer = trim($buffer, ",");
                echo "INSERT INTO $tableName2 ($columns) VALUES $buffer; \n\n";
                $this->query("INSERT INTO $tableName2 ($columns) VALUES $buffer;", $this->conn);
                $buffer = "";
                // for pretty output to user :)
                $percentage = round((($i+1) / $total) * 100, 0);
                $this->c($percentage . "%...");
            }
            $buffer .= "(";
            foreach($data as $key=>$var){
                if(isset($map[$key])){
                    echo $key."\n";
                    $buffer .= "'".mysql_escape_string($var)."',";
                }
            }
            echo "\n";
            $buffer = trim($buffer, ",");
            $buffer .= '),';
            $i++;
        }

        if($buffer != ''){
            $buffer = trim($buffer, ",");
            $this->query("INSERT INTO $tableName2 ($columns) VALUES $buffer;", $this->conn);
        }
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
