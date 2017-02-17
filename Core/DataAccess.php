<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLib\Utils\Singleton;
use ManiaLive\Application\Event as AppEvent;
use ManiaLive\Application\Listener;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;
use ManiaLive\Features\Tick\Listener as TickListener;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\Classes\Webaccess;
use ManiaLivePlugins\eXpansion\Core\Structures\HttpQuery;

/**
 * Description of DataStorage
 *
 * @author Reaby
 */
class DataAccess extends Singleton implements Listener, TickListener
{
    /** @var Webaccess */
    private $webaccess;
    // these are used for async webaccess
    private $read;
    private $write;
    private $except;
    private $tryTimer = -1;

    /** @var Classes\AsynchronousCurl */
    private $asyncCurl;

    public function __construct()
    {
        $this->read = array();
        $this->write = array();
        $this->except = array();
        $this->webaccess = new Webaccess();
        $this->asyncCurl = Classes\AsynchronousCurl::getInstance();
        $this->asyncCurl->start();
    }

    public function start()
    {
        Dispatcher::register(AppEvent::getClass(), $this);
    }

    public function onTick()
    {
        try {
            if ($this->tryTimer == -1) {
                $this->webaccess->select($this->read, $this->write, $this->except, 0, 0);
            }
        } catch (\Exception $e) {
            Console::println(
                "[DataAccess] OnTick Update failed: " . $e->getMessage()
                . "\n file " . $e->getFile() . ":" . $e->getLine()
            );
        }
    }

    public function __destruct()
    {
        $this->webaccess = null;
        Dispatcher::unregister(TickEvent::getClass(), $this);
    }

    /**
     * Asynchromous curl query
     *
     * Use this if you need to access for example https
     * note: This may block the main loop for short period of time.
     *
     * @param string $url
     * @param callable $callback
     * @param mixed $addionalData additional data passed for the query, like login, map-object, whatever
     * @param array $options curl options array
     */
    public function httpCurl($url, $callback, $addionalData = null, $options = array())
    {
        $this->asyncCurl->query($url, $callback, $addionalData, $options);
    }

    /**
     * Asynchromous httpGetter
     *
     *
     * use callback function like below: <br><br>
     * function xcallback(string $data, int $http_code, $callparams1, $callparams2...) {
     *
     * }
     *
     * @param string $url usage: http://www.example.com?param=value
     * @param array $callback usage: array($this, "xCallback")
     * @param array $callparams usage: array($param1, $param2)
     * @param string $userAgent userAgent to be sent
     * @param string $mimeType header mimetype request -> defaults to "text/html"
     *
     * @throws \Exception
     */
    final public function httpGet(
        $url,
        $callback,
        $callparams = array(),
        $userAgent = "ManiaLive - eXpansionPluginPack",
        $mimeType = "text/html"
    )
    {
        $this->_get(new HttpQuery($url, $callback, $callparams, $userAgent, $mimeType));
    }

    private function _get(HttpQuery $query)
    {
        $this->webaccess->request(
            $query->baseurl . "?" . $query->params,
            array(array($this, "_process"), $query),
            $query->data,
            false,
            20,
            3,
            5,
            $query->userAgent,
            $query->mimeType
        );
    }

    /**
     * @param $url
     * @param null $data
     * @param $callback
     * @param array $callparams
     * @param string $userAgent
     * @param string $mimeType
     */
    final public function httpPost(
        $url,
        $data = null,
        $callback,
        $callparams = array(),
        $userAgent = "ManiaLive - eXpansionPluginPack",
        $mimeType = "application/json"
    )
    {

        $query = new HttpQuery($url, $callback, $callparams, $userAgent, $mimeType);
        $query->setData($data);
        $this->_get($query);
    }

    /** @todo make queue and process it onPostLoop
     * @param $filename
     * @param $data
     * @param bool $append
     * @return bool|int
     */
    final public function save($filename, $data, $append = false)
    {
        clearstatcache();
        if (!is_file($filename)) {
            if (!touch($filename)) {
                chmod($filename, 0755);

                return false;
            }
        }
        clearstatcache();
        if (is_writable($filename)) {
            try {
                if ($append === true) {
                    return file_put_contents($filename, $data, LOCK_EX | FILE_APPEND);
                }

                return file_put_contents($filename, $data, LOCK_EX);
            } catch (\Exception $e) {
                Console::println("File write exception:" . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    /** @todo make queue and process it onPostLoop
     * @param $filename
     * @return bool|string
     */
    final public function load($filename)
    {
        clearstatcache();
        if (!is_file($filename)) {
            return false;
        }
        if (is_readable($filename)) {
            try {
                return file_get_contents($filename);
            } catch (\Exception $e) {
                Console::println("File read exception:" . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    /**
     * @param $data
     * @param HttpQuery $query
     */
    public function _process($data, HttpQuery $query)
    {
        if (!is_callable($query->callback)) {
            Console::println("[DataAccess Error] Callback-function is not valid!");
            return;
        }

        if (array_key_exists("Error", $data)) {
            return;
        }

        if ($data['Code'] == 301) {
            Console::println("[DataAccess] webRequest to " . $query->baseurl . " is permanently moved.");
            $args = $query->callparams;
            array_unshift($args, null, $data['Code']);
            call_user_func_array($query->callback, $args);

            if (!isset($data['Headers']['location'][0])) {
                return;
            }
            // set new redirected address
            $query->baseurl = $data['Headers']['location'][0];

            $query->redirectCount++;

            if ($query->redirectCount < 3) {
                Console::println("[DataAccess] request redirection to " . $query->baseurl);
                $this->_get($query);
            } else {
                Console::println("[DataAccess] webRequest redirected more than 3 times, canceling request.");
            }

            return;
        }
        // moved temporarily
        if ($data['Code'] == 302) {
            Console::println("[DataAccess] webRequest to " . $query->baseurl . " is temporarily moved.");

            $args = $query->callparams;
            array_unshift($args, null, $data['Code']);
            call_user_func_array($query->callback, $args);
            if (!isset($data['Headers']['location'][0])) {
                return;
            }
            // set new redirected address
            $query->baseurl = $data['Headers']['location'][0];
            $query->redirectCount++;
            if ($query->redirectCount < 3) {
                Console::println("[DataAccess] request redirection to " . $query->baseurl);
                $this->_get($query);
            } else {
                Console::println("[DataAccess] webRequest redirected more than 3 times, canceling request.");
            }

            return;
        }
        // access ok

        if ($data['Code'] == 200) {
            $outData = $data['Message'];

            $args = $query->callparams;
            array_unshift($args, $outData, $data['Code']);
            call_user_func_array($query->callback, $args);
        } else {
            $args = $query->callparams;
            array_unshift($args, null, $data['Code']);
            call_user_func_array($query->callback, $args);
        }
    }

    public function onInit()
    {

    }

    public function onPostLoop()
    {
        try {
            if ($this->tryTimer == -1) {
                $this->webaccess->select($this->read, $this->write, $this->except, 0, 0);
            } else {
                if (time() >= $this->tryTimer) {
                    Console::println("[DataAccess] Loop active again!");
                    $this->tryTimer = -1;
                }
            }
        } catch (\Exception $e) {
            Console::println(
                "[DataAccess] OnTick Update failed: " . $e->getMessage()
                . "\n file " . $e->getFile() . ":" . $e->getLine()
            );
            Console::println("[DataAccess] Recovering retry loop in 2 seconds...");
            $this->tryTimer = time() + 2;
        }
    }

    public function onPreLoop()
    {

    }

    public function onRun()
    {

    }

    public function onTerminate()
    {

    }
}
