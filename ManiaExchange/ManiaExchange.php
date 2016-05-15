<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Widgets\MxWidget;
use ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Windows\MxSearch;
use oliverde8\AsynchronousJobs\Job\Curl;

class ManiaExchange extends ExpPlugin
{
    /** @var Config * */
    private $config;

    /** @var \Maniaplanet\DedicatedServer\Structures\Vote */
    private $vote;

    /** @var string */
    private $titleId;

    /** @var \ManiaLivePlugins\eXpansion\Core\i18n\Message */
    private $msg_add;

    /** @var \ManiaLivePlugins\eXpansion\Core\DataAccess */
    private $dataAccess;
    private $cmd_add;

    public function expOnInit()
    {
        $this->config = Config::getInstance();
    }

    public function eXpOnLoad()
    {

        $this->msg_add = eXpGetMessage('Map %s $z$s$fff added from MX Succesfully');
    }

    public function eXpOnReady()
    {
        $this->dataAccess = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();
        $this->registerChatCommand("mx", "chatMX", 2, true);
        $this->registerChatCommand("mx", "chatMX", 1, true);
        $this->registerChatCommand("mx", "chatMX", 0, true);
        $this->setPublicMethod("mxSearch");

        $cmd = AdminGroups::addAdminCommand('add', $this, 'addMap', 'server_maps'); //
        $cmd->setHelp('Adds a map from ManiaExchange');
        $cmd->setHelpMore('$w/admin add #id$z will add a map with id fron ManiaExchange');
        $cmd->setMinParam(1);
        $this->cmd_add = $cmd;

//
//if ($this->config->mxVote_enable) {
//  $this->registerChatCommand('mxqueue', "mxVote", 1, true);
//}

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addSeparator', __('ManiaExchange'), false);
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addItem', __('Search Maps'), null, array($this, 'mxSearch'), false);
        }

        $version = $this->connection->getVersion();
        $this->titleId = $version->titleId;
        $this->enableDedicatedEvents();

        $widget = Gui\Widgets\MxWidget::Create();
        $widget->setDisableAxis("x");
        $widget->show();
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        Gui\Windows\MxSearch::Erase($login);
        Gui\Widgets\MxWidget::Erase($login);
    }

    public function chatMX($login, $arg = "", $param = null)
    {
        switch ($arg) {
            case "add":
                $this->addMap($login, $param);
                break;
            case "search":
                $this->mxSearch($login, $param, "");
                break;
            case "author":
                $this->mxSearch($login, "", $param);
                break;
            case "queue":
                $this->mxVote($login, $param);
                break;
            case "help":
            default:
                $msg = eXpGetMessage("usage /mx add [id], /mx queue [id], /mx search \"terms here\"  \"authorname\" ,/mx author \"name\" ");
                $this->eXpChatSendServerMessage($msg, $login);
                break;
        }
    }

    public function mxSearch($login, $search = "", $author = "")
    {
        $window = Gui\Windows\MxSearch::Create($login);
        $window->setTitle('ManiaExchange');
        $window->setPlugin($this);
        $window->search($login, $search, $author);
        $window->setSize(210, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function addMap($login, $mxId)
    {
        if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance()->hasPermission($login, 'map_add')) {
            $this->connection->chatSendServerMessage(__('$iYou don\'t have permission to do that!', $login, $mxId), $login);

            return;
        }

        if (is_array($mxId)) $mxId = $mxId[0];

        if ($mxId == 'this') {
            try {
                $this->connection->addMap($this->storage->currentMap->fileName);
                $this->eXpChatSendServerMessage($this->msg_add, null, array($this->storage->currentMap->name));
            } catch (\Exception $e) {
                $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()), $login);
            }

            return;
        }
        $this->download($mxId, $login, "xAddMapAdmin");
    }

    /**
     *
     * @param string $mxId
     * @param string $login
     * @param        $redirect
     *
     * @return string
     */
    function download($mxId, $login, $redirect)
    {
        if (!is_numeric($mxId)) {
            $this->connection->chatSendServerMessage(__('"%s" is not a numeric value.', $login, $mxId), $login);

            return false;
        }

        $query = "";
        switch ($this->expStorage->simpleEnviTitle) {
            case "SM":
                $query = 'https://sm.mania-exchange.com/tracks/download/' . $mxId;
                break;
            case "TM":
                $query = 'https://tm.mania-exchange.com/tracks/download/' . $mxId;
                break;
        }

        $this->eXpChatSendServerMessage("Download starting for: $mxId", $login);
        //$this->dataAccess->httpGet($query, array($this, $redirect), array($login, $mxId), "Manialive/eXpansion MXapi [getter] ver 0.1", "application/json");
        $options = array(CURLOPT_HTTPHEADER => array("X-ManiaPlanet-ServerLogin" => $this->storage->serverLogin));
        $this->dataAccess->httpCurl($query, array($this, $redirect), array("login" => $login, "mxId" => $mxId), $options);
    }

    /**
     * @param Curl $job
     * @param      $jobData
     */
    function xAddMapAdmin($job, $jobData)
    {
        $info = $job->getCurlInfo();
        $code = $info['http_code'];

        $additionalData = $job->__additionalData;

        $mxId = $additionalData['mxId'];
        $login = $additionalData['login'];

        $data = $job->getResponse();

        if ($code !== 200) {
            if ($code == 302) {
                $this->eXpChatSendServerMessage("Map author has declined the permission to download this map!", $login);

                return;
            }
            $this->eXpChatSendServerMessage("MX returned error code $code", $login);

            return;
        }
        /** @var \Maniaplanet\DedicatedServer\Structures\Version */
        $game = $this->connection->getVersion();
        $dir = Helper::getPaths()->getDownloadMapsPath() . $game->titleId;
        if ($this->expStorage->isRemoteControlled) {
            $dir = "Downloaded/" . $game->titleId;
        }

        $file = $dir . "/" . $mxId . ".Map.Gbx";

        if ($this->expStorage->isRemoteControlled) {
            $this->saveMapRemotelly($file, $dir, $data, $login);
        } else {
            $this->saveMapLocally($file, $dir, $data, $login);
        }
    }

    public function saveMapRemotelly($file, $dir, $data, $login)
    {
        try {
            if ($this->connection->writeFile($file, $data)) {

                try {
                    if (!$this->connection->checkMapForCurrentServerParams($file)) {
                        $msg = eXpGetMessage("The Map is not compatible with current server settings, map not added.");
                        $this->eXpChatSendServerMessage($msg, $login);

                        return;
                    }

                    $this->connection->addMap($file);

                    $map = $this->connection->getMapInfo($file);
                    $this->eXpChatSendServerMessage($this->msg_add, null, array($map->name));
                    if ($this->config->juke_newmaps) {
                        $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Maps\Maps', "queueMap", $login, $map, false);
                    }
                } catch (\Exception $e) {
                    $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()), $login);
                }
            } else {
                $this->eXpChatSendServerMessage("Error while saving a map file at remote host: " . $file, $login);
            }
        } catch (Exception $ex) {
            $this->eXpChatSendServerMessage("Error while saving a map file at remote host :" . $e->getMessage(), $login);
        }
    }

    public function saveMapLocally($file, $dir, $data, $login)
    {
        try {
            if (!is_dir($dir)) {
                mkdir($dir, 0775);
            }

            if (is_dir($dir) && $this->dataAccess->save($file, $data)) {

                try {
                    if (!$this->connection->checkMapForCurrentServerParams($file)) {
                        $msg = eXpGetMessage("Map is not compatible with current server settings, map not added.");
                        $this->eXpChatSendServerMessage($msg, $login);

                        return;
                    }

                    $this->connection->addMap($file);

                    $map = $this->connection->getMapInfo($file);
                    $this->eXpChatSendServerMessage($this->msg_add, null, array($map->name));
                    if ($this->config->juke_newmaps) {
                        $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Maps\Maps', "queueMap", $login, $map, false);
                    }
                } catch (\Exception $e) {
                    $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()), $login);
                }
            } else {
                $this->eXpChatSendServerMessage("Error while saving a map file. ", $login);
            }
        } catch (\Exception $ex) {
            $this->eXpChatSendServerMessage("Error while saving a map file : " . $ex->getMessage(), $login);
        }
    }

    /**
     * @param Curl $job
     * @param      $jobData
     */
    function xQueue($job, $jobData)
    {
        $info = $job->getCurlInfo();
        $code = $info['http_code'];

        $additionalData = $job->__additionalData;

        $mxId = $additionalData['mxId'];
        $login = $additionalData['login'];

        $data = $job->getResponse();

        if ($code !== 200) {
            if ($code == 302) {
                $this->eXpChatSendServerMessage("Map author has declined the permission to download this map!", $login);

                return;
            }
            $this->eXpChatSendServerMessage("MX returned error code $code", $login);

            return;
        }

        $file = Helper::getPaths()->getDownloadMapsPath() . $mxId . ".Map.Gbx";

        if (!is_dir(Helper::getPaths()->getDownloadMapsPath())) {
            mkdir(Helper::getPaths()->getDownloadMapsPath(), 0775);
        }

        if ($this->dataAccess->save($file, $data)) {
            if (!$this->connection->checkMapForCurrentServerParams($file)) {
                $msg = eXpGetMessage("Map is not compatible with current server settings, map not added.");
                $this->eXpChatSendServerMessage($msg, $login);

                return;
            }
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\\Maps\\Maps', 'queueMxMap', $login, $file);
        }
    }

    function mxVote($login, $mxId)
    {
        if (!$this->config->mxVote_enable) return;

        if (!is_numeric($mxId)) {
            $this->connection->chatSendServerMessage(__('"%s" is not a numeric value.', $login, $mxId), $login);

            return;
        }

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::MAP_ADD_MX)) {
            $this->mxQueue($login, $mxId);

            return;
        }

        $queue = $this->callPublicMethod('\ManiaLivePlugins\eXpansion\\Maps\\Maps', 'returnQueue');
        foreach ($queue as $q) {
            if ($q->player->login == $login) {
                $msg = eXpGetMessage('#admin_error# $iYou already have a map in the queue...');
                $this->eXpChatSendServerMessage($msg, $login);

                return;
            }
        }


        $query = "";
        switch ($this->expStorage->simpleEnviTitle) {
            case "SM":
                $query = 'https://sm.mania-exchange.com/api/tracks/get_track_info/id/' . $mxId;
                break;
            case "TM":
                $query = 'https://tm.mania-exchange.com/api/tracks/get_track_info/id/' . $mxId;
                break;
        }

        //$this->dataAccess->httpGet($query, Array($this, ), array($login, $mxId), "Manialive/eXpansion MXapi [search] ver 0.1",  "application/json");

        $options = array(CURLOPT_HTTPHEADER => "X-ManiaPlanet-ServerLogin:" . $this->storage->serverLogin);
        $this->dataAccess->httpCurl($query, array($this, "xVote"), array("login" => $login, "mxId" => $mxId), $options);
    }

    //function xVote($data, $code, $login, $mxId)
    function xVote($job, $jobData)
    {
        $info = $job->getCurlInfo();
        $code = $info['http_code'];

        $additionalData = $job->__additionalData;

        $mxId = $additionalData['mxId'];
        $login = $additionalData['login'];

        $data = $job->getResponse();

        if ($code !== 200) {
            if ($code == 302) {
                $this->eXpChatSendServerMessage("Map author has declined the permission to download this map!", $login);

                return;
            }
            $this->eXpChatSendServerMessage("Mx error: $code", $login);

            return;
        }
        $map = json_decode($data, true);

        if (!$map) {
            $this->connection->chatSendServerMessage(__('Unable to retrieve track info from MX..  wrong ID..?'), $login);

            return;
        }

        $version = $this->connection->getVersion();

        if (strtolower(substr($version->titleId, 2)) != strtolower($map['EnvironmentName'])) {
            $this->connection->chatSendServerMessage(__('Wrong environment!'), $login);

            return;
        }

        $this->vote = array();
        $this->vote['login'] = $login;
        $this->vote['mxId'] = $mxId;

        $vote = new \Maniaplanet\DedicatedServer\Structures\Vote();
        $vote->callerLogin = $login;
        $vote->cmdName = '$0f0add $fff$o' . $map['Name'] . '$o$0f0 by $eee' . $map['Username'] . ' $0f0';
        $vote->cmdParam = array('to the queue from MX?$3f3');
        $this->connection->callVote($vote, $this->config->mxVote_ratio, ($this->config->mxVote_timeout * 1000), $this->config->mxVote_voters);
    }

    function mxQueue($login, $mxId)
    {
        $this->download($mxId, $login, "xQueue");
    }

    function onVoteUpdated($stateName, $login, $cmdName, $cmdParam)
    {
        switch ($cmdParam) {
            case 'to the queue from MX?$3f3':
                switch ($stateName) {
                    case "VotePassed":
                        $msg = eXpGetMessage('#record# $iVote passed!');
                        $this->eXpChatSendServerMessage($msg, null);
                        $this->mxQueue($this->vote['login'], $this->vote['mxId']);
                        $this->vote = array();
                        break;
                    case "VoteFailed":
                        $msg = eXpGetMessage('#admin_error# $iVote failed!');
                        $this->eXpChatSendServerMessage($msg, null);
                        $this->vote = array();
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    }

    function eXpOnUnload()
    {
        MxWidget::EraseAll();
        MxSearch::EraseAll();
        AdminGroups::removeAdminCommand($this->cmd_add);
    }
}

?>