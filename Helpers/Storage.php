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

namespace ManiaLivePlugins\eXpansion\Helpers;


use ManiaLib\Utils\Singleton;
use ManiaLive\DedicatedApi\Callback\base64;
use ManiaLive\DedicatedApi\Callback\SMapInfo;
use ManiaLive\DedicatedApi\Callback\SPlayerInfo;
use ManiaLive\DedicatedApi\Callback\SPlayerRanking;
use ManiaLive\DedicatedApi\Callback\StatsName;
use ManiaLive\DedicatedApi\Callback\StatusCode;
use ManiaLive\Event\Dispatcher;
use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;
use ManiaLive\DedicatedApi\Callback\Listener as ServerListener;
use ManiaLivePlugins\eXpansion\Core\RelayLink;
use ManiaLivePlugins\eXpansion\Database\Structures\DbPlayer;
use Maniaplanet\DedicatedServer\Structures\Version;

class Storage extends Singleton implements \ManiaLive\Event\Listener{

    const TITLE_SIMPLE_TM = 'TM';
    const TITLE_SIMPLE_SM = 'SM';

    /**
     * @var \Maniaplanet\DedicatedServer\Connection
     */
    private $connection;

	/**
	 * @var DbPlayer[]
	 */
	public $dbPlayers = array();

    /**
     * @var \ManiaLive\Data\Storage
     */
    private $storage;

    /**
     * The version of the dedicated on which the system is running
     *
     * @var Version
     */
    public $version;

    /**
     * The simple title the environment of the track refers to
     *
     * @var String
     */
    public $simpleEnviTitle;

    /**
     * Is this server a relay server or a game server
     *
     * @var bool
     */
    public $isRelay;


    /**
     * @var RelayLink
     */
    public $relay;

    protected function __construct()
    {
        Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_BEGIN_MAP);

        $this->connection = Singletons::getInstance()->getDediConnection();

        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->version = $this->connection->getVersion();

        $this->isRelay = $this->connection->isRelayServer();

        $this->simpleEnviTitle = $this->getSimpleTitleByEnvironment($this->storage->currentMap->environnement);

        $this->relay = RelayLink::getInstance();
    }

    protected function getSimpleTitleByEnvironment($enviName){
        if($enviName == "Stadium" || $enviName == "Valley" || $enviName == "Canyon"){
            return self::TITLE_SIMPLE_TM;
        }else{
            return self::TITLE_SIMPLE_SM;
        }
    }

	/**
	 * @param $login
	 *
	 * @return DbPlayer|null
	 */
	public function getDbPlayer($login){
		return $this->dbPlayers[$login] == null ? null : $this->dbPlayers[$login];
	}

    /**
     * Method called when a map begin
     *
     * @param SMapInfo $map
     * @param bool     $warmUp
     * @param bool     $matchContinuation
     */
    function onBeginMap($map, $warmUp, $matchContinuation)
    {
    }
}