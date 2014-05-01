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
    private $icon_global = null;
    private $input;
    private $var;

    function __construct($indexNumber, Variable $var, $login) {
	$this->var = $var;
	
	$this->label_varName = new \ManiaLib\Gui\Elements\Label(40, 5);
	$this->label_varName->setPosY(4);
	$this->label_varName->setPosX(7);
	$this->label_varName->setText($var->getVisibleName());
	$this->addComponent($this->label_varName);

	$this->bg = new ListBackGround($indexNumber, 100, 4);
	$this->addComponent($this->bg);

	if ($var instanceof HashList || $var instanceof BasicList || $var instanceof SortedList) {

	    $this->label_varValue = new \ManiaLib\Gui\Elements\Label(40, 5);
	    $this->label_varValue->setScale(0.9);
	    $this->label_varValue->setPosX(10);
	    $this->label_varValue->setId('column_' . $indexNumber . '_1');
	    $this->label_varValue->setText($var->getPreviewValues());
	    $this->addComponent($this->label_varValue);

	    $this->button_change = new Button(25, 6);
	    $this->button_change->setText(__('Change', $login));
	    $this->button_change->setDescription(__('Allows you to edit values', $login), 40);
	    $this->button_change->setAction($this->createAction(array($this, "openWin"), $var));
	    $this->addComponent($this->button_change);
	} else if($var->getDescription() != ""){
	    
	    $this->button_change = new Button(8, 8);
	    $this->button_change->setIcon('UIConstructionSimple_Buttons', 'Help');
	    $this->button_change->setDescription($var->getDescription(), 120, 12, 2);
	    $this->button_change->setAction($this->createAction(array($this, "openWin"), $var));
	    $this->addComponent($this->button_change);
	}
	
	if ($var instanceof HashList || $var instanceof BasicList || $var instanceof SortedList) {
	    
	}else{
	    if ($var instanceof Boolean) {
		$this->input = new \ManiaLivePlugins\eXpansion\Gui\Elements\CheckboxScripted(5, 5);
		$this->input->setStatus($var->getRawValue());
		$this->input->setPosY(-1);
		$this->input->setPosX(7);
		$this->addComponent($this->input);
	    }else{
		$this->input = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox($var->getName());
		$this->input->setText($var->getRawValue());
		$this->input->setPosY(-2);
		$this->input->setPosX(7);
		$this->addComponent($this->input);
	    }
	}

	$this->icon_global = new Button(7,7);
	if($var->getIsGlobal()){
	    $this->icon_global->setIcon('Icons64x64_1', 'IconLeaguesLadder');
	    $this->icon_global->setDescription(__("Global Setting, Saved for all servers sharing this configuration", $login),120);
	}else{
	    $this->icon_global->setIcon('Icons64x64_1', 'IconServers');
	    $this->icon_global->setDescription(__("Server Setting, Saved for this server only", $login), 80);
	}
	$this->addComponent($this->icon_global);

	$this->setScale(0.8);
	$this->setSize(117, 8);
    }

    protected function onResize($oldX, $oldY) {
	//echo 'OnResize : '.$this->getSizeX()."\n";
	parent::onResize($oldX, $oldY);
	$this->label_varName->setSizeX($this->getSizeX() - 27);
	$this->bg->setSize($this->getSizeX(), $this->getSizeY() + 2);

	if ($this->button_change != null) {
	    $this->button_change->setPosition($this->getSizeX() - $this->button_change->getSizeX() + 5, 0);
	}
	if ($this->label_varValue != null) {
	    $this->label_varValue->setSizeX($this->getSizeX() - 27);
	    $this->label_varValue->setPosition(5, -1);
	}
	
	if($this->input != null){
	    $this->input->setSizeX($this->getSizeX() - 20);
	}
    }

    public function getNbTextColumns() {
	return 2;
    }

    public function openWin($login, $var) {
	ExpListSetting::Erase($login);
	$win = ExpListSetting::Create($login);
	$win->setTitle("Expansion Settings : " . $var->getVisibleName());
	$win->centerOnScreen();
	$win->setSize(140, 100);
	$win->populate($var);
	$win->show();
    }
    
    public function getVar(){
	if($this->input != null)
	    return $this->var;
	else
	    return null;
    }
    
    public function getVarValue($options){
	if($this->input != null){
	    if($this->input instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\CheckboxScripted){
		$this->input->setArgs($options);
		return $this->input->getStatus();
	    }else{
		return isset($options[$this->var->getName()]) ? $options[$this->var->getName()] : null;
	    }
	}
    }

}
?>
/