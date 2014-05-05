<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem;
use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\MapitemCurrent;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Maps\Maps;
use ManiaLivePlugins\eXpansion\Gui\Gui;

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
    private $frame;
    protected $title_mapName, $title_envi;
    protected $title_authorName;
    protected $title_goldTime;
    protected $title_rank;
    protected $title_rating;
    protected $title_actions;
    protected $searchBox, $searchframe;
    protected $btn_search,  $btn_search2;
    private $actionRemoveAll;
    private $actionRemoveAllf;
    private $currentMap = null;
    private $titlebg;

    /** @var \ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating[] */
    private $ratings = array();

    /** @var  \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var  \ManiaLive\Data\Storage */
    private $storage;
    private $widths = array(5, 15, 4, 4, 3, 3, 3, .7);

    /** @var \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap[] */
    private $maps = array();

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $sizeX = 100;
        $scaledSizes = Gui::getScaledSize($this->widths, $sizeX);

        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \Maniaplanet\DedicatedServer\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->titlebg = new \ManiaLivePlugins\eXpansion\Gui\Elements\TitleBackGround($sizeX, 6);
        $this->mainFrame->addComponent($this->titlebg);


        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, 4);
        $this->frame->setAlign("left", "top");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->mainFrame->addComponent($this->frame);

        $textStyle = "TextCardRaceRank";
        $textColor = "000";
        $textSize = "1.5";

        $this->title_authorName = new \ManiaLib\Gui\Elements\Label();
        $this->title_authorName->setText(__("Author", $login));
        $this->title_authorName->setStyle($textStyle);
        $this->title_authorName->setTextColor($textColor);
        $this->title_authorName->setAction($this->createAction(array($this, "updateList"), "author"));
        $this->title_authorName->setTextSize($textSize);
        $this->frame->addComponent($this->title_authorName);

        $this->title_mapName = new \ManiaLib\Gui\Elements\Label();
        $this->title_mapName->setText(__("Map name", $login));
        $this->title_mapName->setStyle($textStyle);
        $this->title_mapName->setTextColor($textColor);
        $this->title_mapName->setAction($this->createAction(array($this, "updateList"), "name"));
        $this->title_mapName->setTextSize($textSize);


        $this->frame->addComponent($this->title_mapName);
	
	$this->title_envi = new \ManiaLib\Gui\Elements\Label();
        $this->title_envi->setText(__("Title", $login));
        $this->title_envi->setStyle($textStyle);
        $this->title_envi->setTextColor($textColor);
        $this->title_envi->setTextSize($textSize);
	$this->frame->addComponent($this->title_envi);

        $this->title_goldTime = new \ManiaLib\Gui\Elements\Label();
        $this->title_goldTime->setText(__("Length", $login));
        $this->title_goldTime->setStyle($textStyle);
        $this->title_goldTime->setTextColor($textColor);
        $this->title_goldTime->setAction($this->createAction(array($this, "updateList"), "goldTime"));
        $this->title_goldTime->setTextSize($textSize);
        $this->frame->addComponent($this->title_goldTime);

        $this->title_rank = new \ManiaLib\Gui\Elements\Label();
        $this->title_rank->setText(__("Record", $login));
        $this->title_rank->setAlign("center");
        $this->title_rank->setStyle($textStyle);
        $this->title_rank->setTextColor($textColor);
        $this->title_rank->setAction($this->createAction(array($this, "updateList"), "localrecord"));
        $this->title_rank->setTextSize($textSize);
        $this->frame->addComponent($this->title_rank);

        $this->title_rating = new \ManiaLib\Gui\Elements\Label();
        $this->title_rating->setText(__("Rating", $login));
        $this->title_rating->setAlign("center");
        $this->title_rating->setStyle($textStyle);
        $this->title_rating->setTextColor($textColor);
        $this->title_rating->setAction($this->createAction(array($this, "updateList"), "rating"));

        $this->title_rating->setTextSize($textSize);
        $this->frame->addComponent($this->title_rating);

        $this->title_actions = new \ManiaLib\Gui\Elements\Label();
        $this->title_actions->setText(__("Actions", $login));

        $this->title_actions->setTextSize($textSize);
        $this->title_actions->setTextColor($textColor);
        $this->title_actions->setStyle($textStyle);
        $this->frame->addComponent($this->title_actions);

        $this->searchframe = new \ManiaLive\Gui\Controls\Frame();
        $this->searchframe->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->searchframe->setAlign("right", "top");
        $this->addComponent($this->searchframe);

        $this->searchBox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("searchbox");
        $this->searchBox->setLabel(__("Search maps", $login));
        $this->searchframe->addComponent($this->searchBox);

        $spacer = new \ManiaLib\Gui\Elements\Spacer(3, 4);
        $this->searchframe->addComponent($spacer);

        $this->btn_search = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(40);
        $this->btn_search->setAction($this->createAction(array($this, "doSearchMap")));
        $this->btn_search->setText(__("Search Map", $login));
        $this->btn_search->colorize('0a0');
        $this->searchframe->addComponent($this->btn_search);
        
        $this->btn_search2 = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(40);
        $this->btn_search2->setAction($this->createAction(array($this, "doSearchAuthor")));
        $this->btn_search2->setText(__("Search Author", $login));
        $this->btn_search2->colorize('0a0');
        $this->searchframe->addComponent($this->btn_search2);

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\OptimizedPager();
        $this->mainFrame->addComponent($this->pager);

        if (array_key_exists($login, Maps::$playerSortModes) == false) {
            Maps::$playerSortModes[$login] = new \ManiaLivePlugins\eXpansion\Maps\Structures\MapSortMode();
        }

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'server_maps')) {
            $this->actionRemoveAllf = $this->createAction(array($this, "removeAllMaps"));	    
            $this->actionRemoveAll = Gui::createConfirm($this->actionRemoveAllf);	    
            $this->btnRemoveAll = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(35);
            $this->btnRemoveAll->setAction($this->actionRemoveAll);
            $this->btnRemoveAll->setText('$d00' . __("Clear Maplist", $login));
            $this->btnRemoveAll->setScale(0.5);
            $this->mainFrame->addComponent($this->btnRemoveAll);
        }
    }

    static function Initialize($mapsPlugin) {
        self::$mapsPlugin = $mapsPlugin;
    }

    function gotoMap($login, \Maniaplanet\DedicatedServer\Structures\Map $map) {
        self::$mapsPlugin->gotoMap($login, $map);
        $this->Erase($this->getRecipient());
    }

    function removeMap($login, \Maniaplanet\DedicatedServer\Structures\Map $map) {
        self::$mapsPlugin->removeMap($login, $map);
        $this->RedrawAll();
    }

    function queueMap($login, \Maniaplanet\DedicatedServer\Structures\Map $map) {
        self::$mapsPlugin->playerQueueMap($login, $map, false);
    }

    function showRec($login, \Maniaplanet\DedicatedServer\Structures\Map $map) {
        self::$mapsPlugin->showRec($login, $map);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);

        $this->searchframe->setPosition(12, -7);

        $this->pager->setSize($this->getSizeX() - 6, $this->getSizeY() - 20);

        $this->pager->setPosition(3, -17);

        $this->titlebg->setPosition(3, -9.5);
        $this->titlebg->setSize($this->getSizeX() - 6, 6.5);
        $this->frame->setPosition(3, -8.25);

        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() - 10));


        $this->title_authorName->setSizeX($scaledSizes[0]);
        $this->title_mapName->setSizeX($scaledSizes[1]);
        $this->title_envi->setSizeX($scaledSizes[2]);
        $this->title_goldTime->setSizeX($scaledSizes[3]);
        $this->title_rank->setSizeX($scaledSizes[4]);
        $this->title_rating->setSizeX($scaledSizes[5]);
        $this->title_actions->setSizeX($scaledSizes[6]);



        if (is_object($this->btnRemoveAll))
            $this->btnRemoveAll->setPosition($this->getSizeX() - 25, 0);
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

        $this->pager->clearItems();

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

        $this->items = array();


        $isAdmin = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'server_maps');

        $this->maps = array();

        $maxrec = \ManiaLivePlugins\eXpansion\LocalRecords\Config::getInstance()->recordsCount;

        foreach ($maps as $map) {
            $localrecord = "-";
            $rating = new \ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating(-1, 0);
            if (array_key_exists($map->uId, $this->records)) {
                if ($this->records[$map->uId] <= $maxrec)
                    $localrecord = $this->records[$map->uId];
            }
            if (array_key_exists($map->uId, $this->ratings)) {
                $rating = $this->ratings[$map->uId];
            }

            if (!empty(Maps::$searchTerm[$login])) {
                $field = Maps::$searchField[$login];
                $substring = $this->shortest_edit_substring(Maps::$searchTerm[$login], \ManiaLib\Utils\Formatting::stripStyles($map->{$field}));
                $dist = $this->edit_distance(Maps::$searchTerm[$login], $substring);
                if (!empty($substring) && $dist < 2) {
                    $this->maps[] = new \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap($map, $localrecord, $maxrec, $rating);
                }
            } else {
                $this->maps[] = new \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap($map, $localrecord, $maxrec, $rating);
            }
        }
	

        if ($column !== null) {
            if ($column != Maps::$playerSortModes[$login]->column) {
                Maps::$playerSortModes[$login]->sortMode = 1;
                Maps::$playerSortModes[$login]->column = $column;
            } else {
                Maps::$playerSortModes[$login]->sortMode = (Maps::$playerSortModes[$login]->sortMode + 1) % 3;
            }
        }

        // select sorttype and sort the list
        $sortmode = SORT_STRING;
        switch (Maps::$playerSortModes[$login]->column) {
            case "rating":
            case "localrecord":
            case "goldTime":
                $sortmode = SORT_NUMERIC;
                break;
        }



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

            $queueMapAction = $this->createAction(array($this, 'queueMap'), $sortableMap->map);
            $removeMapAction = $this->createAction(array($this, 'removeMap'), $sortableMap->map);
            $showRecsAction = $this->createAction(array($this, 'showRec'), $sortableMap->map);

            $rate = ($sortableMap->rating->rating / 5) * 100;
            $rate = round($rate) . "%";
            if ($sortableMap->rating->rating == -1)
                $rate = " - ";

            $this->pager->addSimpleItems(array(Gui::fixHyphens($sortableMap->map->name) => $queueMapAction,
		Gui::fixHyphens($sortableMap->map->author) => -1,
		$sortableMap->map->environnement => -1,
                \ManiaLive\Utilities\Time::fromTM($sortableMap->goldTime) => -1,
                $sortableMap->localrecord => -1,
                $rate => -1,
                "Recs" => $showRecsAction,
                "remove" => $removeMapAction
            ));

            /* if ($sortableMap->map->uId == $this->currentMap->uId) {
              $this->items[$x] = new MapitemCurrent($x, $login, $sortableMap, $this, $isAdmin, $isHistory, $this->widths, $this->getSizeX());
              } else {
              $this->items[$x] = new Mapitem($x, $login, $sortableMap, $this, $isAdmin, $isHistory, $this->widths, $this->getSizeX());
              }
              $this->pager->addItem($this->items[$x]); */
            $x++;
        }

        Mapitem::$ColumnWidths = $this->widths;
        $this->pager->setContentLayout('\ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem');
        $this->pager->update($this->getRecipient());        
        $this->redraw($this->getRecipient());
    }

    function setRecords($records) {
        self::$localrecordsLoaded = true;
        $this->records = $records;
    }

    /** @param \Maniaplanet\DedicatedServer\Structures\Map[] $history */
    function setHistory($history) {
        $this->history = array();
        foreach ($history as $map) {
            $this->history[$map->uId] = true;
        }
    }

    function setCurrentMap(\Maniaplanet\DedicatedServer\Structures\Map $map) {
        $this->currentMap = $map;
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
	
	 ActionHandler::getInstance()->deleteAction($this->actionRemoveAll);
	 ActionHandler::getInstance()->deleteAction($this->actionRemoveAllf);
	
        parent::destroy();
    }

    function doSearchMap($login, $entries) {
        Maps::$searchTerm[$login] = $entries['searchbox'];
        Maps::$searchField[$login]= "name";
        $this->updateList($login);
        $this->redraw($login);
    }

    function doSearchAuthor($login, $entries) {
        Maps::$searchTerm[$login] = $entries['searchbox'];
        Maps::$searchField[$login]= "author";
        $this->updateList($login);
        $this->redraw($login);
    }
    
    // utility function - returns the key of the array minimum
    function array_min_key($arr) {
        $min_key = null;
        $min = PHP_INT_MAX;
        foreach ($arr as $k => $v) {
            if ($v < $min) {
                $min = $v;
                $min_key = $k;
            }
        }
        return $min_key;
    }

    /*
      Following code is from experts-exchange answer:
     */

    // Calculate the edit distance between two strings
    function edit_distance($string1, $string2) {
        $m = strlen($string1);
        $n = strlen($string2);
        $d = array();

        // the distance from '' to substr(string,$i)
        for ($i = 0; $i <= $m; $i++)
            $d[$i][0] = $i;
        for ($i = 0; $i <= $n; $i++)
            $d[0][$i] = $i;

        // fill-in the edit distance matrix
        for ($j = 1; $j <= $n; $j++) {
            for ($i = 1; $i <= $m; $i++) {
                // Using, for example, the levenshtein distance as edit distance
                list($p_i, $p_j, $cost) = $this->levenshtein_weighting($i, $j, $d, $string1, $string2);
                $d[$i][$j] = $d[$p_i][$p_j] + $cost;
            }
        }

        return $d[$m][$n];
    }

// Helper function for edit_distance()
    function levenshtein_weighting($i, $j, $d, $string1, $string2) {
        // if the two letters are equal, cost is 0
        if ($string1[$i - 1] === $string2[$j - 1]) {
            return array($i - 1, $j - 1, 0);
        }

        // cost we assign each operation
        $cost['delete'] = 1;
        $cost['insert'] = 1;
        $cost['substitute'] = 1;

        // cost of operation + cost to get to the substring we perform it on
        $total_cost['delete'] = $d[$i - 1][$j] + $cost['delete'];
        $total_cost['insert'] = $d[$i][$j - 1] + $cost['insert'];
        $total_cost['substitute'] = $d[$i - 1][$j - 1] + $cost['substitute'];

        // return the parent array keys of $d and the operation's cost
        $min_key = $this->array_min_key($total_cost);
        if ($min_key == 'delete') {
            return array($i - 1, $j, $cost['delete']);
        } elseif ($min_key == 'insert') {
            return array($i, $j - 1, $cost['insert']);
        } else {
            return array($i - 1, $j - 1, $cost['substitute']);
        }
    }

// attempt to find the substring of $haystack most closely matching $needle
    function shortest_edit_substring($needle, $haystack) {
        // initialize edit distance matrix
        $m = strlen($needle);
        $n = strlen($haystack);
        $d = array();
        for ($i = 0; $i <= $m; $i++) {
            $d[$i][0] = $i;
            $backtrace[$i][0] = null;
        }
        // instead of strlen, we initialize the top row to all 0's
        for ($i = 0; $i <= $n; $i++) {
            $d[0][$i] = 0;
            $backtrace[0][$i] = null;
        }

        // same as the edit_distance calculation, but keep track of how we got there
        for ($j = 1; $j <= $n; $j++) {
            for ($i = 1; $i <= $m; $i++) {
                list($p_i, $p_j, $cost) = $this->levenshtein_weighting($i, $j, $d, $needle, $haystack);
                $d[$i][$j] = $d[$p_i][$p_j] + $cost;
                $backtrace[$i][$j] = array($p_i, $p_j);
            }
        }

        // now find the minimum at the bottom row
        $min_key = $this->array_min_key($d[$m]);
        $current = array($m, $min_key);
        $parent = $backtrace[$m][$min_key];

        // trace up path to the top row
        while (!is_null($parent)) {
            $current = $parent;
            $parent = $backtrace[$current[0]][$current[1]];
        }

        // and take a substring based on those results
        $start = $current[1];
        $end = $min_key;
        return substr($haystack, $start, $end - $start);
    }

}

?>
