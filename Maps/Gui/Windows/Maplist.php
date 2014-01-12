<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Maps\Maps;

class Maplist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    public $records = array();
    public static $mapsPlugin = null;
    public static $localrecordsLoaded = false;
    private $history = array();
    private $ratingsLoaded = false;
    private $items = array();

    /** @var \ManiaLive\Gui\Controls\Pager */
    protected $pager;
    protected $btnRemoveAll;
    protected $frame;
    protected $title_mapName;
    protected $title_authorName;
    protected $title_goldTime;
    protected $title_rank;
    protected $title_rating;
    protected $title_actions;
    private $actionRemoveAll;

    /** @var \ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating[] */
    private $ratings = array();

    /** @var  \DedicatedApi\Connection */
    private $connection;

    /** @var  \ManiaLive\Data\Storage */
    private $storage;
    private $widths = array(8, 4, 3, 2, 2, 2, 6);

    /** @var \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap[] */
    private $maps = array();

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $sizeX = 100;
        $scaledSizes = Gui::getScaledSize($this->widths, $sizeX / .8);

        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->mainFrame->addComponent($this->frame);

        $textStyle = "TextCardRaceRank";
        $textColor = "000";
        $textSize = 2.5;
        $this->title_mapName = new \ManiaLib\Gui\Elements\Label();
        $this->title_mapName->setText(__("Map name", $login));
        $this->title_mapName->setStyle($textStyle);
        $this->title_mapName->setTextColor($textColor);
        $this->title_mapName->setAction($this->createAction(array($this, "updateList"), "name"));
        $this->title_mapName->setTextSize($textSize);
        $this->title_mapName->setScale(0.8);

        $this->frame->addComponent($this->title_mapName);

        $this->title_authorName = new \ManiaLib\Gui\Elements\Label();
        $this->title_authorName->setText(__("Author", $login));
        $this->title_authorName->setStyle($textStyle);
        $this->title_authorName->setTextColor($textColor);
        $this->title_authorName->setAction($this->createAction(array($this, "updateList"), "author"));
        $this->title_authorName->setScale(0.8);
        $this->title_authorName->setTextSize($textSize);
        $this->frame->addComponent($this->title_authorName);

        $this->title_goldTime = new \ManiaLib\Gui\Elements\Label();
        $this->title_goldTime->setText(__("Lenght", $login));
        $this->title_goldTime->setStyle($textStyle);
        $this->title_goldTime->setTextColor($textColor);
        $this->title_goldTime->setAction($this->createAction(array($this, "updateList"), "goldTime"));
        $this->title_goldTime->setScale(0.8);
        $this->title_goldTime->setTextSize($textSize);
        $this->frame->addComponent($this->title_goldTime);

        $this->title_rank = new \ManiaLib\Gui\Elements\Label();
        $this->title_rank->setText(__("Records", $login));
        $this->title_rank->setAlign("center");
        $this->title_rank->setStyle($textStyle);
        $this->title_rank->setTextColor($textColor);
        $this->title_rank->setAction($this->createAction(array($this, "updateList"), "localrecord"));
        $this->title_rank->setScale(0.8);
        $this->title_rank->setTextSize($textSize);
        $this->frame->addComponent($this->title_rank);

        $this->title_rating = new \ManiaLib\Gui\Elements\Label();
        $this->title_rating->setText(__("Ratings", $login));
        $this->title_rating->setAlign("center");
        $this->title_rating->setStyle($textStyle);
        $this->title_rating->setTextColor($textColor);
        $this->title_rating->setAction($this->createAction(array($this, "updateList"), "rating"));
        $this->title_rating->setScale(0.8);
        $this->title_rating->setTextSize($textSize);
        $this->frame->addComponent($this->title_rating);

        $this->title_actions = new \ManiaLib\Gui\Elements\Label();
        $this->title_actions->setText(__("Actions", $login));
        $this->title_actions->setScale(0.8);
        $this->title_actions->setTextSize($textSize);
        $this->title_actions->setTextColor($textColor);
        $this->title_actions->setStyle($textStyle);
        $this->frame->addComponent($this->title_actions);

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);

        if (array_key_exists($login, Maps::$playerSortModes) == false) {
            Maps::$playerSortModes[$login] = new \ManiaLivePlugins\eXpansion\Maps\Structures\MapSortMode();
        }

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'server_maps')) {
            $this->actionRemoveAll = $this->createAction(array($this, "removeAllMaps"));
            $this->btnRemoveAll = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
            $this->btnRemoveAll->setAction($this->actionRemoveAll);
            $this->btnRemoveAll->setText(__('$fff' . "Clear Maplist", $login));
            $this->btnRemoveAll->setScale(0.5);
            $this->btnRemoveAll->colorize("d00");
            $this->mainFrame->addComponent($this->btnRemoveAll);
        }
    }

    static function Initialize($mapsPlugin) {
        self::$mapsPlugin = $mapsPlugin;
    }

    function gotoMap($login, \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->gotoMap($login, $map);
        $this->Erase($this->getRecipient());
    }

    function removeMap($login, \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->removeMap($login, $map);
        $this->RedrawAll();
    }

    function queueMap($login, \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->queueMap($login, $map, false);
    }

    function showRec($login, \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->showRec($login, $map);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->getSizeX() - 4, $this->getSizeY() - 12);
        $this->pager->setPosition(0, -7);
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / 0.8));
        $this->title_mapName->setSizeX($scaledSizes[0]);
        $this->title_authorName->setSizeX($scaledSizes[1]);
        $this->title_goldTime->setSizeX($scaledSizes[2]);
        $this->title_rank->setSizeX($scaledSizes[3]);
        $this->title_rating->setSizeX($scaledSizes[4]);
        $this->title_actions->setSizeX($scaledSizes[5]);

        if (is_object($this->btnRemoveAll))
            $this->btnRemoveAll->setPosition(3, 4.5);
    }

    function removeAllMaps($login) {
        $mapsAtServer = array();
        $maps = $this->connection->getMapList(-1, 0);

        foreach ($maps as $map) {
            $mapsAtServer[] = $map->fileName;
        }

        array_shift($mapsAtServer);

        try {
            $this->connection->RemoveMapList($mapsAtServer);
            $this->connection->chatSendServerMessage("Maplist cleared with:" . count($mapsAtServer) . " maps!", $login);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage("Oops, couldn't clear the map list. server said:" . $e->getMessage());
        }
    }

    function updateList($login, $column = null, $sortType = null, $maps = null) {

        if ($maps == null) {
            $maps = $this->storage->maps;
        } else {
            $this->title_mapName->setAction(null);
            $this->title_authorName->setAction(null);
            $this->title_rating->setAction(null);
            $this->title_rank->setAction(null);
            $this->title_goldTime->setAction(null);
        }

        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();


        $isAdmin = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'server_maps');

        $this->maps = array();

        foreach ($maps as $map) {
            $localrecord = "-";
            $rating = new \ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating(-1, 0);


            $maxrec = \ManiaLivePlugins\eXpansion\LocalRecords\Config::getInstance()->recordsCount;
            if (array_key_exists($map->uId, $this->records)) {
                if ($this->records[$map->uId] <= $maxrec)
                    $localrecord = $this->records[$map->uId] . "/" . $maxrec;
            }
            if (array_key_exists($map->uId, $this->ratings)) {
                $rating = $this->ratings[$map->uId];
            }
            $this->maps[] = new \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap($map, $localrecord, $rating);
        }

        if ($column !== null) {
            if ($column != Maps::$playerSortModes[$login]->column) {
                Maps::$playerSortModes[$login]->sortMode = 0;
                Maps::$playerSortModes[$login]->column = $column;
            } else {
                Maps::$playerSortModes[$login]->sortMode = (Maps::$playerSortModes[$login]->sortMode + 1) % 3;
            }
        }

        // select sorttype and sort the list
        if (Maps::$playerSortModes[$login]->sortMode == 1)
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc($this->maps, Maps::$playerSortModes[$login]->column);
        if (Maps::$playerSortModes[$login]->sortMode == 2) {
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->maps, Maps::$playerSortModes[$login]->column);
        }

        // add items to display
        $x = 0;
        foreach ($this->maps as $sortableMap) {
            $isHistory = false;
            if (array_key_exists($sortableMap->map->uId, $this->history)) {
                $isHistory = true;
            }
            $this->items[$x] = new Mapitem($x, $login, $sortableMap, $this, $isAdmin, $isHistory, $this->widths, $this->getSizeX());
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
        $this->redraw($this->getRecipient());
    }

    function setRecords($records) {
        self::$localrecordsLoaded = true;
        $this->records = $records;
    }

    /** @param \DedicatedApi\Structures\Map[] $history */
    function setHistory($history) {
        $this->history = array();
        foreach ($history as $map) {
            $this->history[$map->uId] = true;
        }        
    }

    function setRatings($ratings) {
        $this->ratingsLoaded = true;
        $this->ratings = $ratings;
    }

    function destroy() {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        if (is_object($this->btnRemoveAll))
            $this->btnRemoveAll->destroy();
        $this->pager->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
