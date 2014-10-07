<?php

/*
 * Copyright (C) 2014 Reaby
 *
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
 */

namespace ManiaLivePlugins\eXpansion\MXKarma\Classes;

use ManiaLive\Data\Storage;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Core\DataAccess;
use ManiaLivePlugins\eXpansion\Helpers\Storage as Storage2;
use ManiaLivePlugins\eXpansion\MXKarma\Events\MXKarmaEvent;
use ManiaLivePlugins\eXpansion\MXKarma\Structures\MXRating;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

/**
 * Description of Connection
 *
 * @author Reaby
 */
class Connection
{

	/** @var DataAccess */
	public $dataAccess;

	/** @var Storage */
	public $storage;

	/** @var Storage2 */
	public $expStorage;

	public $address = "http://karma.mania-exchange.com/api2/";

	private $connected = false;

	private $sessionKey = null;

	private $sessionSeed = null;

	private $apikey = "";

	/** @var MXRating */
	private $ratings = null;

	public function __construct()
	{
		$this->dataAccess = DataAccess::getInstance();
		$this->storage = Storage::getInstance();
		$this->expStorage = Storage2::getInstance();
	}

	public function connect($serverLogin, $apikey)
	{
		$this->apikey = $apikey;

		$params = array("serverLogin" => $serverLogin, "applicationIdentifier" => "eXpansion " . Core::EXP_VERSION, "testMode" => "false");
		$this->dataAccess->httpGet($this->build("startSession", $params), array($this, "xConnect"), array(), "ManiaLive - eXpansionPluginPack", "application/json");
	}

	public function xConnect($answer, $httpCode)
	{

		if ($httpCode != 200) {
			echo "http error" . $httpCode . "\n";
			return;
		}

		$data = $this->getObject($answer, "onConnect");

		if ($data === null) {
			// print_r($answer);
			return;
		}

		$this->sessionKey = $data->sessionKey;
		$this->sessionSeed = $data->sessionSeed;

		$outHash = hash("sha512", ($this->apikey . $this->sessionSeed));

		$params = array("sessionKey" => $this->sessionKey, "activationHash" => $outHash);
		$this->dataAccess->httpGet($this->build("activateSession", $params), array($this, "xActivate"), array(), "ManiaLive - eXpansionPluginPack", "application/json");
	}

	public function xActivate($answer, $httpCode)
	{

		if ($httpCode != 200) {
			echo "http error" . $httpCode . "\n";
			return;
		}

		$data = $this->getObject($answer, "onActivate");

		if ($data === null) {
			// print_r($answer);
			return;
		}

		if ($data->activated) {
			$this->connected = true;
			Dispatcher::dispatch(new MXKarmaEvent(MXKarmaEvent::ON_CONNECTED));
		}
	}

	public function getRatings($players = array(), $getVotesOnly = false)
	{
		if (!$this->connected)
			return;

		$params = array("sessionKey" => $this->sessionKey);
		$postData = array("gamemode" => $this->getGameMode(), "titleid" => $this->expStorage->titleId, "mapuid" => $this->storage->currentMap->uId, "getvotesonly" => $getVotesOnly, "playerlogins" => $players);
		$this->dataAccess->httpPost($this->build("getMapRating", $params), json_encode($postData), array($this, "xGetRatings"), array(), "ManiaLive - eXpansionPluginPack", "application/json");
	}

	public function saveVotes(\Maniaplanet\DedicatedServer\Structures\Map $map, $time, $votes)
	{
		if (!$this->connected)
			return;

		$params = array("sessionKey" => $this->sessionKey);
		$postData = array("gamemode" => $this->getGameMode(), "titleid" => $this->expStorage->titleId, "mapuid" => $map->uId, "mapname" => $map->name, "mapauthor" => $map->author, "isimport" => false, "maptime" => $time, "votes" => $votes);
		$this->dataAccess->httpPost($this->build("saveVotes", $params), json_encode($postData), array($this, "xSaveVotes"), array(), "ManiaLive - eXpansionPluginPack", "application/json");
	}

	public function xSaveVotes($answer, $httpCode)
	{

		if ($httpCode != 200) {
			echo "http error" . $httpCode . "\n";
			return;
		}

		$data = $this->getObject($answer, "getRatings");

		if ($data === null) {
			// print_r($answer);
			return;
		}

		Dispatcher::dispatch(new MXKarmaEvent(MXKarmaEvent::ON_VOTE_SAVE, $data->updated));
	}

	public function xGetRatings($answer, $httpCode)
	{

		if ($httpCode != 200) {
			echo "http error" . $httpCode . "\n";
			return;
		}

		$data = $this->getObject($answer, "getRatings");

		if ($data === null) {
			// print_r($answer);
			return;
		}

		$this->ratings = new MXRating();
		$this->ratings->append($data);
		Dispatcher::dispatch(new MXKarmaEvent(MXKarmaEvent::ON_VOTES_RECIEVED, $this->ratings));
	}

	public function getGameMode()
	{
		switch ($this->storage->gameInfos->gameMode) {
			case GameInfos::GAMEMODE_SCRIPT:
				$gamemode = strtolower($this->storage->gameInfos->scriptName);
				break;
			case GameInfos::GAMEMODE_ROUNDS:
				$gamemode = "Rounds";
				break;
			case GameInfos::GAMEMODE_TIMEATTACK:
				$gamemode = "TimeAttack";
				break;
			case GameInfos::GAMEMODE_TEAM:
				$gamemode = "Team";
				break;
			case GameInfos::GAMEMODE_LAPS:
				$gamemode = "Laps";
				break;
			case GameInfos::GAMEMODE_CUP:
				$gamemode = "Cup";
				break;
		}
		return $gamemode;
	}

	public function getObject($data, $origin = "onRecieve")
	{
		$obj = (object) json_decode($data);
		if ($obj->success == false) {
			$this->handleErrors($obj, $origin);
			return null;
		}
		return $obj->data;
	}

	public function handleErrors($obj, $origin = "onRecieve")
	{
		switch ($obj->data->code) {
			case 2:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
				$this->connected = false;

			default:
				break;
		}

		Dispatcher::dispatch(new MXKarmaEvent(MXKarmaEvent::ON_ERROR, $origin, $obj->data->code, $obj->data->message));
	}

	private function build($method, $params)
	{
		$url = $this->address . $method;
		$first = true;
		$buffer = "";
		foreach ($params as $key => $value) {
			$prefix = "&";
			if ($first) {
				$first = false;
				$prefix = "?";
			}
			$buffer .= $prefix . $key . "=" . rawurlencode($value);
		}

		return $url . $buffer;
	}

	public function isConnected()
	{
		return $this->connected;
	}

}
