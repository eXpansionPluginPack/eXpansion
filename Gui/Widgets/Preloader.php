<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

class Preloader extends \ManiaLive\Gui\Window {

    protected $frame;

    protected function onConstruct() {
	parent::onConstruct();

	$this->frame = new \ManiaLive\Gui\Controls\Frame(0, 0);
	$this->frame->setAlign("left", "center");
	$this->frame->setSize(320, 40);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(240,40));
	$this->addComponent($this->frame);

	$this->setSize(320, 40);
	$this->setPosition(900, 0);
	$this->setAlign("center", "center");
    }

    function add($url) {
	$elem = new \ManiaLib\Gui\Elements\Quad(6, 6);
	$elem->setImage($url);
	$this->frame->addComponent($elem);
    }

    function destroy() {
	$this->frame->clearComponents();
	$this->frame->destroy();
	$this->clearComponents();
	parent::destroy();
    }

}

?>
