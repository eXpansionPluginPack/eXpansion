<?php

namespace ManiaLivePlugins\eXpansion\Core\Classes;

////////////////////////////////////////////////////////////////
//¤
// File:      WEB ACCESS 2.1.3
// Date:      13.10.2011
// Author:    Gilles Masson
// Contributor & fixes: Xymph
// Additional changes for manialive: Reaby - updated at 20.3.2013
//
////////////////////////////////////////////////////////////////
// This class and functions can be used to make asynchronous xml or http (POST or GET) queries.
// this means that you call a function to send the query, and a callback function
// will automatically be called when the response has arrived, without having your
// program waiting for the response.
// You can also use it for synchronous queries (see below).
// The class handle (for each url) keepalive and compression (when possible).
// It support Cookies, and so can use sessions like php one (anyway the cookie is not stored,
// so its maximal life is the life of the program).
//
//
// usage:  $_webaccess = new Webaccess();
//         $_webaccess->request($url, array('func_name',xxx), $datas, $is_xmlrpc, $keepalive_min_timeout);
//    $url : the web script URL.
//    $datas : string to send in http body (xml, xml_rpc or POST data)
//    $is_xmlrpc : true if it's a xml or xml-rpc request, false if it's a standard html GET or POST
//    $keepalive_min_timeout : minimal value of server keepalive timeout to send a keepalive request,
//                             else make a request with close connection.
//    func_name is the callback function name, which will be called this way:
//       func_name(array('Code'=>code,'Reason'=>reason,'Headers'=>headers,'Message'=>message),xxx), where :
//           xxx is the same as given previously in callback description.
//           code is the returned http code
//             (http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6)
//           reason is the returned http reason
//             (http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6)
//           headers are the http headers of the reply
//           message is the returned text body
//
// IMPORTANT: to have this work, the main part of your program must include a
// $_webaccess->select() call peridically, which work exactly like stream_select().
// This is because the send and receive are asynchronous and will be completed later
// in the select() when datas are ready to be received and sent.
// This class can be use to make a synchronous query too. For such use null for the callback,
// so make the request this way:
//   $response = $_webaccess->request($url, null, $datas, $is_xmlrpc, $keepalive_min_timeout);
// where $response is an array('Code'=>code,'Reason'=>reason,'Headers'=>headers,'Message'=>message)
// like the one passed to the callback function of the asynchronous request.
// If you use only synchronous queries then there is no need to call select() as the function
// will return when the reply will be fully returned.
// If the connection itself fail, the array response will include a 'Error' string.
// other functions:
//   list($host,$port,$path) = getHostPortPath($url);
//   gzdecode() workaround
// will compress xmlrpc request ('never','accept','force','force-gzip','force-deflate')
// if set to 'accept' the first request will be made without, and the eventual 'Accept-Encoding'
// in reply will permit to decide if request compression can be used (and if gzip or deflate)
$_web_access_compress_xmlrpc_request = 'accept';

// will ask server for compressed reply (false, true)
// if true then will add a 'Accept-Encoding' header to tell the server to compress
// the reply if it support it.
$_web_access_compress_reply = true;


// keep alive connection ? else close it after the reply.
// unless false, first request will be with keepalive, to get server timeout and max values
// after timeout will be compared with the request $keepalive_min_timeout value to decide
// if keepalive have to be used or not. Note that apache2 timeout is short (about 15s).
// The classes will open, re-open or use existing connection as needed.
$_web_access_keepalive = true;
// timeout (s) without request before close, for keep alive
$_web_access_keepalive_timeout = 600;
// max requests before close, for keep alive
$_web_access_keepalive_max = 2000;


// for asynchrounous call, in case of error, timeout before retrying.
// it will be x2 for each error (on request or auto retry) until max,
// then stop automatic retry, and next request calls will return false.
// When stopped, a retry() or synchronous request will force a retry.
$_web_access_retry_timeout = 20;
$_web_access_retry_timeout_max = 5 * 60;


// use text/html with xmlrpc= , instead of of pure text/xml request (false, true)
// standard xml-rpc use pure text/xml request, where the xml is simply the body
// of the http request (and it's how the xml-rpc reply will be made). As a facility
// Dedimania support also to get the xml in a html GET or POST, where xmlrpc= will
// contain a urlsafe base64 of the xml. Default to false, so use pure text/xml.
$_web_access_post_xmlrpc = false;

// Note that in each request the text/xml or xmlrpc= will be used only if $is_xmlrpc
// is true. If false then the request will be a standard application/x-www-form-urlencoded 
// html GET or POST request ; in that case you have to build the url (GET) and/or
// body data (POST) yourself.


class Webaccess
{
    private $_WebaccessList;

    public function __construct()
    {
        $this->_WebaccessList = array();
        $this->WebaccessList = array();
    }

    public function request(
        $url,
        $callback,
        $datas,
        $is_xmlrpc = false,
        $keepalive_min_timeout = 300,
        $opentimeout = 3,
        $waittimeout = 5,
        $agent = 'XMLaccess',
        $mimeType = "text/html"
    ) {
        global $_web_access_keepalive;
        global $_web_access_keepalive_timeout;
        global $_web_access_keepalive_max;

        list($host, $port, $path) = getHostPortPath($url);

        if ($host === false) {
            print_r('*Webaccess request(): Bad url: ' . $url . "\n");
        } else {
            $server = $host . ':' . $port;
            // create object is needed
            if (!isset($this->_WebaccessList[$server]) || $this->_WebaccessList[$server] === null) {
                $this->_WebaccessList[$server] = new WebaccessUrl(
                    $this,
                    $host,
                    $port,
                    true,
                    600,
                    2000,
                    $agent,
                    $mimeType
                );
            }

            // increase the default timeout for sync/wait request
            if ($callback == null && $waittimeout == 5) {
                $waittimeout = 12;
            }

            // call request
            if ($this->_WebaccessList[$server] !== null) {
                $query = array('Path' => $path, 'Callback' => $callback, 'QueryDatas' => $datas,
                    'IsXmlrpc' => $is_xmlrpc, 'KeepaliveMinTimeout' => $keepalive_min_timeout,
                    'OpenTimeout' => $opentimeout, 'WaitTimeout' => $waittimeout,
                    'MimeType' => $mimeType);

                return $this->_WebaccessList[$server]->request($query);
            }
        }

        return false;
    }

    public function retry($url)
    {
        list($host, $port, $path) = getHostPortPath($url);
        if ($host === false) {
            print_r('*Webaccess retry(): Bad url: ' . $url . "\r");
        } else {
            $server = $host . ':' . $port;
            if (isset($this->_WebaccessList[$server])) {
                $this->_WebaccessList[$server]->retry();
            }
        }
    }

    public function select(&$read, &$write, &$except, $tv_sec, $tv_usec = 0)
    {
        $timeout = (int)($tv_sec * 1000000 + $tv_usec);
        if ($read == null) {
            $read = array();
        }
        if ($write == null) {
            $write = array();
        }
        if ($except == null) {
            $except = array();
        }

        $read = $this->_getWebaccessReadSockets($read);
        $write = $this->_getWebaccessWriteSockets($write);
        if (count($read) + count($write) + count($except) == 0) {
            // sleep the asked timeout...
            if ($timeout > 1000) {
                usleep($timeout);
            }

            return 0;
        }

        $utime = (int)(microtime(true) * 1000000);
        $nb = @stream_select($read, $write, $except, $tv_sec, $tv_usec);
        if ($nb === false) {
            // in case stream_select "forgot" to wait, sleep the remaining asked timeout...
            $dtime = (int)(microtime(true) * 1000000) - $utime;
            $timeout -= $dtime;
            if ($timeout > 1000) {
                usleep($timeout);
            }

            return false;
        }

        $this->_manageWebaccessSockets($read, $write, $except);

        // workaround for stream_select bug with amd64, replace $nb with sum of arrays
        return count($read) + count($write) + count($except);
    }

    private function _manageWebaccessSockets(&$receive, &$send, &$except)
    {
        // send pending datas on all webaccess sockets
        if (is_array($send) && count($send) > 0) {
            foreach ($send as $key => $socket) {
                $i = $this->_findWebaccessSocket($socket);
                if ($i !== false) {
                    if (isset($this->_WebaccessList[$i]->_spool[0]['State']) &&
                        $this->_WebaccessList[$i]->_spool[0]['State'] == 'OPEN'
                    ) {
                        $this->_WebaccessList[$i]->_open();
                    } else {
                        $this->_WebaccessList[$i]->_send();
                    }
                    unset($send[$key]);
                }
            }
        }

        // read datas from all needed webaccess
        if (is_array($receive) && count($receive) > 0) {
            foreach ($receive as $key => $socket) {
                $i = $this->_findWebaccessSocket($socket);
                if ($i !== false) {
                    $this->_WebaccessList[$i]->_receive();
                    unset($receive[$key]);
                }
            }
        }
    }

    private function _findWebaccessSocket($socket)
    {
        foreach ($this->_WebaccessList as $key => $wau) {
            if ($wau->_socket == $socket) {
                return $key;
            }
        }

        return false;
    }

    private function _getWebaccessReadSockets($socks)
    {
        foreach ($this->_WebaccessList as $key => $wau) {
            if ($wau->_state == 'OPENED' && $wau->_socket) {
                $socks[] = $wau->_socket;
            }
        }

        return $socks;
    }

    private function _getWebaccessWriteSockets($socks)
    {
        foreach ($this->_WebaccessList as $key => $wau) {

            if (isset($wau->_spool[0]['State']) &&
                ($wau->_spool[0]['State'] == 'OPEN' ||
                    $wau->_spool[0]['State'] == 'BAD' ||
                    $wau->_spool[0]['State'] == 'SEND')
            ) {
                //
                if (($wau->_state == 'CLOSED' || $wau->_state == 'BAD') && !$wau->_socket) {
                    $wau->_open();
                }

                if ($wau->_state == 'OPENED' && $wau->_socket) {
                    $socks[] = $wau->_socket;
                }
            }
        }

        return $socks;
    }

    function getAllSpools()
    {
        $num = 0;
        $bad = 0;
        foreach ($this->_WebaccessList as $key => $wau) {
            if ($wau->_state == 'OPENED' || $wau->_state == 'CLOSED') {
                $num += count($wau->_spool);
            } elseif ($wau->_state == 'BAD') {
                $bad += count($wau->_spool);
            }
        }

        return array($num, $bad);
    }
}

// usefull datas to handle received headers
$_wa_header_separator = array('cookie' => ';', 'set-cookie' => ';');
$_wa_header_multi = array('set-cookie' => true);

class WebaccessUrl
{

    //-----------------------------
    // Fields
    //-----------------------------

    public $wa;

    public $_host;

    public $_port;

    public $_compress_request;

    public $_socket;

    public $_state;

    public $_keepalive;

    public $_keepalive_timeout;

    public $_keepalive_max;

    public $_serv_keepalive_timeout;

    public $_serv_keepalive_max;

    public $_spool;

    public $_wait;

    public $_response;

    public $_query_num;

    public $_request_time;

    public $_cookies;

    public $_webaccess_str;

    public $_bad_time;

    public $_bad_timeout;

    public $_read_time;

    public $_agent;

    public $_mimeType;

    //-----------------------------
    // Methods
    //-----------------------------

    public function __construct(
        &$wa,
        $host,
        $port,
        $keepalive = true,
        $keepalive_timeout = 600,
        $keepalive_max = 300,
        $agent = 'XMLaccess',
        $mimeType = "text/html"
    ) {
        global $_web_access_compress_xmlrpc_request;
        global $_web_access_retry_timeout;
        $this->wa = &$wa;
        $this->_host = $host;
        $this->_port = $port;
        $this->_webaccess_str = 'Webaccess(' . $this->_host . ':' . $this->_port . '): ';
        $this->_agent = $agent;
        $this->_mimeType = $mimeType;

        // request compression setting
        if ($_web_access_compress_xmlrpc_request == 'accept') {
            $this->_compress_request = 'accept';
        } elseif ($_web_access_compress_xmlrpc_request == 'force') {
            if (function_exists('gzencode')) {
                $this->_compress_request = 'gzip';
            } elseif (function_exists('gzdeflate')) {
                $this->_compress_request = 'deflate';
            } else {
                $this->_compress_request = false;
            }
        } elseif ($_web_access_compress_xmlrpc_request == 'force-gzip' && function_exists('gzencode')) {
            $this->_compress_request = 'gzip';
        } elseif ($_web_access_compress_xmlrpc_request == 'force-deflate' && function_exists('gzdeflate')) {
            $this->_compress_request = 'deflate';
        } else {
            $this->_compress_request = false;
        }

        $this->_socket = null;
        $this->_state = 'CLOSED';
        $this->_keepalive = $keepalive;
        $this->_keepalive_timeout = $keepalive_timeout;
        $this->_keepalive_max = $keepalive_max;
        $this->_serv_keepalive_timeout = $keepalive_timeout;
        $this->_serv_keepalive_max = $keepalive_max;
        $this->_spool = array();
        $this->_wait = false;
        $this->_response = '';
        $this->_query_num = 0;
        $this->_query_time = time();
        $this->_cookies = array();
        $this->_bad_time = time();
        $this->_bad_timeout = 0;
        $this->_read_time = 0;
    }

    // put connection in BAD state
    public function _bad($errstr, $isbad = true)
    {
        global $_web_access_retry_timeout;
        print_r('*' . $this->_webaccess_str . $errstr . "\n");

        $this->infos();

        if ($this->_socket) {
            @fclose($this->_socket);
        }
        $this->_socket = null;

        if ($isbad) {
            if (isset($this->_spool[0]['State'])) {
                $this->_spool[0]['State'] = 'BAD';
            }
            $this->_state = 'BAD';

            $this->_bad_time = time();
            if ($this->_bad_timeout < $_web_access_retry_timeout) {
                $this->_bad_timeout = $_web_access_retry_timeout;
            } else {
                $this->_bad_timeout *= 2;
            }
        } else {
            if (isset($this->_spool[0]['State'])) {
                $this->_spool[0]['State'] = 'CLOSED';
            }
            $this->_state = 'CLOSED';
        }
        $this->_callCallback($this->_webaccess_str . $errstr);
    }

    public function retry()
    {
        global $_web_access_retry_timeout;
        if ($this->_state == 'BAD') {
            $this->_bad_time = time();
            $this->_bad_timeout = 0;
        }
    }

    //$query = array('Path'=>$path,'Callback'=>$callback, 'QueryDatas'=>$datas,
    //               'IsXmlrpc'=>$is_xmlrpc, 'KeepaliveMinTimeout'=>$keepalive_min_timeout,
    //               'OpenTimeout'=>$opentimeout, 'WaitTimeout'=>$waittimeout);
    // will add:     'State','HDatas','Datas','DatasSize',
    // will add:     'DatasSent','Response','ResponseSize','Headers','Close','Times'
    public function request(&$query)
    {
        global $_web_access_compress_reply;
        global $_web_access_post_xmlrpc;
        global $_web_access_retry_timeout;
        global $_web_access_retry_timeout_max;
        $query['State'] = 'BAD';
        $query['HDatas'] = '';
        $query['Datas'] = '';
        $query['DatasSize'] = 0;
        $query['DatasSent'] = 0;
        $query['Response'] = '';
        $query['ResponseSize'] = 0;
        $query['Headers'] = array();
        $query['Close'] = false;
        $query['Times'] = array(
            'open' => array(-1.0, -1.0),
            'send' => array(-1.0, -1.0),
            'receive' => array(-1.00, -1.0, 0)
        );


        // if asynch, in error, and maximal timeout, then forget the request and return false.
        if (($query['Callback'] != null) && ($this->_state == 'BAD')) {
            if ($this->_bad_timeout > $_web_access_retry_timeout_max) {
                print_r(
                    '*' . $this->_webaccess_str . 'Request refused for consecutive errors (' . $this->_bad_timeout
                    . " / " . $_web_access_retry_timeout_max . ")\n"
                );

                return false;
            } else {
                // if not max then accept the request
                // and try a request (minimum $_web_access_retry_timeout/2 after previous try)
                $time = time();
                $timeout = ($this->_bad_timeout / 2) - ($time - $this->_bad_time);
                if ($timeout < 0) {
                    $timeout = 0;
                }
                $this->_bad_time = $time - $this->_bad_timeout + $timeout;
            }
        }


        // build datas to send
        if (($query['Callback'] == null) || (is_array($query['Callback']) &&
                isset($query['Callback'][0]) &&
                is_callable($query['Callback'][0]))
        ) {

            if (is_string($query['QueryDatas']) && strlen($query['QueryDatas']) > 0) {
                $msg = "POST " . $query['Path'] . " HTTP/1.1\r\n";
                $msg .= "Host: " . $this->_host . "\r\n";
                $msg .= "User-Agent: " . $this->_agent . "\r\n";
                $msg .= "Cache-Control: no-cache\r\n";

                if ($_web_access_compress_reply) {
                    // ask compression of response if gzdecode() and/or gzinflate() is available
                    if (function_exists('gzdecode') && function_exists('gzinflate')) {
                        $msg .= "Accept-Encoding: deflate, gzip\r\n";
                    } elseif (function_exists('gzdecode')) {
                        $msg .= "Accept-Encoding: gzip\r\n";
                    } elseif (function_exists('gzinflate')) {
                        $msg .= "Accept-Encoding: deflate\r\n";
                    }
                }

                if ($query['IsXmlrpc'] === true) {
                    if ($_web_access_post_xmlrpc) {
                        $msg .= "Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
                        $query['QueryDatas'] = "xmlrpc=" . urlsafe_base64_encode($query['QueryDatas']);
                    } else {
                        $msg .= "Content-type: text/xml; charset=UTF-8\r\n";
                    }

                    if ($this->_compress_request == 'gzip' && function_exists('gzencode')) {
                        $msg .= "Content-Encoding: gzip\r\n";
                        $query['QueryDatas'] = gzencode($query['QueryDatas']);
                    } elseif ($this->_compress_request == 'deflate' && function_exists('gzdeflate')) {
                        $msg .= "Content-Encoding: deflate\r\n";
                        $query['QueryDatas'] = gzdeflate($query['QueryDatas']);
                    }
                } elseif (is_string($query['IsXmlrpc'])) {
                    $msg .= "Content-type: " . $query['IsXmlrpc'] . "\r\n";
                    $msg .= "Accept: */*\r\n";
                } else {
                    $msg .= "Content-type: " . $query['MimeType'] . "\r\n";
                    $msg .= "Accept: */*\r\n";
                }

                $msg .= "Content-length: " . strlen($query['QueryDatas']) . "\r\n";

                $query['HDatas'] = $msg;

                $query['State'] = 'OPEN';
                $query['Retries'] = 0;

                // add the query in spool
                $this->_spool[] = &$query;

                if ($query['Callback'] == null) {
                    $this->_wait = true;
                    $this->_open($query['OpenTimeout'], $query['WaitTimeout']); // wait more in not callback mode
                    $this->_spool = array();
                    $this->_wait = false;

                    return $query['Response'];
                } else {
                    $this->_open();
                }
            } else {
                $msg = "GET " . $query['Path'] . " HTTP/1.1\r\n";
                $msg .= "Host: " . $this->_host . "\r\n";
                $msg .= "User-Agent: " . $this->_agent . "\r\n";
                $msg .= "Cache-Control: no-cache\r\n";
                if ($_web_access_compress_reply) {
                    // ask compression of response if gzdecode() and/or gzinflate() is available
                    if (function_exists('gzdecode') && function_exists('gzinflate')) {
                        $msg .= "Accept-Encoding: deflate, gzip\r\n";
                    } elseif (function_exists('gzdecode')) {
                        $msg .= "Accept-Encoding: gzip\r\n";
                    } elseif (function_exists('gzinflate')) {
                        $msg .= "Accept-Encoding: deflate\r\n";
                    }
                }
                $msg .= "Content-type: " . $query['MimeType'] . "; charset=UTF-8\r\n";
                $msg .= "Content-length: " . strlen($query['QueryDatas']) . "\r\n";
                $query['HDatas'] = $msg;

                $query['State'] = 'OPEN';
                $query['Retries'] = 0;

                // add the query in spool
                $this->_spool[] = &$query;

                if ($query['Callback'] == null) {
                    $this->_wait = true;
                    $this->_open($query['OpenTimeout'], $query['WaitTimeout']); // wait more in not callback mode
                    $this->_spool = array();
                    $this->_wait = false;

                    return $query['Response'];
                } else {
                    $this->_open();
                }
            }
        } else {


            print '*' . $this->_webaccess_str . 'Bad callback function: ' . $query['Callback'];

            return false;
        }

        return true;
    }

    // open the socket (close it before if needed)
    private function _open_socket($opentimeout = 0.0)
    {
        // if socket not opened, then open it (2 tries)
        if (!$this->_socket || $this->_state != 'OPENED') {
            $time = microtime(true);
            $this->_spool[0]['Times']['open'][0] = $time;

            $errno = '';
            $errstr = '';
            $this->_socket = @fsockopen($this->_host, $this->_port, $errno, $errstr, 1.8); // first try
            if (!$this->_socket) {

                if ($opentimeout >= 1.0) {
                    $this->_socket = @fsockopen($this->_host, $this->_port, $errno, $errstr, $opentimeout);
                }
                if (!$this->_socket) {
                    $this->_bad('Error(' . $errno . ')' . $errstr . ', connection failed!');

                    return;
                }
            }
            $this->_state = 'OPENED';
            // new socket connection : reset all pending request original values
            for ($i = 0; $i < count($this->_spool); $i++) {
                $this->_spool[$i]['State'] = 'OPEN';
                $this->_spool[$i]['DatasSent'] = 0;
                $this->_spool[$i]['Response'] = '';
                $this->_spool[$i]['Headers'] = array();
            }
            $this->_response = '';
            $this->_query_num = 0;
            $this->_query_time = time();
            $time = microtime(true);
            $this->_spool[0]['Times']['open'][1] = $time - $this->_spool[0]['Times']['open'][0];
        }
    }

    // open the connection (if not already opened) and send
    public function _open($opentimeout = 0.0, $waittimeout = 5.0)
    {
        global $_web_access_retry_timeout_max;

        if (!isset($this->_spool[0]['State'])) {
            return false;
        }
        $time = time();

        // if asynch, in error, then return false until timeout or if >max)
        if (!$this->_wait && $this->_state == 'BAD' &&
            (($this->_bad_timeout > $_web_access_retry_timeout_max) ||
                (($time - $this->_bad_time) < $this->_bad_timeout))
        ) {
            return false;
        }

        // if the socket is probably in timeout, close it
        if ($this->_socket && $this->_state == 'OPENED' &&
            ($this->_serv_keepalive_timeout <= ($time - $this->_query_time))
        ) {
            $this->_state = 'CLOSED';
            @fclose($this->_socket);
            $this->_socket = null;
        }

        // if socket is not opened, open it
        if (!$this->_socket || $this->_state != 'OPENED') {
            $this->_open_socket($opentimeout);
        }

        // if socket is open, send data if possible
        if ($this->_socket) {
            $this->_read_time = microtime(true);

            // if wait (synchronous query) then go on all pending write/read until the last
            if ($this->_wait) {
                @stream_set_timeout($this->_socket, 0, 10000);

                while (isset($this->_spool[0]['State']) &&
                    ($this->_spool[0]['State'] == 'OPEN' ||
                        $this->_spool[0]['State'] == 'SEND' ||
                        $this->_spool[0]['State'] == 'RECEIVE')) {
                    if (!$this->_socket || $this->_state != 'OPENED') {
                        $this->_open_socket($opentimeout);
                    }

                    if ($this->_spool[0]['State'] == 'OPEN') {
                        $time = microtime(true);
                        $this->_spool[0]['Times']['send'][0] = $time;
                        $this->_send($waittimeout);
                    } elseif ($this->_spool[0]['State'] == 'SEND') {
                        $this->_send($waittimeout);
                    } elseif ($this->_spool[0]['State'] == 'RECEIVE') {
                        $this->_receive($waittimeout * 4);
                    }

                    // if timeout then error
                    if (($difftime = round(microtime(true) - $this->_read_time)) > $waittimeout) {
                        $this->_bad(
                            "Request timeout, in _open ({$difftime} > {$waittimeout}s) state="
                            . $this->_spool[0]['State']
                        );

                        return;
                    }
                }
                if ($this->_socket) {
                    @stream_set_timeout($this->_socket, 0, 2000);
                }
            } elseif (isset($this->_spool[0]['State'])
                && $this->_spool[0]['State'] == 'OPEN'
            ) { // else just do a send on the current
                @stream_set_timeout($this->_socket, 0, 2000);
                $this->_send($waittimeout);
            }
        }
    }

    public function _send($waittimeout = 20)
    {
        if (!isset($this->_spool[0]['State'])) {
            return;
        }
        $errno = '';
        $errstr = '';

        // if OPEN then become SEND
        if ($this->_spool[0]['State'] == 'OPEN') {

            $this->_spool[0]['State'] = 'SEND';
            $time = microtime(true);
            $this->_spool[0]['Times']['send'][0] = $time;
            $this->_spool[0]['Response'] = '';
            $this->_spool[0]['Headers'] = array();

            // finish to prepare header and data to send
            $msg = $this->_spool[0]['HDatas'];
            if (!$this->_keepalive || ($this->_spool[0]['KeepaliveMinTimeout'] < 0) ||
                ($this->_serv_keepalive_timeout < $this->_spool[0]['KeepaliveMinTimeout']) ||
                ($this->_serv_keepalive_max <= ($this->_query_num + 2)) ||
                ($this->_serv_keepalive_timeout <= (time() - $this->_query_time + 2))
            ) {
                $msg .= "Connection: close\r\n";
                $this->_spool[0]['Close'] = true;
            } else {
                $msg .= "Keep-Alive: timeout=" . $this->_keepalive_timeout . ', max=' . $this->_keepalive_max
                    . "\r\nConnection: Keep-Alive\r\n";
            }

            // add cookie header
            if (count($this->_cookies) > 0) {
                $cookie_msg = '';
                $sep = '';
                foreach ($this->_cookies as $name => $cookie) {
                    if (!isset($cookie['path'])
                        || strncmp($this->_spool[0]['Path'], $cookie['path'], strlen($cookie['path'])) == 0
                    ) {
                        $cookie_msg .= $sep . $name . '=' . $cookie['Value'];
                        $sep = '; ';
                    }
                }
                if ($cookie_msg != '') {
                    $msg .= "Cookie: $cookie_msg\r\n";
                }
            }

            $msg .= "\r\n";
            $msg .= $this->_spool[0]['QueryDatas'];
            $this->_spool[0]['Datas'] = $msg;
            $this->_spool[0]['DatasSize'] = strlen($msg);
            $this->_spool[0]['DatasSent'] = 0;
        }

        // if not SEND then stop
        if ($this->_spool[0]['State'] != 'SEND') {
            return;
        }

        do {
            $sent = @stream_socket_sendto(
                $this->_socket,
                substr(
                    $this->_spool[0]['Datas'],
                    $this->_spool[0]['DatasSent'],
                    ($this->_spool[0]['DatasSize'] - $this->_spool[0]['DatasSent'])
                )
            );

            if ($sent == false) {

                $time = microtime(true);
                $this->_spool[0]['Times']['send'][1] = $time - $this->_spool[0]['Times']['send'][0];
                $this->_bad(
                    'Error(' . $errno . ')' . $errstr . ', could not send datas ! ('
                    . $sent . ' / ' . ($this->_spool[0]['DatasSize'] - $this->_spool[0]['DatasSent']) . ' , '
                    . $this->_spool[0]['DatasSent'] . ' / ' . $this->_spool[0]['DatasSize'] . ')'
                );
                if ($this->_wait) {
                    return;
                }
                break;
            } else {
                $this->_spool[0]['DatasSent'] += $sent;
                if ($this->_spool[0]['DatasSent'] >= $this->_spool[0]['DatasSize']) {
                    // All is sent, prepare to receive the reply
                    $this->_query_num++;
                    $this->_query_time = time();

                    $time = microtime(true);
                    $this->_spool[0]['Times']['send'][1] = $time - $this->_spool[0]['Times']['send'][0];

                    $this->_spool[0]['State'] = 'RECEIVE';
                    $this->_spool[0]['Times']['receive'][0] = $time;
                } elseif (($difftime = round(microtime(true) - $this->_read_time)) > $waittimeout) {
                    // if timeout then error
                    $this->_bad(
                        "Request timeout, in _send ({$difftime} > {$waittimeout}s) state=" . $this->_spool[0]['State']
                    );
                    break;
                }
            }

            // if not async-callback then continue until all is sent
        } while ($this->_wait && isset($this->_spool[0]['State']) && ($this->_spool[0]['State'] == 'SEND'));
    }

    public function _receive($waittimeout = 40)
    {
        global $_Webaccess_last_response;

        if (!$this->_socket || $this->_state != 'OPENED') {
            return;
        }

        $errno = '';
        $errstr = '';
        $state = false;
        $time0 = microtime(true);
        $timeout = ($this->_wait) ? $waittimeout : 0;
        do {
            $r = array($this->_socket);
            $w = null;
            $e = null;
            $nb = @stream_select($r, $w, $e, $timeout);
            if ($nb === 0) {
                $nb = count($r);
            }

            while (!@feof($this->_socket) && $nb !== false && $nb > 0) {
                $timeout = 0;

                if (count($r) > 0) {
                    $res = @stream_socket_recvfrom($this->_socket, 8192);

                    if ($res == '') { // should not happen habitually, but...
                        break;
                    } elseif ($res !== false) {
                        $this->_response .= $res;
                    } else {
                        if (isset($this->_spool[0])) {
                            $time = microtime(true);
                            $this->_spool[0]['Times']['receive'][1] = $time - $this->_spool[0]['Times']['receive'][0];
                        }
                        $this->_bad('Error(' . $errno . ')' . $errstr . ', could not read all datas !');

                        return;
                    }
                }

                // if timeout then error
                if (($difftime = round(microtime(true) - $this->_read_time)) > $waittimeout) {
                    $this->_bad("Request timeout, in _receive ({$difftime} > {$waittimeout}s)");
                    break;
                }

                $r = array($this->_socket);
                $w = null;
                $e = null;
                $nb = @stream_select($r, $w, $e, $timeout);
                if ($nb === 0) {
                    $nb = count($r);
                }
            }

            if (isset($this->_spool[0]['Times']['receive'][2])) {
                $time = microtime(true);
                $this->_spool[0]['Times']['receive'][2] += ($time - $time0);
            }

            // get headers and full message
            $state = $this->_handleHeaders();
        } while ($this->_wait && $state === false && $this->_socket && !@feof($this->_socket));

        if (!isset($this->_spool[0]['State']) || $this->_spool[0]['State'] != 'RECEIVE') {
            // in case of (probably keepalive) connection closed by server
            if ($this->_socket && @feof($this->_socket)) {
                $this->_state = 'CLOSED';
                @fclose($this->_socket);
                $this->_socket = null;
            }

            return;
        }


        // terminated but incomplete ! more than probably closed by server...
        if ($state === false && $this->_socket && @feof($this->_socket)) {
            $this->_state = 'CLOSED';
            if (isset($this->_spool[0])) {
                $time = microtime(true);
                $this->_spool[0]['State'] = 'OPEN';
                $this->_spool[0]['Times']['receive'][1] = $time - $this->_spool[0]['Times']['receive'][0];
            }
            // if not 0 sized then show error message
            if (strlen($this->_response) > 0) {
                $this->_bad(
                    'Error: closed with incomplete read : re-open socket and re-send ! (' . strlen(
                        $this->_response
                    ) . ')'
                );
            } else {
                $this->_bad(
                    'Closed by server when reading : re-open socket and re-send ! (' . strlen($this->_response) . ')',
                    false
                );
            }

            $this->_spool[0]['Retries']++;
            if ($this->_spool[0]['Retries'] > 2) {
                // 3 tries failed, remode entry from spool
                print_r(
                    '*' . $this->_webaccess_str
                    . " Failed {$this->_spool[0]['Retries']} times : skip current request.\n"
                );
                array_shift($this->_spool);
            }

            return;
        }


        // reply is complete  :)
        if ($state === true) {
            $this->_bad_timeout = 0; // reset error timeout

            $this->_spool[0]['Times']['receive'][1] = $time - $this->_spool[0]['Times']['receive'][0];
            $this->_spool[0]['State'] = 'DONE';

            // store http/xml response in global $_Webaccess_last_response for debugging use
            $_Webaccess_last_response = $this->_spool[0]['Response'];
            // call callback func
            $this->_callCallback();


            $this->_query_time = time();

            if (!$this->_keepalive || $this->_spool[0]['Close']) {
                $this->_state = 'CLOSED';
                @fclose($this->_socket);
                $this->_socket = null;
            }

            $this->infos();

            // request completed, remove it from spool !
            array_shift($this->_spool);
        }
    }

    private function _callCallback($error = null)
    {
        if ($error !== null) {
            $this->_spool[0]['Response']['Error'] = $error;
        }
        // call callback func
        if (isset($this->_spool[0]['Callback'])) {
            $callbackinfo = $this->_spool[0]['Callback'];
            if (isset($callbackinfo[0]) && is_callable($callbackinfo[0])) {
                $callback_func = $callbackinfo[0];
                $callbackinfo[0] = $this->_spool[0]['Response'];
                call_user_func_array($callback_func, $callbackinfo);
            }
        }
    }

    private function _handleHeaders()
    {
        global $_wa_header_separator, $_wa_header_multi;

        if (!isset($this->_spool[0]['State'])) {
            return false;
        }

        // not enough data, continue read
        if (strlen($this->_response) < 8) {
            return false;
        }
        if (strncmp($this->_response, 'HTTP/', 5) != 0) { // not HTTP !
            $this->_bad(
                "Error, not HTTP response ! **********\n" . substr($this->_response, 0, 300) . "\n***************\n"
            );

            return null;
        }

        // separate headers and datas
        $datas = explode("\r\n\r\n", $this->_response, 2);
        if (count($datas) < 2) {
            $datas = explode("\n\n", $this->_response, 2);
            if (count($datas) < 2) {
                $datas = explode("\r\r", $this->_response, 2);
                if (count($datas) < 2) {
                    return false;
                } // not complete headers, continue read
            }
        }

        // get headers if not done on previous read
        if (!isset($this->_spool[0]['Headers']['Command'][0])) {
            // separate headers

            $headers = array();
            $heads = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $datas[0])));
            if (count($heads) < 2) {
                $this->_bad("Error, uncomplete headers ! **********\n" . $datas[0] . "\n***************\n");

                return null;
            }

            $headers['Command'] = explode(' ', $heads[0], 3);

            for ($i = 1; $i < count($heads); $i++) {
                $header = explode(':', $heads[$i], 2);
                if (count($header) > 1) {
                    $headername = strtolower(trim($header[0]));
                    if (isset($_wa_header_separator[$headername])) {
                        $sep = $_wa_header_separator[$headername];
                    } else {
                        $sep = ',';
                    }
                    if (isset($_wa_header_multi[$headername]) && $_wa_header_multi[$headername]) {
                        if (!isset($headers[$headername])) {
                            $headers[$headername] = array();
                        }
                        $headers[$headername][] = explode($sep, trim($header[1]));
                    } else {
                        $headers[$headername] = explode($sep, trim($header[1]));
                    }
                }
            }

            if (isset($headers['content-length'][0])) {
                $headers['content-length'][0] += 0;
            } //convert to int

            $this->_spool[0]['Headers'] = $headers;

            // add header specific info in case of Dedimania reply
            if (isset($headers['server'][0])) {
                $this->_webaccess_str = 'Webaccess(' . $this->_host . ':' . $this->_port . '/'
                    . $headers['server'][0] . '): ';
            }
        } else {
            $headers = &$this->_spool[0]['Headers'];
        }


        // get real message
        $datasize = strlen($datas[1]);
        if (isset($headers['content-length'][0]) && $headers['content-length'][0] >= 0) {

            // incomplete message
            if ($headers['content-length'][0] > $datasize) {
                return false;
            } elseif ($headers['content-length'][0] < $datasize) {
                $message = substr($datas[1], 0, $headers['content-length'][0]);
                // remaining buffer for next reply
                $this->_response = substr($datas[1], $headers['content-length'][0]);
            } else {
                $message = $datas[1];
                $this->_response = '';
            }
            $this->_spool[0]['ResponseSize'] = strlen($datas[0]) + 4 + $headers['content-length'][0];
        } elseif (isset($headers['transfer-encoding'][0])
            && $headers['transfer-encoding'][0] == 'chunked'
        ) {  // get real message when reply is chunked

            // get chunk size and make message with chunks datas
            $size = -1;
            $chunkpos = 0;
            if (($datapos = strpos($datas[1], "\r\n", $chunkpos)) !== false) {
                $message = '';
                $chunk = explode(';', substr($datas[1], $chunkpos, $datapos - $chunkpos));
                $size = hexdec($chunk[0]);
                while ($size > 0) {
                    // incomplete message
                    if ($datapos + 2 + $size > $datasize) {
                        return false;
                    }
                    $message .= substr($datas[1], $datapos + 2, $size);
                    $chunkpos = $datapos + 2 + $size + 2;
                    if (($datapos = strpos($datas[1], "\r\n", $chunkpos)) !== false) {
                        $chunk = explode(';', substr($datas[1], $chunkpos, $datapos - $chunkpos));
                        $size = hexdec($chunk[0]);
                    } else {
                        $size = -1;
                    }
                }
            }
            // error bad size or incomplete message
            if ($size < 0) {
                return false;
            }

            // incomplete message : end is missing
            if (strpos($datas[1], "\r\n\r\n", $chunkpos) === false) {
                return false;
            }

            // store complete message size
            $msize = strlen($message);
            // add message size after 'chunked' for information
            $headers['transfer-encoding'][1] = 'total_size=' . $msize;
            $this->_spool[0]['ResponseSize'] = strlen($datas[0]) + 4 + $msize;

            // after the message itself...
            $message_end = explode("\r\n\r\n", substr($datas[1], $chunkpos), 2);

            // add end headers if any
            $heads = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $message_end[0])));
            for ($i = 1; $i < count($heads); $i++) {
                $header = explode(':', $heads[$i], 2);
                if (count($header) > 1) {
                    $headername = strtolower(trim($header[0]));
                    if (isset($_wa_header_separator[$headername])) {
                        $sep = $_wa_header_separator[$headername];
                    } else {
                        $sep = ',';
                    }
                    if (isset($_wa_header_multi[$headername]) && $_wa_header_multi[$headername]) {
                        if (!isset($headers[$headername])) {
                            $headers[$headername] = array();
                        }
                        $headers[$headername][] = explode($sep, trim($header[1]));
                    } else {
                        $headers[$headername] = explode($sep, trim($header[1]));
                    }
                }
            }
            $this->_spool[0]['Headers'] = $headers;

            // remaining buffer for next reply
            if (isset($message_end[1]) && strlen($message_end[1]) > 0) {
                $this->_response = $message_end[1];
            } else {
                $this->_response = '';
            }
        } else {  // no content-length and not chunked !
            $this->_bad(
                "Error, bad http, no content-length and not chunked ! **********\n" . $datas[0] . "\n***************\n"
            );

            return null;
        }

        // if Content-Encoding: gzip  or  Content-Encoding: deflate
        if (isset($headers['content-encoding'][0])) {
            if ($headers['content-encoding'][0] == 'gzip') {
                $message = @gzdecode($message);
            } elseif ($headers['content-encoding'][0] == 'deflate') {
                $message = @gzinflate($message);
            }
        }

        // if Accept-Encoding: gzip or deflate
        if ($this->_compress_request == 'accept' && isset($headers['accept-encoding'][0])) {
            foreach ($headers['accept-encoding'] as $comp) {
                $comp = trim($comp);
                if ($comp == 'gzip' && function_exists('gzencode')) {
                    $this->_compress_request = 'gzip';
                    break;
                } elseif ($comp == 'deflate' && function_exists('gzdeflate')) {
                    $this->_compress_request = 'deflate';
                    break;
                }
            }
            if ($this->_compress_request == 'accept') {
                $this->_compress_request = false;
            }
        }

        // get cookies values
        if (isset($headers['set-cookie'])) {
            foreach ($headers['set-cookie'] as $cookie) {
                $cook = explode('=', $cookie[0], 2);
                if (count($cook) > 1) {
                    // set main cookie value
                    $cookname = trim($cook[0]);
                    if (!isset($this->_cookies[$cookname])) {
                        $this->_cookies[$cookname] = array();
                    }
                    $this->_cookies[$cookname]['Value'] = trim($cook[1]);

                    // set cookie options
                    for ($i = 1; $i < count($cookie); $i++) {
                        $cook = explode('=', $cookie[$i], 2);
                        $cookarg = strtolower(trim($cook[0]));
                        if (isset($cook[1])) {
                            $this->_cookies[$cookname][$cookarg] = trim($cook[1]);
                        }
                    }
                }
            }
        }

        // if the server reply ask to close, then close
        if (!isset($headers['connection'][0]) || $headers['connection'][0] == 'close') {
            $this->_spool[0]['Close'] = true;
        }

        // verify server keepalive value and use them if lower
        if (isset($headers['keep-alive'])) {
            $kasize = count($headers['keep-alive']);
            for ($i = 0; $i < $kasize; $i++) {
                $keep = explode('=', $headers['keep-alive'][$i], 2);
                if (count($keep) > 1) {
                    $headers['keep-alive'][trim(strtolower($keep[0]))] = 0 + trim($keep[1]);
                }
            }
            if (isset($headers['keep-alive']['timeout'])) {
                $this->_serv_keepalive_timeout = $headers['keep-alive']['timeout'];
            }
            if (isset($headers['keep-alive']['max'])) {
                $this->_serv_keepalive_max = $headers['keep-alive']['max'];
            }
        }

        // store complete reply message for the request
        $this->_spool[0]['Response'] = array('Code' => 0 + $headers['Command'][1],
            'Reason' => $headers['Command'][2],
            'Headers' => $headers,
            'Message' => $message);

        return true;
    }

    public function infos()
    {
        try {
            $size = (isset($this->_spool[0]['Response']['Message'])) ? strlen(
                $this->_spool[0]['Response']['Message']
            ) : 0;
            $msg = $this->_webaccess_str
                . sprintf(
                    "[%s,%s]: %0.3f / %0.3f / %0.3f (%0.3f) / %d [%d,%d,%d]",
                    $this->_state,
                    $this->_spool[0]['State'],
                    $this->_spool[0]['Times']['open'][1],
                    $this->_spool[0]['Times']['send'][1],
                    $this->_spool[0]['Times']['receive'][1],
                    $this->_spool[0]['Times']['receive'][2],
                    $this->_query_num,
                    $this->_spool[0]['DatasSize'],
                    $size,
                    $this->_spool[0]['ResponseSize']
                );
        } catch (Exception $e) {
            print "Exception at infos:" . $e->getMessage();
        }
    }
}

// use: list($host,$port,$path) = getHostPortPath($url);
function getHostPortPath($url)
{
    $http_pos = strpos($url, 'http://');
    if ($http_pos !== false) {
        $script = explode('/', substr($url, $http_pos + 7), 2);
        if (isset($script[1])) {
            $path = '/' . $script[1];
        } else {
            $path = '/';
        }
        $serv = explode(':', $script[0], 2);
        $host = $serv[0];
        if (isset($serv[1])) {
            $port = 0 + $serv[1];
        } else {
            $port = 80;
        }
        if (strlen($host) > 2) {
            return array($host, $port, $path);
        }
    }

    return array(false, false, false);
}

// gzdecode() workaround
if (!function_exists('gzdecode') && function_exists('gzinflate')) {

    function gzdecode($data)
    {
        $len = strlen($data);
        if ($len < 18 || strcmp(substr($data, 0, 2), "\x1f\x8b")) {
            return null; // Not GZIP format (See RFC 1952)
        }
        $method = ord(substr($data, 2, 1)); // Compression method
        $flags = ord(substr($data, 3, 1)); // Flags
        if ($flags & 31 != $flags) {
            // Reserved bits are set -- NOT ALLOWED by RFC 1952
            return null;
        }
        // NOTE: $mtime may be negative (PHP integer limitations)
        $mtime = unpack("V", substr($data, 4, 4));
        $mtime = $mtime[1];
        $xfl = substr($data, 8, 1);
        $os = substr($data, 8, 1);
        $headerlen = 10;
        $extralen = 0;
        $extra = "";
        if ($flags & 4) {
            // 2-byte length prefixed EXTRA data in header
            if (($len - $headerlen - 2) < 8) {
                return false; // Invalid format
            }
            $extralen = unpack("v", substr($data, 8, 2));
            $extralen = $extralen[1];
            if (($len - $headerlen - 2 - $extralen) < 8) {
                return false; // Invalid format
            }
            $extra = substr($data, 10, $extralen);
            $headerlen += 2 + $extralen;
        }

        $filenamelen = 0;
        $filename = "";
        if ($flags & 8) {
            // C-style string file NAME data in header
            if (($len - $headerlen - 1) < 8) {
                return false; // Invalid format
            }
            $filenamelen = strpos(substr($data, 8 + $extralen), chr(0));
            if ($filenamelen === false || ($len - $headerlen - $filenamelen - 1) < 8) {
                return false; // Invalid format
            }
            $filename = substr($data, $headerlen, $filenamelen);
            $headerlen += $filenamelen + 1;
        }

        $commentlen = 0;
        $comment = "";
        if ($flags & 16) {
            // C-style string COMMENT data in header
            if (($len - $headerlen - 1) < 8) {
                return false; // Invalid format
            }
            $commentlen = strpos(substr($data, 8 + $extralen + $filenamelen), chr(0));
            if ($commentlen === false || ($len - $headerlen - $commentlen - 1) < 8) {
                return false; // Invalid header format
            }
            $comment = substr($data, $headerlen, $commentlen);
            $headerlen += $commentlen + 1;
        }

        $headercrc = "";
        if ($flags & 1) {
            // 2-bytes (lowest order) of CRC32 on header present
            if (($len - $headerlen - 2) < 8) {
                return false; // Invalid format
            }
            $calccrc = crc32(substr($data, 0, $headerlen)) & 0xffff;
            $headercrc = unpack("v", substr($data, $headerlen, 2));
            $headercrc = $headercrc[1];
            if ($headercrc != $calccrc) {
                return false; // Bad header CRC
            }
            $headerlen += 2;
        }

        // GZIP FOOTER - These be negative due to PHP's limitations
        $datacrc = unpack("V", substr($data, -8, 4));
        $datacrc = $datacrc[1];
        $isize = unpack("V", substr($data, -4));
        $isize = $isize[1];

        // Perform the decompression:
        $bodylen = $len - $headerlen - 8;
        if ($bodylen < 1) {
            // This should never happen - IMPLEMENTATION BUG!
            return null;
        }
        $body = substr($data, $headerlen, $bodylen);
        $data = "";
        if ($bodylen > 0) {
            switch ($method) {
                case 8:
                    // Currently the only supported compression method:
                    $data = gzinflate($body);
                    break;
                default:
                    // Unknown compression method
                    return false;
            }
        } else {
            // I'm not sure if zero-byte body content is allowed.
            // Allow it for now...  Do nothing...
        }

        // Verifiy decompressed size and CRC32:
        // NOTE: This may fail with large data sizes depending on how
        //      PHP's integer limitations affect strlen() since $isize
        //      may be negative for large sizes.
        if ($isize != strlen($data) || crc32($data) != $datacrc) {
            // Bad format!  Length or CRC doesn't match!
            return false;
        }

        return $data;
    }
}

function urlsafe_base64_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_,');
}
