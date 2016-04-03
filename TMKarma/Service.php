<?php

namespace ManiaLivePlugins\eXpansion\TMKarma;

use ManiaLivePlugins\eXpansion\TMKarma\Structures\Karma;
use ManiaLive\Utilities\Console;

abstract class Service
{

    /**
     * Link continents with their specific API URLs.
     * @var unknown_type
     */
    protected static $apiUrls = array(
        'AFRICA' => 'http://africa.tm-karma.com/API-v2',
        'ASIA' => 'http://asia.tm-karma.com/API-v2',
        'EUROPE' => 'http://europe.tm-karma.com/API-v2',
        'SOUTHAMERICA' => 'http://south-america.tm-karma.com/API-v2',
        'NORTHAMERICA' => 'http://north-america.tm-karma.com/API-v2',
        'OCEANIA' => 'http://oceania.tm-karma.com/API-v2',
        'RUSSIA' => 'http://russia.tm-karma.com/API-v2'
    );
    public static $login = null;
    static protected $apiUrl = null;
    static protected $userCountryCode = null;
    static protected $authCode = null;

    /**
     * forceCountryCode();
     *
     * @param string $code
     * @throws Exception
     */
    static function forceCountryCode($code)
    {
        if (!isset(Data::$countries[$code])) {
            throw new Exception("The country code $code is not known!");
        }
        self::$userCountryCode = $code;
    }

    /**
     * Return the country code you are running
     * this script in.
     * @param bool $long
     */
    protected static function getCountryCode($long = false)
    {
        if (!isset(self::$userCountryCode)) {
            $this->console("[TMKarma] Attempting to autodetect using http://api.hostip.info!");
            $data = file_get_contents('http://api.hostip.info/get_json.php');
            $json = json_decode($data);
            if ($json === null) {
                $this->console("[TMKarma] Autodetect failed, usign default location: Germany");
                self::$userCountryCode = "DEU";
            } else {
                self::$userCountryCode = \ManiaLivePlugins\eXpansion\Helpers\Countries::mapCountry(ucwords(strtolower($json->country_name)));
                if (self::$userCountryCode == "OTH") {
                    $this->console("[TMKarma] Autodetect failed, usign default location: Germany, Detected country was: " . ucwords(strtolower($json->country_name)));
                    self::$userCountryCode = "DEU";
                }
            }
        }

        if ($long) {
            return Data::$countries[self::$userCountryCode][1];
        } else {
            return self::$userCountryCode;
        }
    }

    /**
     * Returns the continent you are running
     * this script in.
     */
    protected static function getContinent()
    {
        $code = self::getCountryCode();
        $continent = ucwords(strtolower(Data::$countries[$code][1]));
        return $continent;
    }

    /**
     * Returns the url where you can find the api
     * depending on the country you are in.
     */
    protected static function getAPIUrl()
    {
        if (!isset(self::$apiUrl)) {
            $code = self::getContinent();
            self::$apiUrl = Data::$apiUrls[$code];
            //var_dump(Data::$apiUrls[$code]);
        }

        return self::$apiUrl;
    }

    /**
     * Creates a user agent string for
     * the request.
     */
    protected static function getUserAgent()
    {
        return $agent;
    }

    /**
     * Returns string with the users location
     * information.
     */
    static function getLocationInfo()
    {
        $code = self::getCountryCode();
        $country = ucwords(strtolower(Data::$countries[$code][0]));
        $continent = ucwords(strtolower(Data::$countries[$code][1]));
        return $country . ' (' . $continent . ')';
    }

    /**
     * Authenticates at the tm-karma webservice.
     * @param sring $serverName
     * @param string $login
     * @param integer $communityCode
     */
    static function Authenticate($serverName, $login, $game)
    {
        self::$login = $login;

        $nation = self::getLocationInfo();

        // Generate the url for the first Auth-Request
        $requestUrl = sprintf("%s?Action=Auth&login=%s&name=%s&game=%s&zone=%s&nation=%s", self::getAPIUrl(), urlencode(self::$login), base64_encode($serverName), urlencode('ManiaPlanet'), urlencode(ucwords(strtolower(Data::$countries[self::$userCountryCode][0]))), urlencode(ucwords(strtolower(self::$userCountryCode)))
        );
        //var_dump($requestUrl);
        try {
            $result = self::sendRequest($requestUrl);
            self::$authCode = $result->authcode;
        } catch (ApiException $e) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the challenge's karma from the webservice.
     * @param \Maniaplanet\DedicatedServer\Structures\Map $challenge
     * @param \ManiaLive\Data\Player[] $players
     * @return \ManiaLivePlugins\eXpansion\TMKarma\Structures\Karma
     */
    static function GetChallengeKarma($challenge, $players)
    {
        if (!self::$authCode) {
            throw new NotAuthenticatedException('You need to authenticate at the tm-karma webservice first!');
        }

        if (!count($players)) {
            return new Karma();
        }

        $playersString = '';
        foreach ($players as $player)
            $playersString .= urlencode($player->login) . '|';
        $playersString = substr($playersString, 0, -1);

        $requestUrl = sprintf("%s?Action=Get&login=%s&authcode=%s&uid=%s&map=%s&author=%s&env=%s&player=%s", self::getAPIUrl(), urlencode(self::$login), urlencode(self::$authCode), urlencode($challenge->uId), base64_encode($challenge->name), urlencode($challenge->author), urlencode($challenge->environnement), $playersString
        );

        $response = self::sendRequest($requestUrl);

        $karma = new Karma($response);

        return $karma;
    }

    /**
     *
     * SendVotes()
     * @param \Maniaplanet\DedicatedServer\Structures\Map $challenge
     * @param Structures\Vote[] $votes
     */
    static function SendVotes(\Maniaplanet\DedicatedServer\Structures\Map $challenge, $votes)
    {
        if (!self::$authCode) {
            throw new NotAuthenticatedException('You need to authenticate at the tm-karma webservice first!');
        }

        if (sizeof($votes) == 0)
            return;

        $voteString = "";
        foreach ($votes as $vote)
            $voteString .= urlencode($vote->login) . "=" . $vote->vote . "|";
        $voteString = trim($voteString, "|");

        // Generate the url for this vote
        $requestUrl = sprintf("%s?Action=Vote&login=%s&authcode=%s&uid=%s&map=%s&author=%s&env=%s&votes=%s&tmx=%s", self::getAPIUrl(), urlencode(self::$login), urlencode(self::$authCode), urlencode($challenge->uId), base64_encode($challenge->name), urlencode($challenge->author), urlencode($challenge->environnement), $voteString, '');
        //var_dump($requestUrl);
        $response = self::sendRequest($requestUrl);
        //var_dump($response);
        //return new Karma($response);
    }

    /**
     * Sends GET request to the Karma Webservice.
     * @param string $geturl
     * @throws \Exception
     */
    static function sendRequest($url)
    {
        $agent = '';
        $agent .= 'ManiaLive-eXp/' . \ManiaLive\Application\Version;
        $agent .= ' TMKarma/0.0.1';
        $agent .= '';

        $params = array(
            'http' => array(
                'method' => "GET",
                'header' => array("User-Agent: " . $agent)
            )
        );

        $ctx = stream_context_create($params);
        $content = file_get_contents($url, false, $ctx);


        $response = simplexml_load_string($content);

        if ($response->status != 200) {
            throw new ApiException('Webservice returned wrong state [' . $response->status . '] for Request: ' . $url);
        }

        return $response;
    }

}

class Exception extends \Exception
{

}

class NotAuthenticatedException extends Exception
{

}

class ApiException extends Exception
{

}

?>