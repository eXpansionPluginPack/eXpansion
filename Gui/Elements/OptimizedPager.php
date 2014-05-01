<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use \ManiaLive\DedicatedApi\Callback\Event;

class OptimizedPager extends \ManiaLive\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer {

    private $frame;
    private $clickAction;
    private $iitems = array();
    private $data = array();
    private $scroll, $bg, $scrollBg;
    private $scrollDown, $scrollUp;
    private $myScript;
    private $ContentLayout;
    private $nbElemParColumn;
    private $index = 0;
    private $rowPerPage = 1;

    function __construct() {

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
	$this->scrollBg->setStyle("Bgs1");
	$this->scrollBg->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgTitle3_3);
	$this->scrollBg->setId("ScrollBg");
	$this->scrollBg->setScriptEvents();
	$this->addComponent($this->scrollBg);

	$this->scroll = new \ManiaLib\Gui\Elements\Quad(3, 15);
	$this->scroll->setAlign("center", "top");
	$this->scroll->setStyle("Bgs1");
	$this->scroll->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgCard1);
	$this->scroll->setId("ScrollBar");
	$this->scroll->setScriptEvents();
	$this->addComponent($this->scroll);

	$this->scrollDown = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$this->scrollDown->setAlign("center", "top");
	$this->scrollDown->setStyle("Icons128x128_1");
	$this->scrollDown->setSubStyle('Back');
	$this->scrollDown->setId("ScrollDown");
	$this->scrollDown->setScriptEvents();
	$this->scrollDown->setAttribute("rot", 270);
	$this->addComponent($this->scrollDown);

	$this->scrollUp = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$this->scrollUp->setAlign("center", "top");
	$this->scrollUp->setStyle("Icons128x128_1");
	$this->scrollUp->setSubStyle('Back');
	$this->scrollUp->setId("ScrollUp");
	$this->scrollUp->setAttribute("rot", 90);
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

    function handleClick($login, $entries) {
	if (!empty($entries['item'])) {
	    // do some magic
	    $player = \ManiaLive\Data\Storage::getInstance()->getPlayerObject($login);
	    \ManiaLive\Gui\ActionHandler::getInstance()->onPlayerManialinkPageAnswer(intval($player->playerId), $login, intval($entries['item']), array());
	}
    }

    function clearItems() {
	$this->iitems = array();
	$this->data = array();
	$this->index = 0;
    }

    public function addSimpleItems($items) {
	foreach ($items as $text => $action) {
	    $this->iitems[$this->index][] = '"' . $this->fixHyphens($text) . '"';
	    $this->data[$this->index][] = '"' . $this->fixHyphens($action) . '"';
	}
	$this->index++;
    }

    function setSize() {
	$args = func_get_args();
	$this->sizeX = $args[0];
	$this->sizeY = $args[1];
	$this->scroll->setPosition($this->sizeX - 3, 0);
	$this->scrollBg->setPosition($this->sizeX - 3);
	$this->scrollBg->setSizeY($this->sizeY - 2);

	$this->scrollDown->setPosition($this->sizeX - 5.5, -$this->sizeY + 1);
	$this->scrollUp->setPosition($this->sizeX - 0.5, 1);
    }

    public function setContentLayout($className) {
	$this->ContentLayout = $className;
    }

    public function update($login) {

	$className = $this->ContentLayout;
	$layout = new $className(0, $login, $this->clickAction);

	$sizeY = $layout->getSizeY() * ($layout->getScale() == 0.0 ? 1 : $layout->getScale());
	
	$this->frame->clearComponents();
	$layout = null;
	
	$limit = (int)($this->getSizeY()/$sizeY);
	
	for ($x = 0; $x <  $limit; $x++) {
	    $className = $this->ContentLayout;
	    $layout = new $className($x, $login, $this->clickAction);
	    $this->frame->addComponent($layout);
	}
	$this->rowPerPage = $limit;
	
	$this->nbElemParColumn = $layout->getNbTextColumns();
    }

    public function destroy() {
	$this->clearItems();
	//  $this->pager->destroy();
	parent::destroy();
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
	parent::onIsRemoved($target);
	$this->destroy();
    }

    public function getScript() {
	$totalRows = 0;
	$items = "";
	foreach ($this->iitems as $row => $elem) {
	    $totalRows++;
	    $items .= $row . ' => [ ' . implode(",", $elem) . '],';
	}
	if(empty($items)){
	    $this->myScript->setParam("items", "");
	}else{
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

    protected function fixHyphens($string) {
	$out = str_replace('"', "'", $string);
	$out = str_replace('\\', '\\\\', $out);
	$out = str_replace('-', '–', $out);
	return $out;
    }

}
