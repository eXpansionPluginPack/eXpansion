<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use \ManiaLive\DedicatedApi\Callback\Event;

/**
 * Description of OptimizedPager
 *
 * @author Petri
 */
class OptimizedPager extends \ManiaLive\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer {

    private $frame;
    private $xml;
    private $clickAction;
    private $iitems = array();
    private $data = array();
    private $scroll, $bg, $scrollBg;
    private $myScript;
    private $quadNb;
    private $buildLayout = true;
    private $itemLayout = array();

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

    /** add items */
    public function addItem(\ManiaLib\Gui\Component $component) {
        // first detect what items inside
        $this->detect($component);

        $items = "[" . implode(",", $this->iitems) . "]";
        $this->myScript->setParam("items", $items);
        $data = "[" . implode($this->data, ",") . "]";
        $this->myScript->setParam("data", $data);
        $this->buildLayout = false;
    }

    function setSize() {
        $args = func_get_args();
        $this->sizeX = $args[0];
        $this->sizeY = $args[1];
        $this->update();
    }

    private function update() {
        $this->frame->clearComponents();
        echo "update\n";
        $columnNumber = 0;
        for ($x = 0; $x < 15; $x++) {
            $itemframe = new \ManiaLive\Gui\Controls\Frame();
            $itemframe->setSize($this->getSizeX(), 6);
            $itemframe->setId("row_" . $x);

            $bg = new \ManiaLib\Gui\Elements\Quad($this->sizeX, 6);
            $bg->setStyle("Bgs1");
            $bg->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgTitlePage);
            $itemframe->addComponent($bg);

            $itemContainer = new \ManiaLive\Gui\Controls\Frame();
            $itemContainer->setLayout(new \ManiaLib\Gui\Layouts\Line());

            foreach ($this->itemLayout as $elem) {
                if ($elem instanceof \ManiaLib\Gui\Elements\Label) {
                    $newelem = new \ManiaLib\Gui\Elements\Label(25,6);
                    $newelem->setText("column_" . $columnNumber);
                    $newelem->setAttribute("class", "label");
                    $newelem->setId("column_" . $columnNumber);
                    echo "column_" . $columnNumber . "\n";
                    $columnNumber++;
                } else {
                    $newelem->setAttribute("class", "quad");
                }

                $newelem->setAction($this->clickAction);
                $newelem->setScriptEvents();
                $itemContainer->addComponent($newelem);
            }
            $itemframe->addComponent($itemContainer);

            $this->frame->addComponent($itemframe);
        }
        $this->myScript->setParam("columnNumber", $columnNumber);
        $this->myScript->setParam("itemsPerRow", count($this->itemLayout));

        $this->scroll->setPosition($this->sizeX - 3, 0);
        $this->scrollBg->setPosition($this->sizeX - 3);
        $this->scrollBg->setSizeY($this->sizeY);
    }

    public function detect($component) {
        foreach ($component->getComponents() as $item) {

            if ($item instanceof \ManiaLive\Gui\Controls\Frame) {
                $this->detect($item);
            }

            if ($item instanceof \ManiaLib\Gui\Elements\Label) {
                if ($this->buildLayout) {
                    $label = new \ManiaLib\Gui\Elements\Label();
                    $this->itemLayout[] = $label;
                }
                /** @var \ManiaLib\Gui\Elements\Label $item */
                $this->iitems[] = '"' . $item->getText() . '"';
                $this->data[] = '"' . $item->getAction() . '"';
            }

            /* if ($item instanceof \ManiaLib\Gui\Elements\Quad) {
              if ($this->buildLayout) {
              $this->itemLayout[] = $item;
              }

              $this->iitems[] = '"quad_' . $this->quadNb . '"';
              $this->data[] = "" . $item->getAction();
              $this->quadNb++;
              } */
        }
    }

    public function clearItems() {
        
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
        return $this->myScript;
    }

}
