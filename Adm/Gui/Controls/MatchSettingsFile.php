<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
class MatchSettingsFile extends \ManiaLive\Gui\Control {

    private $bg;
    private $mapNick;
    private $saveButton;
    private $loadButton;
    private $label;
    private $time;
    private $saveAction;
    private $loadAction;
    private $deleteButton;
    private $deleteButtonf;
    private $deleteAction;
    private $frame;

    function __construct($indexNumber, $filename, $controller, $login, $sizeX) {
	$sizeY = 6;
	$this->saveAction = $this->createAction(array($controller, 'saveSettings'), $filename);
	$this->loadAction = $this->createAction(array($controller, 'loadSettings'), $filename);

	$this->deleteActionf = ActionHandler::getInstance()->createAction(array($controller, "deleteSetting"), $filename);
	$this->deleteAction = \ManiaLivePlugins\eXpansion\Gui\Gui::createConfirm($this->deleteActionf);

	$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
	$this->addComponent($this->bg);

	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setSize($sizeX, $sizeY);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());


	$spacer = new \ManiaLib\Gui\Elements\Quad();
	$spacer->setSize(4, 4);
	$spacer->setAlign("center", "center2");
	$spacer->setStyle("Icons128x128_1");
	$spacer->setSubStyle("Challenge");
	$this->frame->addComponent($spacer);

	$spacer = new \ManiaLib\Gui\Elements\Quad();
	$spacer->setSize(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
	//$this->frame->addComponent($spacer);

	$this->label = new \ManiaLib\Gui\Elements\Label(90, 4);
	$this->label->setAlign('left', 'center');
	$file = explode('/', $filename);
	$text = utf8_encode(end($file));
	//$text = str_replace(".txt", "", $text);
	$this->label->setText($text);	
	$this->label->setTextSize(1);
	$this->label->setStyle("TextCardSmallScores2");
	$this->label->setTextColor("fff");	
	$this->frame->addComponent($this->label);


	$spacer = new \ManiaLib\Gui\Elements\Quad();
	$spacer->setSize(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

	$this->frame->addComponent($spacer);


	$this->loadButton = new MyButton(26, 5);
	$this->loadButton->setText(__("Load", $login));
	$this->loadButton->setAction($this->loadAction);
	$this->loadButton->setScale(0.6);
	
	$this->frame->addComponent($this->loadButton);

	$this->saveButton = new MyButton(26, 5);
	$this->saveButton->setText(__("Save", $login));	
	$this->saveButton->setAction($this->saveAction);
	$this->saveButton->colorize("0d0");
	$this->saveButton->setScale(0.6);
	$this->frame->addComponent($this->saveButton);

	$this->deleteButton = new MyButton(26, 5);
	$this->deleteButton->setText(__("Delete", $login));
	$this->deleteButton->colorize("d00");
	$this->deleteButton->setAction($this->deleteAction);
	$this->deleteButton->setScale(0.6);
	$this->frame->addComponent($this->deleteButton);

	$this->addComponent($this->frame);

	$this->loadButton->setVisibility(AdminGroups::hasPermission($login, Permission::game_matchSettings));
	$this->saveButton->setVisibility(AdminGroups::hasPermission($login, Permission::game_matchSave));
	$this->deleteButton->setVisibility(AdminGroups::hasPermission($login, Permission::game_matchDelete));

	$this->sizeX = $sizeX;
	$this->sizeY = $sizeY;
	$this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
	$this->bg->setSize($this->sizeX, $this->sizeY);
	$this->bg->setPosX(0);
	
	$this->frame->setPosX(2);
	$this->frame->setSize($this->sizeX - 4, $this->sizeY);
    }

// manialive 3.1 override to do nothing.
    function destroy() {
	ActionHandler::getInstance()->deleteAction($this->deleteAction);
	ActionHandler::getInstance()->deleteAction($this->deleteActionf);
    }

    /*
     * custom function to remove contents.
     */

    function erase() {
	$this->saveButton->destroy();
	$this->loadButton->destroy();
	$this->frame->clearComponents();
	$this->frame->destroy();
	$this->clearComponents();

	parent::destroy();
    }

}
?>

