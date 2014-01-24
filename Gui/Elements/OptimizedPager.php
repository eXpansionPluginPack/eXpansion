<?php
namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of OptimizedPager
 *
 * @author Petri
 */
class OptimizedPager extends \ManiaLive\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer {

    private $pager;
    private $items = array();
    private $scroll;
    private $scrollBg;
    private $itemSizeY = 6;

    private $myScript;
    
    public function __construct() {
        $this->pager = new \ManiaLive\Gui\Controls\Frame();
        $this->pager->setId("Pager");
        $this->pager->setScriptEvents();
        $this->addComponent($this->pager);

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
        
        $this->myScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\OptimizedPager");
    }

    public function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 6, $this->sizeY);
        $this->scroll->setPosition($this->sizeX - 3, 0);

        $this->scrollBg->setPosition($this->sizeX - 3);
        $this->scrollBg->setSizeY($this->sizeY);
    }

    public function setStretchContentX($value) {
        // do nothing xD 
    }

    public function addItem(\ManiaLib\Gui\Component $component) {
        $component->setSizeX($this->sizeX - 4);
        $component->setAlign("left", "top");
        if ($component->getSizeY() > 0) {
            $this->itemSizeY = $component->getSizeY();
        }
        $item = new \ManiaLive\Gui\Controls\Frame();
        $item->setAlign("left", "top");
        $item->setScriptEvents();
        $item->addComponent($component);
        $hash = spl_object_hash($item);
        $this->items[$hash] = $item;
        $this->pager->addComponent($this->items[$hash]);
    }

    public function clearItems() {
        foreach ($this->items as $item) {
            $this->pager->removeComponent($item);
            $item->destroy();
        }
        $this->items = array();
    }

    public function removeItem(\ManiaLib\Gui\Component $item) {
        $hash = spl_object_hash($item);
        $this->pager->removeComponent($this->items[$hash]);
        $this->items[$hash]->destroy();
        unset($this->items[$hash]);
    }

    public function destroy() {
        $this->clearItems();
        $this->pager->destroy();
        parent::destroy();
    }


    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function getScript() {
        $this->myScript->setParam("sizeY", $this->itemSizeY);
        return $this->myScript;
    }
    
}