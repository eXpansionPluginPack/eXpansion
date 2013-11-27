<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls;

class RunElem extends \ManiaLive\Gui\Control {

    protected $frame;
    protected $player;
    protected $cps = array();

    function __construct(\ManiaLivePlugins\eXpansion\Widgets_BestRuns\Structures\Run $run) {
	$x = 0;
	$this->setSize(220, 6);
	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(200, 6));
	$this->frame->setSize(220, 6);
	$this->addComponent($this->frame);

	$this->player = new \ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls\RunPlayerElem($run->player);
	$this->frame->addComponent($this->player);

	$i = 0;
	foreach ($run->checkpoints as $time) {
	    $this->cps[$i] = new \ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls\RunCpElem($i, $time);
	    $this->frame->addComponent($this->cps[$i]);
	    $i++;
	}
    }

    public function destroy() {
	foreach ($this->cps as $item) {
	    $item->destroy();
	}
	$this->cps = array();
	$this->clearComponents();
	parent::destroy();
    }

}
?>

