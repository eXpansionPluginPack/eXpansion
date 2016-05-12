<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Windows;

use ManiaLib\Application\ErrorHandling;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Structures\ButtonHook;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\Helpers\Storage;
use ManiaLivePlugins\eXpansion\ManiaExchange\Config;
use ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxMap;
use ManiaLivePlugins\eXpansion\ManiaExchange\Hooks\ListButtons;
use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\HookData;
use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap as Map;
use oliverde8\AsynchronousJobs\Job\Curl;

class MxSearch extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{
    /** @var \ManiaLive\Gui\Controls\Pager */
    private $pager;

    /** @var  \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var  \ManiaLive\Data\Storage */
    private $storage;
    private $maps;
    private $frame;
    private $searchframe;
    private $inputAuthor;
    private $inputMapName;
    private $buttonSearch;
    private $actionSearch;
    private $header;
    private $style;
    private $lenght;
    private $items = array();
    public $mxPlugin;

    protected function onConstruct() {
        parent::onConstruct();

        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->searchframe = new \ManiaLive\Gui\Controls\Frame();
        $this->searchframe->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->inputMapName = new Inputbox("mapName");
        $this->inputMapName->setLabel("Map name");
        $this->searchframe->addComponent($this->inputMapName);
        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(3, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->searchframe->addComponent($spacer);

        $this->inputAuthor = new Inputbox("author");
        $this->inputAuthor->setLabel("Author name");
        $this->searchframe->addComponent($this->inputAuthor);
        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(3, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $items = array("All", "Race", "Fullspeed", "Tech", "RPG", 'LOL', 'PressForward', 'SpeedTech', 'Multilap', 'Offroad');
        $this->style = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown("style", $items);
        $this->searchframe->addComponent($this->style);

        $items = array("All", "15sec", "30sec", "45sec", "1min");
        $this->lenght = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown("length", $items);

        $this->searchframe->addComponent($this->lenght);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(10, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->searchframe->addComponent($spacer);

        $this->actionSearch = ActionHandler::getInstance()->createAction(array($this, "actionOk"));


        $this->buttonSearch = new OkButton(24, 6);
        $this->buttonSearch->setText("Search");
        $this->buttonSearch->colorize('0a0');
        $this->buttonSearch->setScale(0.6);
        $this->buttonSearch->setAction($this->actionSearch);

        $this->searchframe->addComponent($this->buttonSearch);

        $this->mainFrame->addComponent($this->searchframe);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        //$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());

        /* $this->header = new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\Header();
          $this->frame->addComponent($this->header); */

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->frame->addComponent($this->pager);

        $this->mainFrame->addComponent($this->frame);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->frame->setSizeX($this->sizeX);
        $this->pager->setSize($this->sizeX - 3, $this->sizeY - 12);
        $this->searchframe->setPosition(8, -3);
        $this->frame->setPosition(0, -6);
    }

    public function setPlugin($plugin) {
        $this->mxPlugin = $plugin;
    }

    public function search($login, $trackname = "", $author = "", $style = null, $length = null) {
        foreach ($this->items as $item)
            $item->erase();

        $this->pager->clearItems();
        $this->items = array();
        $this->pager->addItem(new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxInfo(0, "Searching, please wait", $this->sizeX - 6));
        $this->redraw($this->getRecipient());

        $info = $this->connection->getVersion();

        /**
         * @var Storage $storage
         */
        $storage = Storage::getInstance();

        if ($storage->simpleEnviTitle == Storage::TITLE_SIMPLE_SM) {

            $script = $this->connection->getModeScriptInfo();
            $query = "";

            /** @var Storage $storage */
            $storage = Storage::getInstance();
            $titlePack = $storage->version->titleId;
            $mapType = $storage->baseMapType;
            $parts = explode('@', $titlePack);
            $titlePack = $parts[0];

            $query = 'https://sm.mania-exchange.com/tracksearch2/search?mode=0&vm=0&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&mtype=All&mtype=' . rawurlencode($mapType) . '&priord=2&limit=100&environments=1&tracksearch&api=on&format=json';
        }
        else {
            $query = 'https://tm.mania-exchange.com/tracksearch2/search?api=on&format=json';

            switch ($info->titleId) {
                case "TMCanyon":
                    $query .= "&tpack=TMCanyon,Canyon";
                    break;
                case "TMStadium":
                    $query .= "&tpack=TMStadium,Stadium";
                    break;
                case "TMValley":
                    $query .= "&tpack=TMValley";
                    break;
                default :
                    break;
            }
            $out = "";
            if ($style != null) {
                $out .= "&style=" . $style;
            }
            if ($length != null) {
                $out .= "&length=" . $length . "&lengthop=0";
            }

            $query .= '&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . $out . '&mtype=All&priord=2&limit=100';
        }
        /*
          $ch = curl_init($query);
          curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MXapi [search] ver 0.1");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $data = curl_exec($ch);
          $status = curl_getinfo($ch);
          curl_close($ch);

          if ($data === false) {
          $this->connection->chatSendServerMessage('$f00$oError $z$s$fff MX is down', $login);
          return;
          }

          if ($status["http_code"] !== 200) {
          $this->connection->chatSendServerMessage('$f00$oError $z$s$fff MX returned http error code:' . $status["http_code"], $login);
          return;
          }
         */

        $access = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();

        $options = array(CURLOPT_HTTPHEADER => array("Content-Type" => "application/json"));
        if ($length !== null)
            $this->lenght->setSelected(intval($length) + 1);
        if ($style !== null)
            $this->style->setSelected(intval($style));
        $access->httpCurl($query, array($this, "xSearch"), null, $options);

        return;
    }

    /**
     * @param Curl $job
     * @param      $jobData
     */
    function xSearch($job, $jobData) {
        $info = $job->getCurlInfo();
        $code = $info['http_code'];

        $data = $job->getResponse();

        // if user has closed the window... return, since otherwise we have fatal error.
        if ($this->pager == null) {
            return;
        }

        foreach ($this->items as $item)
            $item->erase();

        $this->pager->clearItems();
        $this->items = array();

        if ($code !== 200) {
            $this->pager->addItem(new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxInfo(0, "search returned a http error " . $code, $this->sizeX - 6));
            $this->redraw();

            return;
        }

        try {
            if (! $data) {
                $this->pager->addItem(new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxInfo(0, "search returned no data", $this->sizeX - 6));
                $this->redraw();

                return;
            }
            $json = json_decode($data, true);

            if (isset($json[0]) && ! isset($json['results'])) {
                $newArray['results'] = $json;
                $json = $newArray;
            }

            if ($json === false) {
                $this->pager->addItem(new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxInfo(0, "Error while processing json data from MX.", $this->sizeX - 6));
                $this->redraw();

                return;
            }
            if (! array_key_exists("results", $json)) {
                $this->pager->addItem(new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxInfo(0, "Error: MX returned no results.", $this->sizeX - 6));
                $this->redraw();

                return;
            }

            $this->maps = Map::fromArrayOfArray($json['results']);


            $login = $this->getRecipient();
            $isadmin = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::map_addMX);

            $buttons = $this->hookButtons($isadmin);

            $x = 0;
            if (empty($this->maps)) {
                $this->pager->addItem(new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxInfo(0, "No maps found with this search terms.", $this->sizeX - 6));
            }
            else {
                foreach ($this->maps as $map) {
                    $this->items[ $x ] = new MxMap($x, $map, $this, $buttons, $this->sizeX - 9);
                    $this->pager->addItem($this->items[ $x ]);
                    $x++;
                }
            }

            $this->redraw();
        }
        catch (\Exception $ex) {
            Helper::logError(ErrorHandling::computeMessage($ex));
        }
    }

    protected function hookButtons($isadmin) {
        $buttons = array();

        $config = Config::getInstance();

        if ($isadmin) {
            $buttons['install'] = new ButtonHook();
            $buttons['install']->callback = array($this, 'addMap');
            $buttons['install']->label = 'Install';
        }

        if ($config->mxVote_enable) {
            $buttons['queue'] = new ButtonHook();
            $buttons['queue']->callback = array($this, 'mxVote');
            $buttons['queue']->label = 'Queue';
        }

        $hook = new HookData();
        $hook->data = $buttons;

        \ManiaLive\Event\Dispatcher::dispatch(new ListButtons(ListButtons::ON_BUTTON_LIST_CREATE, $hook, 'test'));

        return $hook->data;
    }

    function addMap($login, $mapId) {
        $this->mxPlugin->addMap($login, $mapId);
    }

    function mxVote($login, $mapId) {
        $this->mxPlugin->mxVote($login, $mapId);
    }

    function actionOk($login, $args) {
        $style = null;
        $length = null;
        if ($args['style']) {
            $style = intval($args['style']);
        }

        if (intval($args['length']) != 0) {
            $length = intval($args['length']) - 1;
        }

        $this->search($login, $args['mapName'], $args['author'], $style, $length);
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->erase();

        $this->items = array();
        $this->maps = null;
        $this->style->destroy();
        $this->lenght->destroy();
        $this->inputMapName->destroy();
        $this->inputAuthor->destroy();
        // $this->header->destroy();
        $this->pager->destroy();
        $this->pager = null;
        $this->connection = null;
        $this->storage = null;
        $this->searchframe->clearComponents();
        $this->searchframe->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}

?>
