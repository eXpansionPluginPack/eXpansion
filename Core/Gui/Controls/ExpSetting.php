<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Controls;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use \ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
 use\ManiaLivePlugins\eXpansion\Core\types\config\types\HashList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\SortedList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\Gui\Windows\ExpListSetting;

class ExpSetting extends \ManiaLive\Gui\Control {

    private $bg;
    private $label_varName;
    private $label_varValue;
    private $button_change = null;
    private $input;

    function __construct($indexNumber, Variable $var, $login) {
	$this->label_varName = new \ManiaLib\Gui\Elements\Label(40, 5);
	$this->label_varName->setPosY(4);
	$this->label_varName->setText($var->getVisibleName());
	$this->addComponent($this->label_varName);

	$this->bg = new ListBackGround($indexNumber, 100, 4);
	$this->addComponent($this->bg);

	if ($var instanceof HashList || $var instanceof BasicList  || $var instanceof SortedList) {

	    $this->label_varValue = new \ManiaLib\Gui\Elements\Label(40, 5);
	    $this->label_varValue->setScale(0.9);
	    $this->label_varValue->setId('column_' . $indexNumber . '_1');
	    $this->label_varValue->setText($var->getPreviewValues());
	    $this->addComponent($this->label_varValue);

	    $this->button_change = new Button(25, 6);
	    $this->button_change->setText(__('Change', $login));
	    $this->button_change->setDescription(__('Allows you to edit values', $login), 40);
	    $this->button_change->setAction($this->createAction(array($this, "openWin"), $var));
	    $this->addComponent($this->button_change);
	}


	$this->setScale(0.8);
	$this->setSize(117, 8);
    }

    protected function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->label_varName->setSizeX($this->getSizeX() - 25);
	$this->bg->setSize($this->getSizeX(), $this->getSizeY() + 2);

	if($this->button_change != null){
	    $this->button_change->setPosition($this->getSizeX() - 20, 0);
	    $this->label_varValue->setSizeX($this->getSizeX() - 25);
	    $this->label_varValue->setPosition(5, -1);
	}
    }

    public function getNbTextColumns() {
	return 2;
    }
    
    public function openWin($login , $var){
	ExpListSetting::Erase($login);
	$win = ExpListSetting::Create($login);
	$win->setTitle("Expansion Settings : ".$var->getVisibleName());
	$win->centerOnScreen();
	$win->setSize(140, 100);
	$win->populate($var);
	$win->show();
    }

}
?>
/