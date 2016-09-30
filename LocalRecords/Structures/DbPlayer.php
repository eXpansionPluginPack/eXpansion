<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Structures;

class DbPlayer extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $login = "";
    public $nickname = "";
    public $nation = "";
    public $language = "en";

    public function __construct()
    {

    }

    public function fromPlayerObj(\Maniaplanet\DedicatedServer\Structures\Player $player)
    {
        $this->nickname = $player->nickName;
        $this->login = $player->login;
        $this->nation = $player->path;
    }

    public function exportToDb()
    {
        $properties = get_object_vars($this);
        $keys = "";
        $values = "";
        $update = "";
        foreach ($properties as $key => $value) {
            if ($key == "id") {
                continue;
            }
            $key = mysql_real_escape_string($key);
            $value = mysql_real_escape_string($value);

            $keys .= "`" . $key . "`,";
            $values .= "'" . $value . "',";
            $update .= "`" . $key . "`='" . $value . "',";
        }

        $keys = substr($keys, 0, -1);
        $values = substr($values, 0, -1);
        $update = substr($update, 0, -1);

        return "INSERT INTO exp_players (" . $keys . ") 
        VALUES (" . $values . ") ON DUPLICATE KEY UPDATE " . $update . ";";
    }
}
