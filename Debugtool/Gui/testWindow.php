<?php

namespace ManiaLivePlugins\eXpansion\Debugtool\Gui;

/**
 * Description of testWindow
 *
 * @author Petri
 */
class testWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $entries = array();

    protected function onConstruct() {
	parent::onConstruct();
	
	$frame = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
	$frame->setSize(120,40);
	
	for ($x = 0; $x < 150; $x++) {

	    $this->entries[$x] = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("e" . $x);
	    $this->entries[$x]->setLabel("e" . $x);
	    $this->entries[$x]->setText("e" . $x);
	    $frame->addItem($this->entries[$x]);
	}
	$this->addComponent($frame);

	$button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
	$button->setPosition(-30, 0);
	$button->setText("submit");
	$button->setAction($this->createAction(array($this, "submit")));
	$this->addComponent($button);

	
	$this->setSize(120,60);
	$this->setTitle("testwidnow");
    }

    function submit($login, $entries) {
	print_r($entries);
	print "total:" . count($entries);
    }

}
