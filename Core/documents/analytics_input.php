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

require 'vendor/autoload.php';
$client = new \Elasticsearch\Client();

$time = time() - 3600;

$key = getData($_GET, 'key', '');

const INTERVAL = 600;

$settings = array(
    'number_of_shrads' => 1,
    'number_of_replicas' => 1
);

if (getData($_GET, 'page') == 'ping' && $key != "" && canSendData($key, $time)) {

    $index = getIndexToUse($time, "expansion-ping");

    $params['body'] = array(
        'nbPlayers' => (int) getData($_GET, 'nbPlayers', 0),
        'key' => getData($_GET, 'key', "invalid-".rand(100000,1000000)),
        '@timestamp' => getCurrentTimeStamp($time),
        'country' => getData($_GET, 'country', ""),
        'version' => getData($_GET, 'version', "invalid"),
        'memory' => (int) getData($_GET, 'memory', 0),
        'memory_peak' => (int) getData($_GET, 'memory_peak', 0),
        'php_version' => getData($_GET, 'php_version', "unknown"),
        'php_version_short' => getData($_GET, 'php_version_short', implode('.', array_slice(explode('.', getData($_GET, 'php_version', 'unknown')), 0, 2))),
        'mysql_version' => getData($_GET, 'mysql_version', 'unknown'),
        'build' => getData($_GET, 'build', "invalid"),
        'game' => getData($_GET, 'game', "invalid"),
        'title' => getData($_GET, 'title', "invalid"),
        'mode' => getData($_GET, 'mode', "invalid"),
        'plugins'=> explode(',', getData($_GET, 'plugins', ""))
    );
    $params['index'] = $index;
    $params['type'] = 'servers';
    //$params['settings']['index'] = $settings;

    $ret = $client->index($params);
    }else if (getData($_GET, 'page') == 'error' && $key != "" && canSendData($key, $time, false) && getData($_GET, 'error_file', false)) {

    $index = getIndexToUse($time, "expansion-error");

    $params['body'] = array(
        'nbPlayers' => (int) getData($_GET, 'nbPlayers', 0),
        'key' => getData($_GET, 'key', "invalid-".rand(100000,1000000)),
        '@timestamp' => date('Y-m-d',($time)).'T'.date('H:i:s', ($time)).'Z',
        'country' => getData($_GET, 'country', ""),
        'version' => getData($_GET, 'version', "invalid"),
        'memory' => (int) getData($_GET, 'memory', 0),
        'memory_peak' => (int) getData($_GET, 'memory_peak', 0),
        'php_version' => getData($_GET, 'php_version', "unknown"),
        'build' => getData($_GET, 'build', "invalid"),
        'game' => getData($_GET, 'game', "invalid"),
        'title' => getData($_GET, 'title', "invalid"),
        'mode' => getData($_GET, 'mode', "invalid"),
        'plugins'=> explode(',', getData($_GET, 'plugins', "")),
        'error_file'=> getData($_GET, 'error_file', ""),
        'error_line'=> getData($_GET, 'error_line', ""),
        'error_msg'=> getData($_GET, 'error_msg', ""),
        'error_stack'=> getData($_GET, 'error_stack', ""),
    );
    $params['index'] = $index;
    $params['type'] = 'servers';
//      $params['settings']['index'] = $settings;

    $ret = $client->index($params);
}  else if(getData($_GET, 'page', '') == 'handshake'){
    $key = md5(getData($_GET, 'server-login'));
    display(array('key'=>$key));

    if (!file_exists('var/servers/'.$key)) {
        file_put_contents('var/servers/'.$key,0);
    }
} else {
    print_r($_GET);
    display(array('error'=>'1'));
}

function getData($data, $key, $default = "") {
    return isset($data[$key]) ? $data[$key] : $default;
}

function getIndexToUse($time, $prefix){
    return "$prefix-".date('Y.m.d',($time));
}

function getCurrentTimeStamp($time) {
    $time = ((int)($time/INTERVAL))*INTERVAL;

    return date('Y-m-d',($time)).'T'.date('H:i:s', ($time)).'Z';
}

function display($data) {
    echo json_encode($data);
}

function canSendData($key, $time, $check = true) {

    if (file_exists('var/servers/'.$key)) {
        if (!$check) {
            return true;
        }
        $thenTime = (int)file_get_contents('var/servers/' . $key);

        if ( ($thenTime + INTERVAL - 10) < $time ) {
            file_put_contents('var/servers/' . $key, $time);
            return true;
        }
    }
    return false;
}