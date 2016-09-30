<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class OptimizedPager extends \ManiaLivePlugins\eXpansion\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer
{

    protected $frame;

    protected $clickAction;

    protected $iitems = array();

    protected $data = array();

    protected $scroll;
    protected $bg;
    protected $scrollBg;

    protected $scrollDown;
    protected $scrollUp;

    protected $myScript;

    protected $ContentLayout;

    protected $nbElemParColumn;

    protected $index = 0;

    protected $rowPerPage = 1;

    function __construct()
    {

        $config = Config::getInstance();

        $this->bg = new \ManiaLib\Gui\Elements\Quad();
        $this->bg->setBgcolor('$f00');
        $this->bg->setId("menuBg");
        $this->bg->setScriptEvents();
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->frame->setId("Pager");
        $this->frame->setScriptEvents();
        $this->addComponent($this->frame);
        $this->clickAction = $this->createAction(array($this, "handleClick"));

        $this->scrollBg = new \ManiaLib\Gui\Elements\Quad(4, 40);
        $this->scrollBg->setAlign("center", "top");
        $this->scrollBg->setStyle("Bgs1InRace");
        $this->scrollBg->setSubStyle('BgPlayerCard');
        $this->scrollBg->setId("ScrollBg");
        $this->scrollBg->setOpacity(0.9);
        $this->addComponent($this->scrollBg);

        $this->scroll = new \ManiaLib\Gui\Elements\Quad(3, 15);
        $this->scroll->setAlign("center", "top");
        $this->scroll->setStyle("BgsPlayerCard");
        $this->scroll->setSubStyle('BgRacePlayerName');
        $this->scroll->setId("ScrollBar");
        $this->scroll->setScriptEvents();
        $this->addComponent($this->scroll);

        $this->scrollDown = new \ManiaLib\Gui\Elements\Quad(6.5, 6.5);
        $this->scrollDown->setAlign("center", "top");
        $this->scrollDown->setStyle("Icons64x64_1");
        $this->scrollDown->setSubStyle("ArrowDown");
        $this->scrollDown->setId("ScrollDown");
        $this->scrollDown->setScriptEvents();
        $this->addComponent($this->scrollDown);

        $this->scrollUp = new \ManiaLib\Gui\Elements\Quad(6.5, 6.5);
        $this->scrollUp->setAlign("center", "bottom");
        $this->scrollUp->setStyle("Icons64x64_1");
        $this->scrollUp->setSubStyle("ArrowUp");
        $this->scrollUp->setId("ScrollUp");
        $this->scrollUp->setScriptEvents();
        $this->addComponent($this->scrollUp);

        $this->xml = new \ManiaLive\Gui\Elements\Xml();

        $entry = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("item");
        $entry->setId("entry");
        $entry->setScriptEvents();
        $entry->setPosition(900, 900);
        $this->addComponent($entry);

        $this->myScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\OptimizedPager");
    }

    function handleClick($login, $entries)
    {
        if (!empty($entries['item'])) {
            // do some magic
            $player = \ManiaLive\Data\Storage::getInstance()->getPlayerObject($login);
            \ManiaLive\Gui\ActionHandler::getInstance()->onPlayerManialinkPageAnswer(
                intval($player->playerId),
                $login,
                intval($entries['item']),
                array()
            );
        }
    }

    function clearItems()
    {
        $this->iitems = array();
        $this->data = array();
        $this->index = 0;
    }

    public function addSimpleItems($items)
    {
        foreach ($items as $text => $action) {
            $this->iitems[$this->index][] = '"' . Gui::fixString($text) . '"';
            $this->data[$this->index][] = '"' . Gui::fixString($action) . '"';
        }
        $this->index++;
    }

    function setSize()
    {
        $args = func_get_args();
        $this->sizeX = $args[0];
        $this->sizeY = $args[1];
        $this->scroll->setPosition($this->sizeX - 3, 0);
        $this->scrollBg->setPosition($this->sizeX - 3);
        $this->scrollBg->setSizeY($this->sizeY - 4);

        $this->scrollDown->setPosition($this->sizeX - 3, -($this->sizeY - 5));
        $this->scrollUp->setPosition($this->sizeX - 3, -1);
    }

    public function setContentLayout($className)
    {
        $this->ContentLayout = $className;
    }

    public function update($login)
    {

        $className = $this->ContentLayout;
        $layout = new $className(0, $login, $this->clickAction);

        $sizeY = $layout->getSizeY() * ($layout->getScale() == 0.0 ? 1 : $layout->getScale());

        $this->frame->destroyComponents();
        $layout = null;

        $limit = (int)($this->getSizeY() / $sizeY);

        for ($x = 0; $x < $limit; $x++) {
            $className = $this->ContentLayout;
            $layout = new $className($x, $login, $this->clickAction);
            $this->frame->addComponent($layout);
        }
        $this->rowPerPage = $limit;
        $this->nbElemParColumn = $layout->getNbTextColumns();
    }

    public function destroy()
    {
        if (isset($this->frame) && $this->frame !== null) {
            $this->frame->destroyComponents();
        }
        $this->clearItems();
        unset($this->script);

        parent::destroy();
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function getScript()
    {
        $totalRows = 0;
        $items = "";
        foreach ($this->iitems as $row => $elem) {
            $totalRows++;
            $items .= $row . ' => [ ' . implode(",", $elem) . '],';
        }
        if (empty($items)) {
            $this->myScript->setParam("items", "");
        } else {
            $items = "= [" . trim($items, ",") . "]";
            $this->myScript->setParam("items", $items);
        }

        $data = "";
        foreach ($this->data as $row => $elem) {
            $data .= $row . ' => [ ' . implode(",", $elem) . '],';
        }
        if (empty($data)) {
            $data = "";
        } else {
            $data = "= [" . trim($data, ",") . "]";
        }

        $this->myScript->setParam("data", $data);
        $this->myScript->setParam("itemsPerRow", $this->nbElemParColumn);
        $this->myScript->setParam("totalRows", $totalRows);
        $this->myScript->setParam("rowPerPage", $this->rowPerPage);


        return $this->myScript;
    }
}
