<?php
/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
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

namespace ManiaLivePlugins\eXpansion\Core\Classes;

use ManiaLive\Application\Event as AppEvent;
use ManiaLive\Data\Storage;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Core\types\AsynchronousCurlData;
use oliverde8\AsynchronousJobs\Job\CallbackCurl;
use oliverde8\AsynchronousJobs\JobRunner;

class AsynchronousCurl extends \ManiaLib\Utils\Singleton implements \ManiaLive\Application\Listener, \ManiaLive\Features\Tick\Listener
{
    protected $handle;

    /** @var AsynchronousCurlData[] */
    protected $_queries = array();

    public function start()
    {
        Dispatcher::register(AppEvent::getClass(), $this);
        /** @var Storage $storage */
        $storage = Storage::getInstance();
        JobRunner::getInstance($storage->serverLogin, 'php', 'tmp/asnychronous/');
    }

    /**
     * make a http query with options
     *
     * @param string $url
     * @param callable $callback
     * @param mixed $addionalData if you need to pass additional metadata with the query, like login do it here
     * @param array $options curl options array
     */
    public function query($url, $callback, $additionalData = null, $options = array())
    {
        $curlJob = new CallbackCurl();
        $curlJob->setCallback($callback);
        $curlJob->setUrl($url);

        $options = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => "eXpansionPluginPack v " . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION,
                ) + $options;

        $curlJob->setOptions($options);
        $curlJob->__additionalData = $additionalData;

        $curlJob->start();
    }

    /**
     * Event launch every seconds
     */
    public function onTick()
    {
        JobRunner::getInstance()->proccess();
    }

    public function onInit()
    {

    }

    public function onRun()
    {

    }

    public function onPreLoop()
    {

    }

    public function onPostLoop()
    {
        JobRunner::getInstance()->proccess();
    }

    public function onTerminate()
    {
        curl_multi_close($this->handle);
    }
}
