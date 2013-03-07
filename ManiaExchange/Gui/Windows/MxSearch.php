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
    private $items = array();
    public static $mxPlugin;

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

        $this->inputAuthor = new Inputbox("author");
        $this->inputAuthor->setLabel("Author name");
        $this->searchframe->addComponent($this->inputAuthor);

        $this->actionSearch = ActionHandler::getInstance()->createAction(array($this, "actionOk"));

        $this->buttonSearch = new OkButton(16, 6);
        $this->buttonSearch->setText("Search");
        $this->buttonSearch->colorize('0f0');
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
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 30);
        $this->pager->setStretchContentX($this->sizeX);
        $this->frame->setPosition(8, -10);
        $this->searchframe->setPosition(8, -$this->sizeY + 6);
    }

    function onShow() {
        
    }

    public function search($login, $trackname, $author) {

        if ($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_SCRIPT) {
            $script = $this->connection->getModeScriptInfo();
            $query = "";

            switch ($script->name) {
                case "ShootMania\Royal":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=RoyalArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=40&environments=1&tracksearch&api=on&format=json';
                    break;
                case "ShootMania\Melee":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=MeleeArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=40&environments=1&tracksearch&api=on&format=json';
                    break;
                case "ShootMania\Battle":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=BattleArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=40&environments=1&tracksearch&api=on&format=json';
                    break;
                case "ShootMania\Elite":
                    $query = 'http://sm.mania-exchange.com/tracksearch?mode=0&vm=0&mtype=EliteArena&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&priord=2&limit=40&environments=1&tracksearch&api=on&format=json';
                    break;
                default:
                    $query = 'http://tm.mania-exchange.com/tracksearch?mode=0&vm=0&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&mtype=All&tpack=All&priord=2&limit=40&environments=1&tracksearch&api=on&format=json';
                    break;
            }
        } else {
            $env = "";
            $info = $this->connection->getVersion();
            switch ($info->titleId) {
                case "TMCanyon":
                    $env = "TMCanyon";
                    break;
                case "TMStadium":
                    $env = "TMStadium";
                    break;
            }

            $query = 'http://tm.mania-exchange.com/tracksearch?mode=0&vm=0&tpack=' . $env . '&trackname=' . rawurlencode($trackname) . '&author=' . rawurlencode($author) . '&mtype=All&priord=2&limit=40&tracksearch&api=on&format=json';
        }

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


        //print_r(json_decode($json, true));

        $this->maps = Map::fromArrayOfArray(json_decode($data, true));

        foreach ($this->items as $item)
            $item->destroy();

        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $login = $this->getRecipient();
        $isadmin = \ManiaLive\Features\Admin\AdminGroup::contains($login);

        foreach ($this->maps as $map) {
            $this->items[$x] = new MxMap($x, $map, $this, $isadmin);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
        $this->redraw();
    }

    function addMap($login, $mapId) {
        self::$mxPlugin->addMap($login, $mapId);
    }

    function actionOk($login, $args) {
        $this->search($login, $args['mapName'], $args['author']);
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->destroy();
        $this->items = null;
        $this->inputMapName->destroy();
        $this->inputAuthor->destroy();
        $this->buttonSearch->destroy();
        $this->header->destroy();
        ActionHandler::getInstance()->deleteAction($this->actionSearch);        
        $this->clearComponents();
        parent::destroy();
    }

}

?>
