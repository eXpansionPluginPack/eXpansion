<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Spacer;
use ManiaLib\Gui\Layouts\Line;
use ManiaLib\Utils\Formatting;
use ManiaLive\Data\Player;
use ManiaLive\Data\Storage;
use ManiaLive\DedicatedApi\Callback\Event;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\ActionHandler;
use ManiaLive\Gui\Controls\Frame;
use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\OptimizedPager;
use ManiaLivePlugins\eXpansion\Gui\Elements\TitleBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;
use ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem;
use ManiaLivePlugins\eXpansion\Maps\Maps;
use ManiaLivePlugins\eXpansion\Maps\Structures\DbMap;
use ManiaLivePlugins\eXpansion\Maps\Structures\MapSortMode;
use Maniaplanet\DedicatedServer\Structures\Map;

class Maplist extends Window
{
    public $records = array();

    protected $history = array();
    protected $items = array();

    public static $localrecordsLoaded = false;
    /** @var OptimizedPager */
    protected $pager;
    /** @var  Button */
    protected $btnRemoveAll;
    /** @var  Button */
    protected $btnUpdate;
    /** @var  Frame */
    protected $frame;
    /** @var  Label */
    protected $title_mapName;
    /** @var  Label */
    protected $title_envi;
    /** @var  Label */
    protected $title_authorName;
    /** @var  Label */
    protected $title_goldTime;
    /** @var  Label */
    protected $title_rank;
    /** @var  Label */
    protected $title_rating;
    /** @var  Label */
    protected $title_difficulty;
    /** @var  Label */
    protected $title_style;
    /** @var  Label */
    protected $title_actions;
    /** @var  Inputbox */
    protected $searchBox;

    /** @var  Frame */
    protected $searchFrame;
    /** @var  Button */
    protected $btn_search;
    /** @var  Button */
    protected $btn_search2;
    /** @var  Button */
    protected $btn_sortNewest;
    protected $actionRemoveAll;
    protected $actionRemoveAllf;

    protected $actionUpdateF;
    protected $actionUpdate;

    /** @var Map */
    protected $currentMap = null;

    /** @var  TitleBackGround */
    protected $titlebg;

    /** @var  DbMap[] */
    protected $mxInfo;

    /** @var  \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var  \ManiaLive\Data\Storage */
    protected $storage;
    protected $widths = array(6, 12, 4, 4, 4, 4, 4, 3, 4);
    protected $actions = array();

    /** @var Map */
    protected $maps = array();

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();
        $sizeX = 100;

        $this->connection = Singletons::getInstance()->getDediConnection();

        $this->titlebg = new TitleBackGround($sizeX, 6);
        $this->mainFrame->addComponent($this->titlebg);


        $this->frame = new Frame();
        $this->frame->setSize($sizeX, 4);
        $this->frame->setAlign("left", "top");
        $this->frame->setLayout(new Line());
        $this->mainFrame->addComponent($this->frame);

        $textStyle = "TextCardRaceRank";
        $textColor = "000";
        $textSize = "1.5";

        $this->title_authorName = new Label();
        $this->title_authorName->setText(__("Author", $login));
        $this->title_authorName->setStyle($textStyle);
        $this->title_authorName->setTextColor($textColor);
        $this->title_authorName->setAction($this->createAction(array($this, "updateList"), "author"));
        $this->title_authorName->setTextSize($textSize);
        $this->frame->addComponent($this->title_authorName);

        $this->title_mapName = new Label();
        $this->title_mapName->setText(__("Map name", $login));
        $this->title_mapName->setStyle($textStyle);
        $this->title_mapName->setTextColor($textColor);
        $this->title_mapName->setAction($this->createAction(array($this, "updateList"), "name"));
        $this->title_mapName->setTextSize($textSize);
        $this->frame->addComponent($this->title_mapName);

        $this->title_envi = new Label();
        $this->title_envi->setText(__("Title", $login));
        $this->title_envi->setStyle($textStyle);
        $this->title_envi->setTextColor($textColor);
        $this->title_envi->setTextSize($textSize);
        $this->frame->addComponent($this->title_envi);

        $this->title_goldTime = new Label();
        $this->title_goldTime->setText(__("Length", $login));
        $this->title_goldTime->setStyle($textStyle);
        $this->title_goldTime->setTextColor($textColor);
        $this->title_goldTime->setAction($this->createAction(array($this, "updateList"), "goldTime"));
        $this->title_goldTime->setTextSize($textSize);
        $this->frame->addComponent($this->title_goldTime);

        $this->title_rank = new Label();
        $this->title_rank->setText(__("Record", $login));
        $this->title_rank->setAlign("center");
        $this->title_rank->setStyle($textStyle);
        $this->title_rank->setTextColor($textColor);
        $this->title_rank->setAction($this->createAction(array($this, "updateList"), "localrecord"));
        $this->title_rank->setTextSize($textSize);
        $this->frame->addComponent($this->title_rank);

        $this->title_rating = new Label();
        $this->title_rating->setText(__("Rating", $login));
        $this->title_rating->setAlign("center");
        $this->title_rating->setStyle($textStyle);
        $this->title_rating->setTextColor($textColor);
        $this->title_rating->setAction($this->createAction(array($this, "updateList"), "rating"));
        $this->title_rating->setTextSize($textSize);
        $this->frame->addComponent($this->title_rating);

        $this->title_difficulty = new Label();
        $this->title_difficulty->setText(__("Difficulty", $login));
        $this->title_difficulty->setAlign("left");
        $this->title_difficulty->setStyle($textStyle);
        $this->title_difficulty->setTextColor($textColor);
        $this->title_difficulty->setTextSize($textSize);
        $this->frame->addComponent($this->title_difficulty);

        $this->title_style = new Label();
        $this->title_style->setText(__("Style", $login));
        $this->title_style->setAlign("center");
        $this->title_style->setStyle($textStyle);
        $this->title_style->setTextColor($textColor);
        $this->title_style->setTextSize($textSize);
        $this->frame->addComponent($this->title_style);

        $this->title_actions = new Label();
        $this->title_actions->setText(__("Actions", $login));

        $this->title_actions->setTextSize($textSize);
        $this->title_actions->setTextColor($textColor);
        $this->title_actions->setStyle($textStyle);
        $this->frame->addComponent($this->title_actions);

        $this->searchFrame = new Frame();
        $this->searchFrame->setLayout(new Line());
        $this->searchFrame->setAlign("right", "top");
        $this->addComponent($this->searchFrame);

        $this->searchBox = new Inputbox("searchbox");
        $this->searchBox->setLabel(__("Search maps", $login));
        $this->searchFrame->addComponent($this->searchBox);

        $spacer = new Spacer(3, 4);
        $this->searchFrame->addComponent($spacer);

        $this->btn_search = new Button(40);
        $this->btn_search->setAction($this->createAction(array($this, "doSearchMap")));
        $this->btn_search->setText(__("Search Map", $login));
        $this->btn_search->colorize('0a0');
        $this->searchFrame->addComponent($this->btn_search);

        $this->btn_search2 = new Button(40);
        $this->btn_search2->setAction($this->createAction(array($this, "doSearchAuthor")));
        $this->btn_search2->setText(__("Search Author", $login));
        $this->btn_search2->colorize('0a0');
        $this->searchFrame->addComponent($this->btn_search2);

        $this->btn_sortNewest = new Button(40);
        $this->btn_sortNewest->setAction($this->createAction(array($this, "updateList"), "addTime"));
        $this->btn_sortNewest->setText(__("Sort By Add Date", $login));
        $this->searchFrame->addComponent($this->btn_sortNewest);

        $this->pager = new OptimizedPager();
        $this->mainFrame->addComponent($this->pager);

        if (array_key_exists($login, Maps::$playerSortModes) == false) {
            Maps::$playerSortModes[$login] = new MapSortMode();
        }

        if (AdminGroups::hasPermission($login, Permission::MAP_REMOVE_MAP)) {
            $this->actionRemoveAllf = $this->createAction(array($this, "removeAllMaps"));
            $this->actionRemoveAll = Gui::createConfirm($this->actionRemoveAllf);
            $this->btnRemoveAll = new Button(35);
            $this->btnRemoveAll->setAction($this->actionRemoveAll);
            $this->btnRemoveAll->setText(__("Clear Maplist", $login));
            $this->btnRemoveAll->colorize('d00');
            $this->mainFrame->addComponent($this->btnRemoveAll);
        }

        if (AdminGroups::hasPermission($login, Permission::MAP_ADD_MX)) {
            $this->actionUpdateF = $this->createAction(array($this, "mxUpdate"));
            $this->actionUpdate = Gui::createConfirm($this->actionUpdateF);
            $this->btnUpdate = new Button(35);
            $this->btnUpdate->setAction($this->actionUpdate);
            $this->btnUpdate->setText(__("Fetch Map Style", $login));
            $this->btnUpdate->colorize('0a0');
            $this->mainFrame->addComponent($this->btnUpdate);
        }

    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);

        $this->searchFrame->setPosition(2, -7);

        $this->pager->setSize($this->getSizeX() - 2, $this->getSizeY() - 20);

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
        $this->title_difficulty->setSizeX($scaledSizes[5]);
        $this->title_style->setSizeX($scaledSizes[6]);
        $this->title_rating->setSizeX($scaledSizes[7]);
        $this->title_actions->setSizeX($scaledSizes[8]);

        if (is_object($this->btnRemoveAll)) {
            $this->btnRemoveAll->setPosition($this->getSizeX() - 25, 2);
        }
        if (is_object($this->btnUpdate)) {
            $this->btnUpdate->setPosition($this->getSizeX() - 25, -3);
        }

    }

    public function removeAllMaps($login)
    {
        $mapsAtServer = array();

        foreach ($this->maps as $map) {
            $mapsAtServer[] = $map->fileName;
        }

        array_shift($mapsAtServer);

        try {
            $this->connection->RemoveMapList($mapsAtServer);
            $this->connection->chatSendServerMessage("Maplist cleared with:" . count($mapsAtServer) . " maps!", $login);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(
                "Oops, couldn't clear the map list. server said:" . $e->getMessage()
            );
        }
    }

    /**
     * @param $login
     * @param null $column
     */
    public function updateList($login, $column = null)
    {

        $this->pager->clearItems();

        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = array();

        if ($column !== null) {
            if ($column != Maps::$playerSortModes[$login]->column) {
                Maps::$playerSortModes[$login]->sortMode = 1;
                Maps::$playerSortModes[$login]->column = $column;
            } else {
                Maps::$playerSortModes[$login]->sortMode = (Maps::$playerSortModes[$login]->sortMode + 1) % 3;
            }
        }

        // select sorttype and sort the list
        $removePermission = AdminGroups::hasPermission($login, Permission::MAP_REMOVE_MAP);
        $adminPermission = AdminGroups::hasPermission($login, Permission::SERVER_ADMIN);

        switch (Maps::$playerSortModes[$login]->column) {
            case "rating":
                if (Maps::$playerSortModes[$login]->sortMode == 1) {
                    self::sortByRankingDesc($this->maps);
                }
                if (Maps::$playerSortModes[$login]->sortMode == 2) {
                    self::sortByRankingAsc($this->maps);
                }
                break;
            case "localrecord":
                if (Maps::$playerSortModes[$login]->sortMode == 1) {
                    ArrayOfObj::asortAsc($this->maps, "localrecord");
                }
                if (Maps::$playerSortModes[$login]->sortMode == 2) {
                    ArrayOfObj::asortDesc($this->maps, "localrecord");
                }
                break;
            case "name":
                if (Maps::$playerSortModes[$login]->sortMode == 1) {
                    ArrayOfObj::asortAsc($this->maps, "strippedName");
                }
                if (Maps::$playerSortModes[$login]->sortMode == 2) {
                    ArrayOfObj::asortDesc($this->maps, "strippedName");
                }
                break;
            default:
                if (Maps::$playerSortModes[$login]->sortMode == 1) {
                    ArrayOfObj::asortAsc(
                        $this->maps,
                        Maps::$playerSortModes[$login]->column
                    );
                }
                if (Maps::$playerSortModes[$login]->sortMode == 2) {
                    ArrayOfObj::asortDesc(
                        $this->maps,
                        Maps::$playerSortModes[$login]->column
                    );
                }
                break;
        }

        // add items to display
        $x = 0;

        foreach ($this->maps as $sortableMap) {

            if (!empty(Maps::$searchTerm[$login])) {
                $field = Maps::$searchField[$login];

                if ($field == "name") {
                    $field = "strippedName";
                }

                $substring = $this->shortest_edit_substring(
                    Maps::$searchTerm[$login],
                    Formatting::stripStyles($sortableMap->{$field})
                );
                $dist = $this->edit_distance(Maps::$searchTerm[$login], $substring);
                if (!empty($substring) && $dist < 2) {

                } else {
                    continue;
                }
            }


            if (isset($sortableMap->mapRating)) {
                $rate = ($sortableMap->mapRating->rating / 5) * 100;
                $rate = round($rate) . "%" . '  $n' . "(" . $sortableMap->mapRating->totalvotes . ")";
                if ($sortableMap->mapRating->rating == -1) {
                    $rate = " -";
                }
            } else {
                $rate = " -";
            }

            $localrecord = "-";
            if (isset($sortableMap->localrecord)) {
                $localrecord = $sortableMap->localrecord + 1;
            }

            if ($sortableMap->uId == $this->currentMap->uId) {
                $name = '$0d0' . $sortableMap->strippedName;
                $author = '$0d0' . $sortableMap->author;
                $color = '$0d0';
            } else if (isset($this->history[$sortableMap->uId])) {
                $name = '$d00' . $sortableMap->strippedName;
                $author = '$d00' . $sortableMap->author;
                $color = '$d00';
            } else {
                $name = $sortableMap->name;
                $author = $sortableMap->author;
                $color = '$fff';
            }

            $diff = " - ";
            if (isset($this->mxInfo[$sortableMap->uId]) && $this->mxInfo[$sortableMap->uId]->difficultyName != "") {
                $diff = $this->mxInfo[$sortableMap->uId]->difficultyName;
            }

            $style = "- ";
            if (isset($this->mxInfo[$sortableMap->uId]) && $this->mxInfo[$sortableMap->uId]->styleName != "") {
                $style = $this->mxInfo[$sortableMap->uId]->styleName;

            }

            $array = array(
                Gui::fixString($name) => $sortableMap->queueMapAction,
                Gui::fixString($author) => -1,
                $color . $sortableMap->environnement => -1,
                $color . Time::fromTM($sortableMap->goldTime) => -1,
                $color . $localrecord => -1,
                $color . $rate => -1,
                $color . $diff => -1,
                $color . $style => -1,
                "Info" => $sortableMap->showInfoAction,
                "Recs" => $sortableMap->showRecsAction,
                "x" => $removePermission ? $sortableMap->removeMapAction : -1,
                "Tag" => $adminPermission ? $sortableMap->showTagAction : -1
            );

            $this->pager->addSimpleItems($array);
            $x++;
        }

        Mapitem::$ColumnWidths = $this->widths;
        $this->pager->setContentLayout('\ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem');
        $this->pager->update($this->getRecipient());
        $this->redraw($this->getRecipient());
    }


    public function setRecords($records)
    {
        self::$localrecordsLoaded = true;
        foreach ($records as $uid => $record) {
            if (isset($this->maps[$uid])) {
                $this->maps[$uid]->localrecord = $record;
            }
        }

    }

    /** @param Map[] $history */
    public function setHistory($history)
    {
        $this->history = array();
        foreach ($history as $map) {
            $this->history[$map->uId] = true;
        }
    }

    public function setRatings($ratings)
    {
        foreach ($ratings as $uid => $rating) {
            if (isset($this->maps[$uid])) {
                $this->maps[$uid]->mapRating = $rating;
            }
        }
    }

    public function setMaps($maps)
    {
        $this->maps = $maps;
    }

    public function setCurrentMap(Map $map)
    {
        $this->currentMap = $map;
    }

    public function destroy()
    {
        /** @var ActionHandler $ah */
        $ah = ActionHandler::getInstance();
        foreach ($this->actions as $action) {
            $ah->deleteAction($action);
        }

        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        if (is_object($this->btnRemoveAll)) {
            $this->btnRemoveAll->destroy();
        }
        $this->pager->destroy();
        $this->destroyComponents();

        ActionHandler::getInstance()->deleteAction($this->actionRemoveAll);
        ActionHandler::getInstance()->deleteAction($this->actionUpdate);

        parent::destroy();
    }

    public function doSearchMap($login, $entries)
    {
        Maps::$searchTerm[$login] = $entries['searchbox'];
        Maps::$searchField[$login] = "name";
        $this->updateList($login);
        $this->redraw($login);
    }

    public function mxUpdate($login)
    {
        /** @var Player $player */
        $player = Storage::getInstance()->getPlayerObject($login);
        Dispatcher::dispatch(new Event("PlayerChat", array($player->playerId, $login, "/mx update", true)));
    }


    public function doSearchAuthor($login, $entries)
    {
        Maps::$searchTerm[$login] = $entries['searchbox'];
        Maps::$searchField[$login] = "author";
        $this->updateList($login);
        $this->redraw($login);
    }

    // utility function - returns the key of the array minimum
    public function array_min_key($arr)
    {
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
    public function edit_distance($string1, $string2)
    {
        $m = strlen($string1);
        $n = strlen($string2);
        $d = array();

        // the distance from '' to substr(string,$i)
        for ($i = 0; $i <= $m; $i++) {
            $d[$i][0] = $i;
        }
        for ($i = 0; $i <= $n; $i++) {
            $d[0][$i] = $i;
        }

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
    public function levenshtein_weighting($i, $j, $d, $string1, $string2)
    {
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
    public function shortest_edit_substring($needle, $haystack)
    {
        // initialize edit distance matrix
        $m = strlen($needle);
        $n = strlen($haystack);
        $d = array();
        $backtrace = array();
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

    public function setMxInfo($maps)
    {
        $this->mxInfo = $maps;
    }


    protected static function sortByRankingAsc(&$array)
    {
        usort(
            $array,
            function ($a, $b) {
                if (!isset($a->mapRating) && !isset($b->mapRating)) {
                    return 0;
                } elseif (!isset($a->mapRating)) {
                    return -1;
                } elseif (!isset($b->mapRating)) {
                    return 1;
                } else {
                    return $a->mapRating->rating > $b->mapRating->rating ? 1 : -1;
                }
            }
        );
    }

    protected static function sortByRankingDesc(&$array)
    {
        usort(
            $array,
            function ($a, $b) {
                if (!isset($a->mapRating) && !isset($b->mapRating)) {
                    return 0;
                } elseif (!isset($a->mapRating)) {
                    return 1;
                } elseif (!isset($b->mapRating)) {
                    return -1;
                } else {
                    return $a->mapRating->rating > $b->mapRating->rating ? -1 : 1;
                }
            }
        );
    }

}
