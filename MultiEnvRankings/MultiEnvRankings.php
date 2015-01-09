<?php

namespace ManiaLivePlugins\eXpansion\MultiEnvRankings;

use DirectoryIterator;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;

class MultiEnvRankings extends ExpPlugin {


    public function exp_onReady()
	{

		$this->enableStorageEvents();
		$this->enableDedicatedEvents();
		$this->enableDatabase();

		$cmd = $this->registerChatCommand("multirank", "chat_showMultiRank", 0, true);
		$cmd->help = 'Show Player Rank';
		
		foreach ($this->storage->players as $player) {
		$this->connection->chatSendServerMessage("Type /multirank to see your multiEnvironment Rankings!!!!", $player->login);
		}
	}
	
	function chat_showMultiRank($login = null)
	{
	
	if ($login != null) {
			$nbTrack = count($this->storage->maps);
			$Valleyuids = $this->getValleyUidSqlString();
			$Canyonuids = $this->getCanyonUidSqlString();
			$Stadiumuids = $this->getStadiumUidSqlString();
			$ValleyTotalRanked = $this->getValleyTotalRanked();
			$CanyonTotalRanked = $this->getCanyonTotalRanked();
			$StadiumTotalRanked = $this->getStadiumTotalRanked();
			
			
			$ValleyPlayerRank = $this->getValleyPlayerRank($login);
			$CanyonPlayerRank = $this->getCanyonPlayerRank($login);
			$StadiumPlayerRank = $this->getStadiumPlayerRank($login);

			$qValley = 'SELECT AVG(rank_rank) as tscore from exp_ranks where rank_playerlogin = ' . $this->db->quote($login)
					. ' and rank_challengeuid IN (' . $Valleyuids . ')';
			
			$qCanyon = 'SELECT AVG(rank_rank) as tscore from exp_ranks where rank_playerlogin = ' . $this->db->quote($login)
					. ' and rank_challengeuid IN (' . $Canyonuids . ')';
					
			$qStadium = 'SELECT AVG(rank_rank) as tscore from exp_ranks where rank_playerlogin = ' . $this->db->quote($login)
					. ' and rank_challengeuid IN (' . $Stadiumuids . ')';
					
			$dataValley = $this->db->execute($qValley);
			$dataCanyon = $this->db->execute($qCanyon);
			$dataStadium = $this->db->execute($qStadium);
			//var_dump($qValley);
			//var_dump($qCanyon);
			//var_dump($qStadium);
			if ($dataValley->recordCount() == 0) {
			$Valleypoints = 0;
			}
			else {
				$ValleyValues = $dataValley->fetchStdObject();
				$Valleypoints = $ValleyValues->tscore;
				if (empty($Valleypoints)) {
				$Valleypoints = 0;
				}
			}
			//var_dump($Valleypoints);
			if ($dataCanyon->recordCount() == 0) {
			$Canyonpoints = 0;
			}
			else {
				$CanyonValues = $dataCanyon->fetchStdObject();
				$Canyonpoints = $CanyonValues->tscore;
				if (empty($Canyonpoints)) {
				$Canyonpoints = 0;
				}
			}
			
			if ($dataStadium->recordCount() == 0) {
			$Stadiumpoints = 0;
			}
			else {
				$StadiumValues = $dataStadium->fetchStdObject();
				$Stadiumpoints = $StadiumValues->tscore;
				if (empty($Stadiumpoints)) {
				$Stadiumpoints = 0;
				}
			}
			
		$xml = '<manialinks>';
		$xml .= '<manialink version="2">';
		$xml .= '<quad posn="-105 38 -1" sizen="195 111" style="Bgs1InRace" substyle="BgWindow1"/>';
		$xml .= '<quad posn="-105 39 0" sizen="195 11" style="Bgs1InRace" substyle="BgTitle3"/>';
		$xml .= '<label posn="-4.5 25 1" halign="center" textsize="3" text="Your server ranking"/>';
		$xml .= '<quad posn="-28 21 0" sizen="51 13" style="Bgs1InRace" substyle="BgTitle3_2"/>';
		$xml .= '<label posn="-76 17 1" halign="center" textsize="3" text="Ranking" sizen="56 7"/>';
		$xml .= '<quad posn="37 21 0" sizen="51 13" style="Bgs1InRace" substyle="BgTitle3_2"/>';
		$xml .= '<label posn="-3 16 1" halign="center" textsize="3" text="Average"/>';
		$xml .= '<quad posn="-102 21 0" sizen="51 13" style="Bgs1InRace" substyle="BgTitle3_2"/>';
		$xml .= '<label posn="64 16 1" halign="center" textsize="3" text="Environment"/>';
		$xml .= '<quad bgcolor="800E" posn="-104 -11 0" sizen="193 10"/>';
		$xml .= '<label posn="60 -14 1" halign="center" textsize="3" text="Valley"/>';
		$xml .= '<label posn="-76 -14 1" halign="center" textsize="3" text="'.$ValleyPlayerRank.' (of '.$ValleyTotalRanked.')"/>';
		$xml .= '<label posn="-1 -14 1" halign="center" textsize="3" text="'.$Valleypoints.'"/>';
		$xml .= '<quad bgcolor="700E" posn="-104 -21 0" sizen="193 10"/>';
		$xml .= '<label posn="60 -24 1" halign="center" textsize="3" text="Stadium"/>';
		$xml .= '<label posn="-76 -24 1" halign="center" textsize="3" text="'.$StadiumPlayerRank.' (of '.$StadiumTotalRanked.')"/>';
		$xml .= '<label posn="-1 -24 1" halign="center" textsize="3" text="'.$Stadiumpoints.'"/>';
		$xml .= '<quad bgcolor="600E" posn="-104 -31 0" sizen="193 10"/>';
		$xml .= '<label posn="60 -34 1" halign="center" textsize="3" text="Canyon"/>';
		$xml .= '<label posn="-76 -34 1" halign="center" textsize="3" text="'.$CanyonPlayerRank.' (of '.$CanyonTotalRanked.')"/>';
		$xml .= '<label posn="-1 -34 1" halign="center" textsize="3" text="'.$Canyonpoints.'"/>';
		$xml .= '<label posn="-1 -64 1" halign="center" style="CardButtonSmall" text="Close" action="0"/>';
		$xml .= '</manialink>';
		$xml .= '</manialinks>';
		$this->connection->sendDisplayManialinkPage($login, $xml, 0, true, true);
	}
	}
	
	public function getValleyPlayerRank($login)
	{
				$Valleyuids = $this->getValleyUidSqlString();

	$q = 'SELECT AVG(rank_rank) as tscore from exp_ranks where rank_playerlogin = ' . $this->db->quote($login)
					. ' and rank_challengeuid IN (' . $Valleyuids . ')';
				$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$ranksaverage = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$ranksaverage = $vals->tscore;
				if (empty($ranksaverage)) {
				$ranksaverage = 0;
				}
			}
	
	$q = 'SELECT COUNT( rank_playerlogin ) as tscore 
	FROM exp_ranks
	WHERE rank_rank >  0
	AND rank_rank <  '.$this->db->quote($ranksaverage).'  and rank_challengeuid IN (' . $Valleyuids . ')';
	
	$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$rank = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$rank = $vals->tscore;
				if (empty($rank)) {
				$rank = 0;
				}
			}
			return $rank + 1;
	}
	
	public function getCanyonPlayerRank($login)
	{
				$Canyonuids = $this->getCanyonUidSqlString();

	$q = 'SELECT AVG(rank_rank) as tscore from exp_ranks where rank_playerlogin = ' . $this->db->quote($login)
					. ' and rank_challengeuid IN (' . $Canyonuids . ')';
				$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$ranksaverage = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$ranksaverage = $vals->tscore;
				if (empty($ranksaverage)) {
				$ranksaverage = 0;
				}
			}
	
	$q = 'SELECT COUNT( rank_playerlogin ) as tscore 
	FROM exp_ranks
	WHERE rank_rank >  0
	AND rank_rank <  '.$this->db->quote($ranksaverage).'  and rank_challengeuid IN (' . $Canyonuids . ')';
	
	$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$rank = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$rank = $vals->tscore;
				if (empty($rank)) {
				$rank = 0;
				}
			}
			return $rank + 1;
	}
	
	public function getStadiumPlayerRank($login)
	{
				$Stadiumuids = $this->getStadiumUidSqlString();
	$q = 'SELECT AVG(rank_rank) as tscore from exp_ranks where rank_playerlogin = ' . $this->db->quote($login)
					. ' and rank_challengeuid IN (' . $Stadiumuids . ')';
				$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$ranksaverage = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$ranksaverage = $vals->tscore;
				if (empty($ranksaverage)) {
				$ranksaverage = 0;
				}
			}
	
	$q = 'SELECT COUNT( rank_playerlogin ) as tscore 
	FROM exp_ranks
	WHERE rank_rank >  0
	AND rank_rank <  '.$this->db->quote($ranksaverage).'  and rank_challengeuid IN (' . $Stadiumuids . ')';
	
	$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$rank = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$rank = $vals->tscore;
				if (empty($rank)) {
				$rank = 0;
				}
			}
			return $rank + 1;
	}
	
	/**
	 * The Total number of player ranked
	 *
	 * @return int
	 */
	public function getValleyTotalRanked()
	{
			$q = 'SELECT COUNT( rank_playerlogin ) as tscore
				FROM exp_ranks
				WHERE rank_challengeuid
				IN (' . $this->getValleyUidSqlString() . ')';
				$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$total_ranked = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$total_ranked = $vals->tscore;
				if (empty($total_ranked)) {
				$total_ranked = 0;
				}
			}
		//var_dump($total_ranked);
		return $total_ranked;
	}
	
	/**
	 * The Total number of player ranked
	 *
	 * @return int
	 */
	public function getCanyonTotalRanked()
	{
			$q = 'SELECT COUNT( rank_playerlogin ) as tscore
				FROM exp_ranks
				WHERE rank_challengeuid
				IN (' . $this->getCanyonUidSqlString() . ')';
				$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$total_ranked = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$total_ranked = $vals->tscore;
				if (empty($total_ranked)) {
				$total_ranked = 0;
				}
			}
		//var_dump($total_ranked);
		return $total_ranked;
	}
	
	/**
	 * The Total number of player ranked
	 *
	 * @return int
	 */
	public function getStadiumTotalRanked()
	{
			$q = 'SELECT COUNT( rank_playerlogin ) as tscore
				FROM exp_ranks
				WHERE rank_challengeuid
				IN (' . $this->getStadiumUidSqlString() . ')';
				$data = $this->db->execute($q);

			if ($data->recordCount() == 0) {
			$total_ranked = 0;
			}
			else {
				$vals = $data->fetchStdObject();
				$total_ranked = $vals->tscore;
				if (empty($total_ranked)) {
				$total_ranked = 0;
				}
			}
		//var_dump($total_ranked);
		return $total_ranked;
	}
	
	/**
	 * Returns a string to be used to in SQL to flter tracks
	 *
	 * @return string
	 */
	public function getValleyUidSqlString()
	{
		$uids = "";
		foreach ($this->storage->maps as $map) {
		if ($map->environnement == "Valley"){
			$uids .= $this->db->quote($map->uId) . ",";
		}
		}

		return trim($uids, ",");
	}
	
	/**
	 * Returns a string to be used to in SQL to flter tracks
	 *
	 * @return string
	 */
	public function getCanyonUidSqlString()
	{
		$uids = "";
		foreach ($this->storage->maps as $map) {
		if ($map->environnement == "Canyon"){
			$uids .= $this->db->quote($map->uId) . ",";
		}
		}

		return trim($uids, ",");
	}
	
	/**
	 * Returns a string to be used to in SQL to flter tracks
	 *
	 * @return string
	 */
	public function getStadiumUidSqlString()
	{
		$uids = "";
		foreach ($this->storage->maps as $map) {
		if ($map->environnement == "Stadium"){
			$uids .= $this->db->quote($map->uId) . ",";
		}
		}

		return trim($uids, ",");
	}

    
}

?>
