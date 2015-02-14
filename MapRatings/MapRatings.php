<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets\EndMapRatings;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Windows\MapRatingsManager;
use ManiaLivePlugins\eXpansion\MapRatings\Structures\PlayerVote;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\MapRatings\Events\Event;

class MapRatings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	/** @var Config */
	private $config;

	private $msg_rating;

	private $msg_noRating;

	private $pendingRatings = array();

	private $oldRatings = array();

	function exp_onInit()
	{
		EndMapRatings::$parentPlugin = $this;
		$actionFinal = ActionHandler::getInstance()->createAction(array($this, "autoRemove"));
		Gui\Windows\MapRatingsManager::$removeId = \ManiaLivePlugins\eXpansion\Gui\Gui::createConfirm($actionFinal);
		$this->config = Config::getInstance();
	}

	function exp_onLoad()
	{
		$this->enableDatabase();
		$this->enableDedicatedEvents();
		$this->msg_rating = exp_getMessage('#rating#Map Approval Rating: #variable#%2$s#rating# (#variable#%3$s #rating#votes).  Your Rating: #variable#%4$s#rating# / #variable#5');  // '%1$s' = Map Name, '%2$s' = Rating %, '%3$s' = # of Ratings, '%4$s' = Player's Rating);
		$this->msg_noRating = exp_getMessage('#rating# $iMap has not been rated yet!');
		if (!$this->db->tableExists("exp_ratings")) {
			$this->db->execute('CREATE TABLE IF NOT EXISTS `exp_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` text NOT NULL,
  `login` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;');
		}

		$cmd = $this->registerChatCommand("rate", "chatRate", 1, true);
		$cmd->help = '/rate +++, /rate ++, /rate +-, /rate --, /rate --- or /rate 5, /rate 4, /rate 3. /rate 2, /rate 1';
		$cmd = $this->registerChatCommand("rating", "chatRating", 1, true);
		$cmd->help = '/rating Map Rating Approval';

		$this->setPublicMethod("getRatings");
		$this->setPublicMethod("getVotesForMap");
		$this->setPublicMethod("showRatingsManager");
	}

	public function exp_onReady()
	{
		$this->reload();
	}

	public function getRatings()
	{
		$ratings = $this->db->execute("SELECT uid, avg(rating) AS rating, COUNT(rating) AS ratingTotal FROM exp_ratings GROUP BY uid;")->fetchArrayOfObject();
		$out = array();
		foreach ($ratings as $rating) {
			$out[$rating->uid] = new Structures\Rating($rating->rating, $rating->ratingTotal, $rating->uid);
		}
		return $out;
	}

	/**
	 *
	 * @param null|string|\Maniaplanet\DedicatedServer\Structures\Map $uId
	 * @return PlayerVote[]
	 */
	public function getVotesForMap($uId = null)
	{
		if ($uId instanceof \Maniaplanet\DedicatedServer\Structures\Map) {
			$uId = $uId->uId;
		}
		else {
			$uId = $this->storage->currentMap->uId;
		}

		$ratings = $this->db->execute("SELECT login, rating FROM exp_ratings WHERE `uid` = " . $this->db->quote($uId) . ";")->fetchArrayOfAssoc();
		//$ratings = $this->db->execute("SELECT count(rating) as yes, (select count(rating) from `exp_ratings` where uid = " . $this->db->quote($uId) . ") as total from `exp_ratings` where rating >= 3 and uid = " . $this->db->quote($uId))->fetchArrayOfAssoc();
		$out = array();
		foreach ($ratings as $data) {
			$vote = PlayerVote::fromArray($data);
			$out[$vote->login] = $vote;
		}
		return $out;
	}

	public function reload()
	{
		$this->oldRatings = $this->getVotesForMap($this->storage->currentMap->uId);
	}

	public function saveRatings($uid)
	{
		try {
			if (empty($this->pendingRatings))
				return;

			$sqlInsert = "INSERT INTO exp_ratings (`uid`, `login`, `rating`  ) VALUES ";
			$loginList = "";
			$i = 0;
			foreach ($this->pendingRatings as $login => $rating) {
				if ($i != 0) {
					$sqlInsert .= ", ";
				}
				$i++;
				$sqlInsert .= "(" . $this->db->quote($uid) . "," . $this->db->quote($login) . "," . $this->db->quote($rating) . ")";
				$loginList .= $this->db->quote($login) . ",";
			}
			$loginList = rtrim($loginList, ",");

			$this->db->execute("DELETE FROM exp_ratings "
					. " WHERE `uid`= " . $this->db->quote($uid) . " "
					. " AND `login` IN (" . $loginList . ")");

			$this->db->execute($sqlInsert);
			$this->pendingRatings = array();
		} catch (\Exception $e) {
			$this->pendingRatings = array();
			$this->console("Error in MapRating: " . $e->getMessage());
		}
	}

	public function saveRating($login, $rating)
	{
		EndMapRatings::Erase($login);

		$this->pendingRatings[$login] = new PlayerVote($login, $rating);

		$votes = array_merge($this->oldRatings, $this->pendingRatings);
		\ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_RATINGS_SAVE, $votes));

		$this->sendRatingMsg($login, $rating);
	}

	function sendRatingMsg($login, $playerRating)
	{
		if ($login != null) {
			if ($playerRating === null) {
				$query = $this->db->execute("SELECT rating AS playerRating FROM exp_ratings WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . " AND `login`=" . $this->db->quote($login) . ";")->fetchObject();
				if ($query === false) {
					$playerRating = "-";
				}
				else {
					$playerRating = $query->playerRating;
				}
			}

			$votes = array_merge($this->oldRatings, $this->pendingRatings);
			$rating = "-";
			$total = count($votes);
			$this->exp_chatSendServerMessage($this->msg_rating, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'), $rating, $total, $playerRating));
		}
	}

	function autoRemove($login)
	{
		if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::map_removeMap)) {

			$filenames = array();
			foreach ($this->autoMapManager_getMaps() as $rating) {
				$filenames[] = $rating->map->fileName;
			}

			try {
				$this->connection->removeMapList($filenames);
				$this->exp_chatSendServerMessage(exp_getMessage("Maps with bad rating removed successfully."));
				Gui\Windows\MapRatingsManager::Erase($login);
			} catch (\Exception $e) {
				$this->exp_chatSendServerMessage("#error#Error: %s", $login, array($e->getMessage()));
			}
		}
	}

	/**
	 * 
	 * @return \ManiaLivePlugins\eXpansion\MapRatings\MapRating[]
	 */
	function autoMapManager_getMaps()
	{
		$items = array();
		foreach ($this->getRatings() as $uid => $rating) {
			$value = round(($rating->rating / 5) * 100);
			if ($rating->totalvotes >= $this->config->minVotes && $value <= $this->config->removeTresholdPercentage) {
				$map = \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::getObjbyPropValue($this->storage->maps, "uId", $uid);
				if ($map) {
					$items[] = new Structures\MapRating($rating, $map);
				}
			}
		}
		return $items;
	}

	function showRatingsManager($login)
	{
		if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::map_removeMap)) {
			$window = Gui\Windows\MapRatingsManager::Create($login);
			$window->setTitle(__("Ratings Manager", $login));
			$window->setSize(120, 90);
			$window->setRatings($this->autoMapManager_getMaps());
			$window->show();
		}
	}

	function chatRating($login = null)
	{
		if ($login !== null) {
			$this->sendRatingMsg($login, null);
		}
	}

	function onBeginMap($var, $var2, $var3)
	{
		$this->reload();
		EndMapRatings::EraseAll();

		//send msg
		if ($this->config->sendBeginMapNotices) {
			if ($this->ratingTotal == 0) {
				$this->exp_chatSendServerMessage($this->msg_noRating, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
			}
			else {
				foreach ($this->storage->players as $login => $player) {
					$this->sendRatingMsg($login, null);
				}
				foreach ($this->storage->spectators as $login => $player) {
					$this->sendRatingMsg($login, null);
				}
			}
		}
	}

	public function onBeginMatch()
	{
		$this->reload();
		EndMapRatings::EraseAll();
	}

	function onEndMatch($rankings = "", $winnerTeamOrMap = "")
	{

		if ($this->config->showPodiumWindow) {
			$ratings = $this->getVotesForMap(null);

			$logins = array();
			foreach ($this->storage->players as $login => $player) {
				if (array_key_exists($login, $ratings) == false) {
					$logins[$login] = $login;
				}
				if (array_key_exists($login, $this->pendingRatings))
					unset($logins[$login]);
			}

			foreach ($this->storage->spectators as $login => $player) {
				if (array_key_exists($login, $ratings) == false) {
					$logins[$login] = $login;
				}
				if (array_key_exists($login, $this->pendingRatings))
					unset($logins[$login]);
			}


			if (sizeof($logins) > 0) {
				\ManiaLive\Gui\Group::Erase("mapratings");
				$group = \ManiaLive\Gui\Group::Create("mapratings", $logins);
				EndMapRatings::EraseAll();
				$widget = EndMapRatings::Create(null);
				$widget->setMap($this->storage->currentMap);
				$widget->show($group);
			}
		}
	}

	public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
	{
		$this->saveRatings($this->storage->currentMap->uid);
	}

	function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
	{
		if ($playerUid == 0)
			return;
		if ($text == "0/5")
			$this->saveRating($login, 0);
		if ($text == "1/5")
			$this->saveRating($login, 0);
		if ($text == "2/5")
			$this->saveRating($login, 0);
		if ($text == "3/5")
			$this->saveRating($login, 5);
		if ($text == "4/5")
			$this->saveRating($login, 5);
		if ($text == "5/5")
			$this->saveRating($login, 5);

		if ($text == "---")
			$this->saveRating($login, 0);
		if ($text == "--")
			$this->saveRating($login, 0);
		if ($text == "-")
			$this->saveRating($login, 0);
		if ($text == "+")
			$this->saveRating($login, 5);
		if ($text == "++")
			$this->saveRating($login, 5);
		if ($text == "+++")
			$this->saveRating($login, 5);
	}

	function chatRate($login, $arg, $param = null)
	{
		if ($login != null) {
			switch ($arg) {
				case "+++":
					$this->saveRating($login, 5);
					break;
				case "++":
					$this->saveRating($login, 5);
					break;
				case "+-":
					$this->saveRating($login, 5);
					break;
				case "--":
					$this->saveRating($login, 0);
					break;
				case "---":
					$this->saveRating($login, 0);
					break;
				case "5":
					$this->saveRating($login, 5);
					break;
				case "4":
					$this->saveRating($login, 5);
					break;
				case "3":
					$this->saveRating($login, 5);
					break;
				case "2":
					$this->saveRating($login, 0);
					break;
				case "1":
					$this->saveRating($login, 0);
					break;
				case "help":
				default:
					$msg = exp_getMessage('#rank# $iUsage /rate #, where number is 1-5..');
					$this->exp_chatSendServerMessage($msg, $login);
					break;
			}
		}
	}

	function exp_onUnload()
	{
		EndMapRatings::EraseAll();
		MapRatingsManager::EraseAll();
	}

}

?>