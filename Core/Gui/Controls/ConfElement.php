<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Controls;

use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Core\MetaData;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use \ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
 use\ManiaLivePlugins\eXpansion\Core\types\config\types\HashList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\SortedList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\Gui\Windows\ExpListSetting;

class ConfElement extends \ManiaLive\Gui\Control {

    private $bg;
    private $label_name;

    /**
     * @var \ManiaLivePlugins\eXpansion\Gui\Elements\Button | null
     */
    private $button_save = null;
    private $button_load = null;
    private $button_select = null;

    private $input;

    function __construct($indexNumber, $name, $isCurrent, $login) {

	$this->label_name = new \ManiaLib\Gui\Elements\Label(40, 5);
	$this->label_name->setPosY(4);
	$this->label_name->setPosX(7);
	$this->label_name->setText($name);
	$this->addComponent($this->label_name);

	$this->bg = new ListBackGround($indexNumber, 100, 4);
	$this->addComponent($this->bg);


        $this->button_save = new Button(15, 6);
        $this->button_save->setText(__('Save', $login));
        $this->button_save->setDescription(__('Will save current configuration', $login), 40);
        $this->button_save->setAction($this->createAction(array($this, "saveAction"), $name));
        $this->addComponent($this->button_save);

        $this->button_load = new Button(15, 6);
        $this->button_load->setText(__('Load', $login));
        $this->button_load->setDescription(__('Wiil load this configuration into current one', $login), 40);
        $this->button_load->setAction($this->createAction(array($this, "loadAction"), $name));
        $this->addComponent($this->button_load);

        if(!$isCurrent){
            $this->button_select  = new Button(15, 6);
            $this->button_select ->setText(__('Choose', $login));
            $this->button_select ->setDescription(__('Will load this configuration and use it', $login), 40);
            $this->button_select ->setAction($this->createAction(array($this, "selectAction"), $name));
            $this->addComponent($this->button_select);
        }

	$this->setScale(0.8);
	$this->setSize(117, 8);
    }

    protected function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->label_name->setSizeX($this->getSizeX() - 27);
	$this->bg->setSize($this->getSizeX(), $this->getSizeY() + 2);

	$this->button_save->setPosition(($this->getSizeX() - $this->button_save->getSizeX()) + 1, 0);
	$this->button_load->setPosition(($this->getSizeX() - $this->button_save->getSizeX()*2) + 5, 0);

	if ($this->button_select != null) {
            $this->button_select->setPosition(($this->getSizeX() - $this->button_save->getSizeX()*3) + 10, 0);
	}
    }

    public function saveAction($login, $name){
        /** @var ConfigManager $confManager */
        $confManager = ConfigManager::getInstance();

        $confManager->saveSettingsIn($name);
    }

    public function loadAction($login, $name){
        /** @var ConfigManager $confManager */
        $confManager = ConfigManager::getInstance();

        $confManager->loadSettingsFrom($name);
    }

    public function selectAction($login, $name){
        /** @var ConfigManager $confManager */
        $confManager = ConfigManager::getInstance();

        $confManager->loadSettingsFrom($name, false);

        $var = MetaData::getInstance()->getVariable('saveSettingsFile');
        $var->setValue(str_replace('.user.exp', '', $name));

        $confManager->check(true);

        $var->hideConfWindow($login);
        $var->showConfWindow($login);
        $confManager->check();
    }

    public function getNbTextColumns() {
	return 2;
    }


}
?>