<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures;

/**
 * Description of HttpQuery
 *
 * @author Reaby
 */
class HttpQuery extends \DedicatedApi\Structures\AbstractStructure {

    public $url;
    public $callback;
    public $userAgent = "ManiaLive - eXpansionPluginPack";
    public $mimeType = "text/html";
    public $baseurl;
    public $redirectCount = 0;
    public $params = "";

    /** @var array */
    public $callparams = array();

    public function __construct($url, $callback, $callParams = array(), $userAgent = "ManiaLive - eXpansionPluginPack", $mimeType = "text/html") {
        $this->url = $url;
        $pos = strpos($url, "?");
        if ($pos) {
            $this->baseurl = trim(substr($url, 0, $pos), "?");
            $this->params = trim(substr($url, $pos), "?");
        } else {
            $this->baseurl = $url;
            $this->params = "";
        }
        $this->callback = $callback;
        if (!is_array($callParams))
            $callParams = array($callParams);
        $this->callparams = $callParams;
        $this->userAgent = $userAgent;
        $this->mimeType = $mimeType;
    }

}
