<?php

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood;

use \Maniaplanet\DedicatedServer\Connection;
use \ManiaLive\Utilities\Time as TmTime;

class Server {

    
    private $server_titleId;
    private $server_login;
    private $server_path;
    private $server_data;
    private $server_isOnline;

    private $server_ladder_max;
    private $server_ladder_min;

    private $server_oldOnline;
    
    private $inDb = false;
    
    function __construct() {
        $this->server_oldOnline = time()-10;
    }

    public function create_fromConnection(Connection $connection, \ManiaLive\Data\Storage $storage) {

        $this->server_titleId = $connection->getVersion()->titleId;
        $this->server_login = $storage->serverLogin;
        $this->server_path = $connection->getDetailedPlayerInfo($storage->serverLogin)->path;

        $ladders = $connection->getLadderServerLimits();
        $this->server_ladder_min = $ladders->ladderServerLimitMin/1000;
        $this->server_ladder_max = $ladders->ladderServerLimitMax/1000;
    }

    public function createXML(Connection $connection, \ManiaLive\Data\Storage $storage) {

        $serverName = $storage->server->name;
        $serverName = \ManiaLib\Utils\Formatting::stripCodes($serverName, 'l');
        $serverName = $this->removespecials($serverName);

        $xml = '<?xml version="1.0" encoding="utf-8" ?>' . "\n";
        $xml .= '<server_neighborhood>' . "\n";
        $xml .= ' <server>' . "\n";
        $xml .= '  <last_modified>' . time() . '</last_modified>' . "\n";
        $xml .= '  <login>' . $this->server_login . '</login>' . "\n";
        $xml .= '  <name>' . $serverName . '</name>' . "\n";
        $xml .= '  <zone>' . $this->server_path . '</zone>' . "\n";
        $xml .= '  <private>' . ($storage->server->password == "" ? 'false' : 'true') . '</private>' . "\n";
        $xml .= '  <game>MP</game>' . "\n";
        $xml .= '  <gamemode>' . $storage->gameInfos->gameMode . '</gamemode>' . "\n";
        $xml .= '  <title>'.$this->server_titleId.'</title>' . "\n";
        $xml .= '  <packmask>'.$this->server_titleId.'</packmask>' . "\n";
        $xml .= '  <players>' . "\n";
        $xml .= '   <current>' . sizeof($storage->players) . '</current>' . "\n";
        $xml .= '   <maximum>' . $storage->server->currentMaxPlayers . '</maximum>' . "\n";
        $xml .= '  </players>' . "\n";
        $xml .= '  <spectators>' . "\n";
        $xml .= '   <current>' . sizeof($storage->spectators) . '</current>' . "\n";
        $xml .= '   <maximum>' . $storage->server->currentMaxSpectators . '</maximum>' . "\n";
        $xml .= '  </spectators>' . "\n";
        $xml .= '  <ladder>' . "\n";
        $xml .= '   <minimum>'.$this->server_ladder_min.'</minimum>' . "\n";
        $xml .= '   <maximum>'.$this->server_ladder_max.'</maximum>' . "\n";
        $xml .= '  </ladder>' . "\n";
        $xml .= ' </server>' . "\n";
        $xml .= ' <current>' . "\n";
        $xml .= '  <map>' . "\n";
        $xml .= '   <name>' . $this->removespecials($storage->currentMap->name) . '</name>' . "\n";
        $xml .= '   <author>' . $storage->currentMap->author . '</author>' . "\n";
        $xml .= '   <environment>' . $storage->currentMap->environnement . '</environment>' . "\n";
        $xml .= '   <mood>' . $storage->currentMap->mood . '</mood>' . "\n";
        $xml .= '   <authortime>' . TmTime::fromTM($storage->currentMap->authorTime) . '</authortime>' . "\n";
        $xml .= '   <goldtime>' . TmTime::fromTM($storage->currentMap->goldTime) . '</goldtime>' . "\n";
        $xml .= '   <silvertime>' . TmTime::fromTM($storage->currentMap->silverTime) . '</silvertime>' . "\n";
        $xml .= '   <bronzetime>' . TmTime::fromTM($storage->currentMap->bronzeTime) . '</bronzetime>' . "\n";
        $xml .= '   <mxurl></mxurl>' . "\n";
        $xml .= '  </map>' . "\n";
        $xml .= '  <players>' . "\n";
        foreach ($storage->players as $player) {
            $nickname = $this->removespecials($player->nickName);
            $nickname = \ManiaLib\Utils\Formatting::stripCodes($nickname, 'l');

            $xml .= '   <player>' . "\n";
            $xml .= '     <nickname>' . $nickname . '</nickname>' . "\n";
            $xml .= '     <login>' . $player->login . '</login>' . "\n";
            $xml .= '     <nation>' . $player->path . '</nation>' . "\n";
            $xml .= '     <ladder>' . $player->ladderRanking . '</ladder>' . "\n";
            $xml .= '     <spectator>false</spectator>' . "\n";
            $xml .= '   </player>' . "\n";
        }
        foreach ($storage->spectators as $player) {
            $nickname = $this->removespecials($player->nickName);
            $nickname = \ManiaLib\Utils\Formatting::stripCodes($nickname, 'l');

            $xml .= '   <player>' . "\n";
            $xml .= '     <nickname>' . $nickname . '</nickname>' . "\n";
            $xml .= '     <login>' . $player->login . '</login>' . "\n";
            $xml .= '     <nation>' . $player->path . '</nation>' . "\n";
            $xml .= '     <ladder>' . $player->ladderRanking . '</ladder>' . "\n";
            $xml .= '     <spectator>true</spectator>' . "\n";
            $xml .= '   </player>' . "\n";
        }
        $xml .= '  </players>' . "\n";
        $xml .= ' </current>' . "\n";
        $xml .= '</server_neighborhood>' . "\n";

        return $xml;
    }

    public function saveToDb(\ManiaLive\Database\Connection $db, \Connection $connection, \ManiaLive\Data\Storage $storage) {

        $data = $this->createXML($connection, $storage);

        if (!$this->inDb) {

            $sql = 'SELECT * FROM ode8_servers WHERE server_login = ' . $db->quote($storage->serverLogin) . '';
            $dbData = $db->query($sql);

            if ($dbData->recordCount() == 0) {
                $sql = 'INSERT INTO ode8_servers VALUES
                            (null, ' . $db->quote($storage->serverLogin) . ', ' . time() . ',' . $db->quote($data) . ')';
                $db->query($sql);
                $this->inDb = true;
                return;
            }
        }

        $sql = 'UPDATE ode8_servers 
                    SET server_lastmodified = ' . $db->quote(time()) . '
                        , server_data = ' . $db->quote($data) . '
                    WHERE server_login = ' . $db->quote($storage->serverLogin) . '';
        $db->query($sql);
    }
   

    private function removespecials($text) {
        $text = str_ireplace('$S', '', $text);        // Remove $S (case-insensitive)
        $text = str_replace('&', '&amp;', $text);     // Convert &
        $text = str_replace('"', '&quot;', $text); // Convert "
        $text = str_replace("'", '&apos;', $text); // Convert '
        $text = str_replace('>', '&gt;', $text);      // Convert >
        $text = str_replace('<', '&lt;', $text);      // Convert <
        return $text;
    }
    
    public function setServer_data($server_data) {
        $this->server_data = $server_data;
        $this->server_isOnline = (($this->server_oldOnline < (int)$this->server_data->server->last_modified) 
                || $this->server_data->server->last_modified + 3600 > time());
        $this->server_oldOnline = (int)$this->server_data->server->last_modified;
        
        if(isset($this->server_data->server->packmask) && !isset($this->server_data->server->title)){
            $this->server_data->server->title = $this->server_data->server->packmask;
            $a = $this->server_data->server->packmask;
            if (strpos($a,'TM') === false && strpos($a,'SM') === false && strpos($a,'QM') === false) {
                $a = strtolower($a);
                $a[0] = strtoupper($a[0]);
                $this->server_data->server->title = 'TM'.$a;
            }
        }
    }
    
    public function getServer_data() {
        return $this->server_data;
    }

    public function isOnline(){
        return $this->server_isOnline;
    }
}

?>
