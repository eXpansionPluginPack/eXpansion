<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Windows;

use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\DataAccess;
use ManiaLivePlugins\eXpansion\Gui\Structures\ButtonHook;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxInfo;
use ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxMap as MxMapItem;
use ManiaLivePlugins\eXpansion\ManiaExchange\ManiaExchange;
use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\HookData;
use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap;
use Maniaplanet\DedicatedServer\Structures\Map;

class MxUpdate extends Window
{
    const chunkSize = 50;

    protected $frame;
    protected $pager;
    protected $items = array();
    /** @var Storage */
    protected $storage;
    /**
     * @var MxMap[] $maps
     */
    protected $maps = array();

    /** @var Map[] */
    protected $mapsByUID = array();

    protected $mapsToProcess = array();
    protected $processedCounter = 0;
    protected $mxPlugin = null;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->frame->addComponent($this->pager);
        $this->addComponent($this->frame);
        $this->update();
    }

    public function update()
    {
        $this->clearItems();

        $this->pager->addItem(
            new MxInfo(
                0,
                "Recieving maps info, please wait...",
                $this->sizeX - 6
            )
        );
        $this->redraw($this->getRecipient());

        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->maps = array();
        $this->mapsToProcess = array_chunk($this->storage->maps, self::chunkSize);
        foreach ($this->storage->maps as $map) {
            $this->mapsByUID[$map->uId] = $map;
        }

        $this->processedCounter = 0;

        if (count(array_keys($this->mapsToProcess)) >= 1) {
            $this->queryMaps($this->getUids($this->mapsToProcess[0]));
        } else {
            $this->clearItems();
            $this->pager->addItem(
                new MxInfo(
                    0,
                    "Error, not enough maps.",
                    $this->sizeX - 6
                )
            );
            $this->redraw($this->getRecipient());
        }
    }

    public function queryMaps($uids)
    {

        $storage = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance();
        $title = "tm";
        if ($storage->simpleEnviTitle == \ManiaLivePlugins\eXpansion\Helpers\Storage::TITLE_SIMPLE_SM) {
            $title = "sm";
        }

        $query = 'https://api.mania-exchange.com/' . $title . '/maps?ids=' . $uids;

        $access = DataAccess::getInstance();
        $options = array(CURLOPT_HTTPHEADER => array("Content-Type" => "application/json"));
        $access->httpCurl($query, array($this, "xUpdate"), null, $options);

    }


    /**
     * @param Map[] $maps
     * @return string
     */
    private function getUids($maps)
    {
        /** @var Map $map */
        $uids = "";
        foreach ($maps as $map) {
            $uids .= $map->uId . ",";
        }
        return rtrim($uids, ",");

    }

    public function xUpdate($job, $jobData)
    {
        $info = $job->getCurlInfo();
        $code = $info['http_code'];

        $data = $job->getResponse();

        // if user has closed the window... return, since otherwise we have fatal error.
        if ($this->pager == null) {
            return;
        }

        $this->clearItems();

        if ($code !== 200) {
            $this->pager->addItem(
                new MxInfo(
                    0,
                    "search returned a http error " . $code,
                    $this->sizeX - 6
                )
            );
            $this->redraw();
            return;
        }

        $json = json_decode($data, true);

        if ($json === false) {
            $this->pager->addItem(
                new MxInfo(
                    0,
                    "Error while processing json data from MX.",
                    $this->sizeX - 6
                )
            );
            $this->redraw();
            return;
        }

        foreach ($json as $map) {
            $map = MxMap::fromArray($map);
            $this->maps[] = $map;
            ManiaExchange::addMxInfo($map);
        }

        $this->processedCounter += 1;

        if (array_key_exists($this->processedCounter, $this->mapsToProcess)) {
            $this->queryMaps($this->getUids($this->mapsToProcess[$this->processedCounter]));
            $this->pager->addItem(
                new MxInfo(
                    0,
                    "Processing chunk " . ($this->processedCounter + 1) . "/" . count(array_keys($this->mapsToProcess)),
                    $this->sizeX - 6
                )
            );
            $this->redraw();
        } else {
            $this->redrawData();
        }

    }

    public function search($login, $trackname = "", $author = "", $style = null, $length = null)
    {
        // do nothing
    }

    public function redrawData()
    {
        $this->clearItems();
        $x = 0;

        $login = $this->getRecipient();
        $isadmin = AdminGroups::hasPermission(
            $login,
            Permission::MAP_ADD_MX
        );

        $buttons = $this->hookButtons($isadmin);
        $connection = Helper::getSingletons()->getDediConnection();
        $mapdir = $connection->getMapsDirectory();

        foreach ($this->maps as $map) {
            if (array_key_exists($map->trackUID, $this->mapsByUID)) {

                $fileCreated = filectime($mapdir . DIRECTORY_SEPARATOR . $this->mapsByUID[$map->trackUID]->fileName);
                $mapUpdated = strtotime($map->updatedAt);

                if ($fileCreated < $mapUpdated) {
                    $this->items[$x] = new MxMapItem($x, $map, $this, $buttons, $this->sizeX - 9);
                    $this->pager->addItem($this->items[$x]);
                    $x++;
                }
            }
        }
        $this->setTitle("Update Maps ", "(" . $x . ")");
        if ($x == 0) {
            $this->pager->addItem(
                new MxInfo(
                    0,
                    "All maps up-to-date!",
                    $this->sizeX - 6
                )
            );
        }
        $this->redraw($this->getRecipient());
    }

    protected function hookButtons($isadmin = false)
    {
        $buttons = array();

        if ($isadmin) {
            $buttons['install'] = new ButtonHook();
            $buttons['install']->callback = array($this, 'updateMap');
            $buttons['install']->label = 'Update';
        }

        $hook = new HookData();
        $hook->data = $buttons;

        // \ManiaLive\Event\Dispatcher::dispatch(new ListButtons(ListButtons::ON_BUTTON_LIST_CREATE, $hook, 'test'));

        return $buttons;
    }

    public function updateMap($login, $mapId)
    {
        $this->mxPlugin->addMap($login, $mapId);
    }

    public function setMain($class)
    {
        $this->mxPlugin = $class;
    }


    public function clearItems()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->frame->setSizeX($this->sizeX);
        $this->pager->setSize($this->sizeX - 3, $this->sizeY - 12);
        $this->frame->setPosition(0, -6);
    }

}