<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls\DediItem;
use ManiaLivePlugins\eXpansion\Widgets_Record\Widgets_Record;

class RecordsPanelTab extends \ManiaLive\Gui\Window {

    /** @var \ManiaLive\Gui\Controls\Frame */
    protected $frame_dedi;
    protected $items_dedi = array();
    protected $frame_local;
    protected $items_local = array();
    protected $bg_dedi, $bg_local;
    protected $titlebar_dedi, $titlebar_local;
    protected $lbl_title_dedi, $lbl_title_local;
    protected $container_dedi, $container_local;
    private $isDediLoaded = false;

    protected function onConstruct() {
	parent::onConstruct();

	$sizeX = 46;
	$sizeY = 95;
	$this->setScriptEvents(true);
	$this->setAlign("center", "top");
	$this->container_dedi = new \ManiaLive\Gui\Controls\Frame(-63, 52);
	$this->container_dedi->setAlign("center", "top");
	$this->container_dedi->setSize($sizeX, $sizeY);


	$this->container_local = new \ManiaLive\Gui\Controls\Frame(155, 52);
	$this->container_local->setAlign("center", "top");
	$this->container_local->setSize($sizeX, $sizeY);
	$this->addComponent($this->container_local);

	$this->bg_dedi = new \ManiaLib\Gui\Elements\Quad();
	$this->bg_dedi->setStyle("Bgs1InRace");
	$this->bg_dedi->setSubStyle("BgList");
	$this->bg_dedi->setAlign("center", "top");
	$this->container_dedi->addComponent($this->bg_dedi);

	$this->lbl_title_dedi = new \ManiaLib\Gui\Elements\Label(30, 8);
	$this->lbl_title_dedi->setTextSize(1);
	$this->lbl_title_dedi->setStyle("TextStaticVerySmall");
	$this->lbl_title_dedi->setAlign("center", "center");
	$this->container_dedi->addComponent($this->lbl_title_dedi);

	$this->titlebar_dedi = new \ManiaLib\Gui\Elements\Quad(30, 8);
	$this->titlebar_dedi->setStyle("Bgs1InRace");
	$this->titlebar_dedi->setSubStyle("BgTitle3_3");
	$this->titlebar_dedi->setAlign("center", "center");
	$this->container_dedi->addComponent($this->titlebar_dedi);
	// LOCALRECORDS

	$this->bg_local = new \ManiaLib\Gui\Elements\Quad();
	$this->bg_local->setStyle("Bgs1InRace");
	$this->bg_local->setSubStyle("BgList");
	$this->bg_local->setAlign("center", "top");
	$this->container_local->addComponent($this->bg_local);

	$this->lbl_title_local = new \ManiaLib\Gui\Elements\Label(30, 8);
	$this->lbl_title_local->setTextSize(1);
	$this->lbl_title_local->setStyle("TextStaticVerySmall");
	$this->lbl_title_local->setAlign("center", "center");
	$this->container_local->addComponent($this->lbl_title_local);

	$this->titlebar_local = new \ManiaLib\Gui\Elements\Quad(30, 8);
	$this->titlebar_local->setStyle("Bgs1InRace");
	$this->titlebar_local->setSubStyle("BgTitle3_3");
	$this->titlebar_local->setAlign("center", "center");
	$this->container_local->addComponent($this->titlebar_local);


	$pmanager = \ManiaLive\PluginHandler\PluginHandler::getInstance();

	$this->frame_dedi = new \ManiaLive\Gui\Controls\Frame();
	$this->frame_dedi->setAlign("left", "top");
	$this->frame_dedi->setPosition(0, -5);
	$this->frame_dedi->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
	$this->container_dedi->addComponent($this->frame_dedi);

	if ($pmanager->isLoaded('eXpansion\Dedimania')) {
	    $this->isDediLoaded = true;
	    $this->addComponent($this->container_dedi);
	}
	$this->addComponent($this->container_dedi);
	$this->frame_local = new \ManiaLive\Gui\Controls\Frame();
	$this->frame_local->setAlign("left", "top");
	$this->frame_local->setPosition(0, -5);
	$this->frame_local->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
	$this->container_local->addComponent($this->frame_local);


	$this->bg_dedi->setSize($sizeX, $sizeY);
	$this->bg_dedi->setPosY(-1);

	$this->bg_local->setSize($sizeX, $sizeY);
	$this->bg_local->setPosY(-1);

	$this->titlebar_dedi->setSizeX($sizeX + 16);
	$this->titlebar_local->setSizeX($sizeX + 16);
	$this->setSize($sizeX, $sizeY);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
    }

    function update() {
	$login = $this->getRecipient();

	$this->lbl_title_dedi->setText('$000' . __('Dedimania Records', $login));
	$this->lbl_title_local->setText('$000' . __('Local Records', $login));

	if ($this->isDediLoaded) {
	    foreach ($this->items_dedi as $item)
		$item->destroy();
	    $this->items_dedi = array();
	    $this->frame_dedi->clearComponents();
	    $index = 1;




	    if (!is_array(Widgets_Record::$dedirecords))
		return;
	    foreach (Widgets_Record::$dedirecords as $record) {
		if ($index > 30)
		    return;
		$this->items_dedi[$index - 1] = new DediItem($index, $record, $this->getRecipient());
		$this->frame_dedi->addComponent($this->items_dedi[$index - 1]);
		$index++;
	    }
	}

	foreach ($this->items_local as $item)
	    $item->destroy();
	$this->items_local = array();
	$this->frame_local->clearComponents();



	if (!is_array(Widgets_Record::$localrecords))
	    return;

	$index = 1;
	foreach (Widgets_Record::$localrecords as $record) {
	    if ($index > 30)
		return;
	    $this->items_local[$index - 1] = new Recorditem($index, $record, $this->getRecipient());
	    $this->frame_local->addComponent($this->items_local[$index - 1]);
	    $index++;
	}
    }

    function destroy() {
	foreach ($this->items_dedi as $item)
	    $item->destroy();
	foreach ($this->items_local as $item)
	    $item->destroy();
	$this->items_dedi = array();
	$this->items_local = array();

	$this->frame_dedi->clearComponents();
	$this->frame_local->clearComponents();
	$this->frame_dedi->destroy();
	$this->frame_local->destroy();
	$this->clearComponents();
	parent::destroy();
    }

}
?>
