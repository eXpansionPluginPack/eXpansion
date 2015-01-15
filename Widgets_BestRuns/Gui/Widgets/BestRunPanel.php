<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Widgets_BestRuns\Structures\Run;

class BestRunPanel extends \ManiaLive\Gui\Window {

    /** @var \ManiaLivePlugins\eXpansion\Widgets_BestRuns\Structures\Run[]  */
    public static $bestRuns = array();
    protected $frame;

    /** @var \ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls\RunElem */
    protected $lines = array();

    protected function onConstruct() {
	parent::onConstruct();
	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(220, 7));
	$this->frame->setSize(220, 20);
	$this->addComponent($this->frame);
    }

    function onDraw() {
	$this->frame->clearComponents();
	foreach (self::$bestRuns as $run) {
	    $this->frame->addComponent(new \ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls\RunElem($run));
	}
    }

    function destroy() {
	foreach ($this->lines as $cp) {
	    $cp->destroy();
	}
	$this->lines = array();
	$this->destroyComponents();

	parent::destroy();
    }

}

?>
