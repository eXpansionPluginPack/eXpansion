<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap as Map;
use ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\MxMap;
use ManiaLive\Gui\ActionHandler;

class MxSearch extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    /** @var \ManiaLive\Gui\Controls\Pager */
    private $pager;

    /** @var  \DedicatedApi\Connection */
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
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
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
        $this->addDropdown("style", $items);
        $this->searchframe->addComponent($this->style);

        $items = array("All", "15sec", "30sec", "45sec", "1min");
        $this->lenght = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown("length", $items);
        $this->addDropdown("length", $items);
        $this->searchframe->addComponent($this->lenght);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(10, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->searchframe->addComponent($spacer);

        $this->actionSearch = ActionHandler::getInstance()->createAction(array($this, "actionOk"));


        $this->buttonSearch = new OkButton(24, 6);
        $this->buttonSearch->setText("Search");
        $this->buttonSearch->colorize('0f0');
        $this->buttonSearch->setScale(0.6);
        $this->buttonSearch->setAction($this->actionSearch);

        $this->searchframe->addComponent($this->buttonSearch);

        $this->mainFrame->addComponent($this->searchframe);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());

        $this->header = new \ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls\Header();
        $this->frame->addComponent($this->header);

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->frame->addComponent($this->pager);

        $this->mainFrame->addComponent($this->frame);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->frame->setSizeX($this->sizeX);
        $this->header->setSize($this->sizeX, 5);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setSize($this->sizeX, $this->sizeY - 22);
        $this->searchframe->setPosition(8, -$this->sizeY + 6);
    }

    public function setPlugin($plugin) {
        $this->mxPlugin = $plugin;
    }

    public function search($login, $trackname = "", $author = "", $style = null, $length = null) {

        if ($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_SCRIPT) {
            $script = $this->connection->getModeScriptInfo();
            $query = "";

            switch ($script->name) {
                case "Royal.Script.txt":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=RoyalArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=100&environments=1&tracksearch&api=on&format=json';
                    break;
                case "Melee.Script.txt":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=MeleeArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=100&environments=1&tracksearch&api=on&format=json';
                    break;
                case "Battle.Script.txt":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=BattleArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=100&environments=1&tracksearch&api=on&format=json';
                    break;
                case "Realm.Script.txt":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=RealmArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=100&environments=1&tracksearch&api=on&format=json';
                    break;
                case "Siege.Script.txt":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=SiegeArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=100&environments=1&tracksearch&api=on&format=json';
                    break;
                default:
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&mtype=All&tpack=All&priord=2&limit=100&environments=1&tracksearch&api=on&format=json';
                    break;
            }
        } else {
            $env = "";
            $info = $this->connection->getVersion();
            switch ($info->titleId) {
                case "TMCanyon":
                    $env = "1";
                    break;
                case "TMStadium":
                    $env = "2";
                    break;
                case "TMValley":
                    $env = "3";
                    break;
            }
            $out = "";
            if ($style != null) {
                $out .= "&style=" . $style;
            }
            if ($length != null) {
                $out .= "&length=" . $length . "&lengthop=0";
            }
            if (!empty($author))
                $out = "";
            $query = 'http://tm.mania-exchange.com/tracksearch?mode=0&vm=0&environments=' . $env . '&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . $out . '&mtype=All&priord=2&limit=100&tracksearch&api=on&format=json';
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
        $access->httpGet($query, Array($this, "xSearch"), null, "Manialive/eXpansion MXapi [search] ver 0.1", "application/json");
        if ($length !== null)
            $this->lenght->setSelected(intval($length) + 1);
        if ($style !== null)
            $this->style->setSelected(intval($style));
        return;
    }

    function xSearch($data) {
        if (!$data)
            return;
        $this->maps = Map::fromArrayOfArray(json_decode($data, true));

        foreach ($this->items as $item)
            $item->erase();

        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $login = $this->getRecipient();
        $isadmin = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'server_maps');

        foreach ($this->maps as $map) {
            $this->items[$x] = new MxMap($x, $map, $this, $isadmin, $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }

        $this->redraw();
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
        $this->header->destroy();
        $this->pager->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->searchframe->clearComponents();
        $this->searchframe->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
