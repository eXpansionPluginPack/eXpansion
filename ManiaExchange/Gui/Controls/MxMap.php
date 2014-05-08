<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class MxMap extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $time;
    private $addAction;
    private $addButton;
    private $actionSearch;
    private $queueButton;
    private $queueAction;
    private $line1, $line2;
    private $isAdmin;

    function __construct($indexNumber, \ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap $map, $controller, $isAdmin, $sizeX) {
	$config = \ManiaLivePlugins\eXpansion\ManiaExchange\Config::getInstance();
	$sizeY = 12;

	$this->isAdmin = $isAdmin;
	$id = "";

	if (property_exists($map, "trackID"))
	    $id = $map->trackID;
	if (property_exists($map, "mapID"))
	    $id = $map->mapID;

	$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
	$this->addComponent($this->bg);


	$this->addAction = $this->createAction(array($controller, 'addMap'), $id);
	$this->queueAction = $this->createAction(array($controller, 'mxVote'), $id);
	$this->actionSearch = $this->createAction(array($controller, 'search'), "", $map->username, null, null);

	$this->line1 = new \ManiaLive\Gui\Controls\Frame(0, 3);
	$this->line1->setAlign("left", "top");
	$this->line1->setSize($sizeX, $sizeY);
	$this->line1->setLayout(new \ManiaLib\Gui\Layouts\Line());

	$this->line2 = new \ManiaLive\Gui\Controls\Frame(0, -3);
	$this->line2->setAlign("left", "top");
	$this->line2->setSize($sizeX, $sizeY);
	$this->line2->setLayout(new \ManiaLib\Gui\Layouts\Line());

	$label = new \ManiaLib\Gui\Elements\Label(36, 6);
	$label->setAlign('left', 'center');
	$pack = str_replace("TM", "", $map->titlePack);
	if (empty($pack) || $pack == "Trackmania_2") {
	    $pack = $map->environmentName;
	}
	$label->setText($pack);
	$this->line1->addComponent($label);

	$label = new \ManiaLib\Gui\Elements\Label(36, 6);
	$label->setAlign('left', 'center');
	$label->setText("");
	if ($map->vehicleName) {
	    $vehicle = str_replace("Car", "", $map->vehicleName);
	    if ($vehicle != $pack) {
		$label->setText("Car: " . $vehicle);
	    }
	}
	$this->line2->addComponent($label);


	$this->label = new \ManiaLib\Gui\Elements\Label(80, 6);
	$this->label->setAlign('left', 'center');
	$this->label->setStyle("TextCardSmallScores2");
	$this->label->setTextEmboss();
	$this->label->setText(Gui::fixHyphens(Formatting::stripCodes($map->gbxMapName, 's')));
	if ($config->mxVote_enable) {
	    $this->label->setAction($this->queueAction);
	}
	$this->line1->addComponent($this->label);

	$info = new \ManiaLib\Gui\Elements\Label(80, 6);
	$info->setAlign('left', 'center');
	$info->setText('$000' . Gui::fixHyphens($map->username));
	$info->setAction($this->actionSearch);
	$info->setStyle("TextCardSmallScores2");
	$info->setScriptEvents(true);
	$this->line2->addComponent($info);


	$info = new \ManiaLib\Gui\Elements\Label(24, 4);
	$info->setAlign('left', 'center');
	$info->setText($map->difficultyName);
	$this->line1->addComponent($info);

	$info = new \ManiaLib\Gui\Elements\Label(24, 4);
	$info->setAlign('left', 'center');
	$info->setText($map->mood);
	$this->line2->addComponent($info);

	$info = new \ManiaLib\Gui\Elements\Label(18, 4);
	$info->setAlign('left', 'center');
	$info->setText($map->styleName);
	$this->line1->addComponent($info);


	$info = new \ManiaLib\Gui\Elements\Label(18, 4);
	$info->setAlign('left', 'center');
	$info->setText($map->lengthName);
	$this->line2->addComponent($info);

	/* if ($config->mxVote_enable) {
	  $this->queueButton = new myButton(24, 5);
	  $this->queueButton->setText(__("Queue"));
	  $this->queueButton->colorize("0d0");
	  $this->queueButton->setAction($this->queueAction);
	  $this->line1->addComponent($this->queueButton);
	  } */

	if ($this->isAdmin) {
	    $this->addButton = new myButton(24, 5);
	    $this->addButton->setPosY(-3);
	    $this->addButton->setText(__("Install"));
	    $this->addButton->colorize("0d0");
	    $this->addButton->setAction($this->addAction);
	    $this->line1->addComponent($this->addButton);

	    $info = new \ManiaLib\Gui\Elements\Label(24, 5);
	    $info->setText("");
	    $this->line2->addComponent($info);
	}


	if ($map->awardCount > 0) {
	    $info = new \ManiaLib\Gui\Elements\Quad(4, 4);
	    $info->setPosY(3);
	    $info->setStyle("Icons64x64_1");
	    $info->setSubStyle("OfficialRace");
	    $info->setAlign('center', 'center');
	    $this->line2->addComponent($info);

	    $info = new \ManiaLib\Gui\Elements\Label(12, 5);
	    $info->setPosY(3);
	    $info->setAlign('center', 'center');
	    $info->setText($map->awardCount);
	    $this->line2->addComponent($info);
	}
	$this->addComponent($this->line1);
	$this->addComponent($this->line2);

	$this->sizeX = $sizeX;
	$this->sizeY = $sizeY;
    }

    // override destroy method not to destroy its contents on manialive 3.1 
    function destroy() {
	
    }

    /**
     * custom function to destroy contents when needed.
     */
    function erase() {
	if (is_object($this->queueButton)) {
	    $this->queueButton->destroy();
	}
	if ($this->isAdmin) {
	    $this->addButton->destroy();
	}
	$this->line1->clearComponents();
	$this->line1->destroy();
	$this->line2->clearComponents();
	$this->line2->destroy();
	$this->clearComponents();
	parent::destroy();
    }

}
?>

