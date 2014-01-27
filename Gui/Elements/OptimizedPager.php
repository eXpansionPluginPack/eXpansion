<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use \ManiaLive\DedicatedApi\Callback\Event;

class OptimizedPager extends \ManiaLive\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer {

    private $frame;
    private $clickAction;
	
    private $iitems = array();
    private $data = array();
	
    private $scroll, $bg, $scrollBg;
    private $myScript;

	private $ContentLayout;
	private $nbElemParColumn;
    
	private $index = 0;
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

        $this->xml = new \ManiaLive\Gui\Elements\Xml();

        $entry = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("item");
        $entry->setId("entry");
        $entry->setScriptEvents();
        $entry->setPosition(0, 0);
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

    public function addSimpleItems($items) {
        foreach ($items as $text => $action) {
            $this->iitems[$this->index][] = '"' . $text . '"';
            $this->data[$this->index][] = '"' . $action . '"';
        }
        $this->index++;
    }

    function setSize() {
        $args = func_get_args();
        $this->sizeX = $args[0];
        $this->sizeY = $args[1];
		$this->scroll->setPosition($this->sizeX - 3, 0);
		$this->scrollBg->setPosition($this->sizeX - 3);
		$this->scrollBg->setSizeY($this->sizeY);
    }
	
	public function setContentLayout($className){
		$this->ContentLayout = $className;
	}

    public function update($login) {
		
		$this->frame->clearComponents();
		$layout = null;
		for ($x = 0; $x < 12; $x++) {		
			$className = $this->ContentLayout;
			$layout = new $className($x, $login, $this->clickAction);
			$this->frame->addComponent($layout);
		}
		$this->nbElemParColumn = $layout->getNbTextColumns();
	}

	public function clearItems(){
		
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
        $items = "[" . trim($items, ",") . "]";       
        $this->myScript->setParam("items", $items);
        
        $data = "";
        foreach ($this->data as $row => $elem) {
            $data .= $row . ' => [ ' . implode(",", $elem) . '],';
        }
        $data = "[" . trim($data, ",") . "]";       

        $this->myScript->setParam("data", $data);
        $this->myScript->setParam("itemsPerRow", $this->nbElemParColumn);
        $this->myScript->setParam("totalRows", $totalRows);
        

        return $this->myScript;
    }

}
