<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls\DediItem;
use ManiaLivePlugins\eXpansion\Widgets_Record\Widgets_Record;

class RecordsPanel extends \ManiaLive\Gui\Window {

    const RIGHT = "right";
    const LEFT = "left";
    
    /** @var \ManiaLive\Gui\Controls\Frame */
    private $frame;
    private $actionDedi = null;
    private $actionLocal = null;
    private $btnDedi;
    private $btnLocal;
    private $items = array();
    private $bg;
    private $titlebar;
    private $lbl_title;
    private $_windowFrame;
    private $minButton;

    const SHOW_DEDIMANIA = 0x02;
    const SHOW_LOCALRECORDS = 0x04;

    private $showpanel = self::SHOW_LOCALRECORDS;
    private $isMinimized = false;
    private $originalPosX;
    private $edge;

    protected function onConstruct() {
	parent::onConstruct();

	$this->setScriptEvents(true);
	$this->setAlign("center", "top");

	$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
	$this->_windowFrame->setAlign("left", "top");
	$this->_windowFrame->setId("Frame");
	$this->_windowFrame->setScriptEvents(true);
	$this->addComponent($this->_windowFrame);

	$this->bg = new \ManiaLib\Gui\Elements\Quad();
	$this->bg->setStyle("Bgs1InRace");
	$this->bg->setSubStyle("BgList");
	$this->bg->setId("MainWindow");
	$this->bg->setScriptEvents(true);
	$this->bg->setAlign("center", "top");
	$this->_windowFrame->addComponent($this->bg);

	$this->lbl_title = new \ManiaLib\Gui\Elements\Label(30, 8);
	$this->lbl_title->setTextSize(1);
	$this->lbl_title->setStyle("TextStaticVerySmall");
	$this->lbl_title->setAlign("center", "center");
	$this->_windowFrame->addComponent($this->lbl_title);

	$this->titlebar = new \ManiaLib\Gui\Elements\Quad(30, 8);
	$this->titlebar->setStyle("Bgs1InRace");
	$this->titlebar->setSubStyle("BgTitle3_3");
	$this->titlebar->setAlign("center", "center");
	$this->_windowFrame->addComponent($this->titlebar);


	$pmanager = \ManiaLive\PluginHandler\PluginHandler::getInstance();

	if ($pmanager->isLoaded('eXpansion\Dedimania') && $pmanager->isLoaded('eXpansion\LocalRecords')) {
	    $this->actionDedi = $this->createAction(array($this, "setPanel"), self::SHOW_DEDIMANIA);
	    $this->actionLocal = $this->createAction(array($this, "setPanel"), self::SHOW_LOCALRECORDS);

	    $this->btnDedi = new \ManiaLib\Gui\Elements\Quad(5, 5);
	    $this->btnDedi->setAction($this->actionDedi);
	    $this->btnDedi->setStyle('Icons64x64_1');
	    $this->btnDedi->setSubStyle('ToolLeague1');
	    $this->btnDedi->setAlign("centers", "center");
	    $this->_windowFrame->addComponent($this->btnDedi);
	}

	$this->actionMin = $this->createAction(array($this, "toggleMinimized"));
	$this->minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$this->minButton->setAction($this->actionMin);
	$this->minButton->setStyle('Icons64x64_1');
	$this->minButton->setSubStyle('Sub');
	$this->minButton->setAlign("center", "center");
	$this->_windowFrame->addComponent($this->minButton);

	if ($pmanager->isLoaded('eXpansion\LocalRecords')) {
	    $this->showpanel = self::SHOW_LOCALRECORDS;
	}
	if ($pmanager->isLoaded('eXpansion\Dedimania')) {
	    // $this->showpanel = self::SHOW_DEDIMANIA;
	}
	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setAlign("left", "top");
	$this->frame->setPosition(0, -3);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
	$this->_windowFrame->addComponent($this->frame);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);

	$this->bg->setSize($this->sizeX, $this->sizeY);
	$this->bg->setPosY(-1);

	$this->titlebar->setSizeX($this->sizeX + 16);
	$this->lbl_title->setPosY(0);

	$pos = (($this->getSizeX() / 2));
	$dedipos = 8;
	if ($this->edge == self::RIGHT) {
	    $pos = -(($this->getSizeX() / 2));
	    $dedipos = -3;
	}
	if (is_object($this->btnDedi)) {
	    $this->btnDedi->setPosition($pos - $dedipos, 0);
	}

	$this->minButton->setPosition($pos, 0);
    }

    function update() {
	$login = $this->getRecipient();

	foreach ($this->items as $item)
	    $item->destroy();
	$this->items = array();
	$this->frame->clearComponents();

	$index = 1;

	if ($this->showpanel == self::SHOW_DEDIMANIA) {
	    $this->lbl_title->setText('$000' . __('Dedimania Records', $login));
	    if (is_object($this->btnDedi))
		$this->btnDedi->setAction($this->actionLocal);

	    if (!is_array(Widgets_Record::$dedirecords ))
		return;
	    foreach (Widgets_Record::$dedirecords  as $record) {
		if ($index > 30)
		    return;
		$this->items[] = new DediItem($index, $record, $this->getRecipient());
		$this->frame->addComponent($this->items[$index - 1]);
		$index++;
	    }
	}

	if ($this->showpanel == self::SHOW_LOCALRECORDS) {
	    $this->lbl_title->setText('$000' . __('Local Records', $login));
	    if (is_object($this->btnDedi))
		$this->btnDedi->setAction($this->actionDedi);

	    if (!is_array(Widgets_Record::$localrecords ))
		return;
	    foreach (Widgets_Record::$localrecords  as $record) {
		if ($index > 30)
		    return;
		$this->items[] = new Recorditem($index, $record, $this->getRecipient());
		$this->frame->addComponent($this->items[$index - 1]);
		$index++;
	    }
	}
    }

    function toggleMinimized($login) {
	$this->isMinimized = !$this->isMinimized;
	$this->redraw($this->getRecipient());
    }

    function setScreenEdge($edge) {
	$this->edge = $edge;
	$this->setSize(46, 95);
	switch ($edge) {
	    case self::RIGHT:
		$this->setPosition(161, 50);
		break;
	    default:
		$this->setPosition(-115, 60);
		break;
	}
    }

    function setPanel($login, $panel) {
	$this->showpanel = $panel;
	$this->update();
	$this->redraw($this->getRecipient());
    }

    function setPosition($x = null, $y = null, $z = null) {
	$this->originalPosX = $x;
	parent::setPosition($x, $y, $z);
    }

    protected function onDraw() {
	// set default value 
	$minBtnStyles = array('ArrowPrev', 'ArrowNext');
	if ($this->edge == self::RIGHT)
	    $minBtnStyles = array('ArrowNext', 'ArrowPrev');

	$this->minButton->setSubStyle($minBtnStyles[0]);
	$this->setPosX($this->originalPosX);

	if (is_object($this->btnDedi))
	    $this->btnDedi->setVisibility(true);

	// if user has minimized, set new values
	if ($this->isMinimized) {

	    $pos = $this->getSizeX();
	    if ($this->edge == self::RIGHT) {
		$pos = -1 * ($this->getSizeX() - 8);
	    }
	    $this->minButton->setSubStyle($minBtnStyles[1]);
	    $this->setPosX($this->originalPosX - $pos + 6);

	    if (is_object($this->btnDedi))
		$this->btnDedi->setVisibility(false);
	}

	parent::onDraw();
    }

    function destroy() {
	foreach ($this->items as $item)
	    $item->destroy();

	$this->items = array();

	$this->frame->clearComponents();
	$this->frame->destroy();
	$this->clearComponents();
	parent::destroy();
    }

}

?>
