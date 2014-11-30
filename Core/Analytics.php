<?php
/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\Data\Storage;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;
use ManiaLive\PluginHandler\PluginHandler;
use ManiaLive\Utilities\Console;
use ManiaLive\Utilities\Logger;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

/**
 * Class Analytics, sends anonymous data to eXpansion analytics server in order to check eXpansion usage.
 *
 * @package ManiaLivePlugins\eXpansion\Core
 */
class Analytics implements \ManiaLive\Features\Tick\Listener
{

	const ACTIVE_PING = 600; //Every 10 minutes is enought
	const NOT_ACTIVE_PING = 14400; //If issue try in 4 hours again.

	private $url = 'http://server1.oliver-decramer.com/exp/input.php';

	private $enable = false;

	private $lasPing = 0;
	private $active = false;
	private $key = null;

	private $running = false;

	/** @var Storage */
	private $storage = null;

	/** @var \ManiaLivePlugins\eXpansion\Helpers\Storage */
	private $expStorage = null;

	/** @var PluginHandler */
	private $pluginHandler = null;

	function __construct()
	{
		$this->storage = Storage::getInstance();
		$this->expStorage = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance();
		$this->pluginHandler = PluginHandler::getInstance();
	}

	public function enable()
	{
		if ($this->enable) {
			return;
		}

		$this->console('');
		$this->console('-------------------------------------------------------------------------------');
		$this->console('');
		$this->console('                  Enabling eXpansion Analytics Tool : ');
		$this->console('This will gather some anonymous data from you server in order to improve');
		$this->console('eXpansion and it\'s components. It can always be disabled in the settings.');
		$this->console('We would appreciate it if you let it run. Thanks');
		$this->console('eXpansion Dev Team.');
		$this->console('');
		$this->console('-------------------------------------------------------------------------------');
		$this->console('');


		Dispatcher::register(TickEvent::getClass(), $this);
		$this->enable = true;
	}

	public function disable()
	{
		if (!$this->enable) {
			return;
		}

		$this->console('');
		$this->console('-------------------------------------------------------------------------------');
		$this->console('');
		$this->console('                   Disablin eXpansion Analytics Tool : ');
		$this->console('We are sorry that you have disabled analytics tool. ');
		$this->console('eXpansion Dev Team.');
		$this->console('');
		$this->console('-------------------------------------------------------------------------------');
		$this->console('');

		Dispatcher::unregister(TickEvent::getClass(), $this);
		$this->enable = false;
	}

	public function destroy()
	{
		Dispatcher::unregister(TickEvent::getClass(), $this);
	}

	/**
	 * Event launch every seconds
	 */
	function onTick()
	{
		if (!$this->active || $this->key == null) {
			if (!$this->running && $this->lasPing + self::NOT_ACTIVE_PING < time()) {
				$this->lasPing = time();
				$this->running = true;
				$this->handshake();
			}
		} else {
			if (!$this->running && $this->lasPing + self::ACTIVE_PING < time()) {
				$this->lasPing = time();
				$this->running = true;
				$this->ping();
			}
		}
	}

	public function handshake()
	{
		/** @var DataAccess $access */
		$access = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();

		$data = array(
			'page'=>'handshake',
			'server-login' => $this->storage->serverLogin
		);

		$url = $this->url."?".$this->generate($data);

		$access->httpGet($url, Array($this, "completeHandshake"), $data, "Manialive/eXpansion", "application/json");
	}

	public function completeHandshake($data){

		$this->running = false;
		if (!$data)
			return;

		$json = json_decode($data);

		if(isset($json->key) && !empty($json->key)) {
			$this->active = true;
			$this->key = $json->key;

			$this->lasPing = time();
			$this->ping();
		}
	}

	public function ping()
	{
		/** @var DataAccess $access */
		$access = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();

		$buildDate = Helper::getBuildDate();

		$plugins = array();
		foreach($this->pluginHandler->getLoadedPluginsList() as $plugin){
			$plugins[] = str_replace('\\', '__', $plugin);
		}

		$data = array(
			'page'=>'ping',
			'key' => $this->key,
			'nbPlayers' => count($this->storage->players) + count($this->storage->spectators),
			'country' => $this->expStorage->serverCountry,
			'version' => Core::EXP_VERSION,
			'php_version' => $this->expStorage->cleanPhpVersion,
			'memory' => memory_get_usage(),
			'memory_peak' => memory_get_peak_usage(),
			'build' => $this->getDateTime($buildDate),
			'game' => $this->expStorage->simpleEnviTitle,
			'title' => str_replace('@', '_', $this->expStorage->titleId),
			'mode' => $this->storage->gameInfos->gameMode == 0 ? $this->storage->gameInfos->scriptName : $this->storage->gameInfos->gameMode,
			'plugins' => implode(',',$this->pluginHandler->getLoadedPluginsList()),
			'serverOs' => $this->expStorage->serverOs,
		);

		$url = $this->url."?".$this->generate($data);

		$access->httpGet($url, Array($this, "completePing"), $data, "Manialive/eXpansion", "application/json");
	}

	private function generate($mapping) {
		$url = '';
		foreach($mapping as $key => $value){
			$url .= "$key=".urlencode($value).'&';
		}

		return $url;
	}

	public function completePing($data)
	{
		$this->running = false;
	}


	private function getDateTime($time) {
		return date('Y-m-d',($time)).'T'.date('H:i:s', ($time)).'Z';
	}

	protected function console($message) {
		$logFile = $this->storage->serverLogin . ".console.log";
		/** @var Logger */
		$logger = Logger::getLog("eXpansion");

		Console::println($message);
		$logger::log($message, true, $logFile);

	}
}