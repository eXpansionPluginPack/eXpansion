<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Core\ConfigManager;

/**
 * Description of ExpSettings
 *
 * @author De Cramer Oliver
 */
class ExpSettings extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    /**
     *
     * @var \ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSettingsMenu
     */
    public $menuFrame = null;
    public $pagerFrame = null;
    public $actions = array();
    public $items = array();
    public $configManager;
    private $currentGroup = "";
    private $first = false;
    
    private $button_validate = null;
    private $button_cancel = null;

    protected function onConstruct() {
	parent::onConstruct();

	$this->menuFrame = new \ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSettingsMenu();

	$this->pagerFrame = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
	$this->pagerFrame->setPosY(3);

	$this->mainFrame->addComponent($this->pagerFrame);
	$this->mainFrame->addComponent($this->menuFrame);

	$this->button_validate = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
	$this->button_validate->setText("Save");
	$this->button_validate->setAction($this->createAction(array($this, 'applySettings')));
	$this->mainFrame->addComponent($this->button_validate);
    }

    public function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->menuFrame->setSizeX($this->getSizeX() / 4);
	$this->menuFrame->setSizeY($this->getSizeY());

	$this->pagerFrame->setPosX($this->getSizeX() / 4);
	echo $this->getSizeX() * 3 / 4 - 3;
	$this->pagerFrame->setSize($this->getSizeX() * 3 / 4 - 3, $this->getSizeY() - 8);
	
	$this->button_validate->setPosX($this->getSizeX() - $this->button_validate->getSizeX());
	$this->button_validate->setPosY(-$this->getSizeY() + 5);
    }

    public function populate(ConfigManager $configs, $groupName) {
	$this->configManager = $configs;
	$this->pagerFrame->clearItems();
	$this->currentGroup = $groupName;
	
	$groupVars = $configs->getGroupedVariables();

	if (!$this->first) {
	    $this->menuFrame->reset();
	    foreach ($groupVars as $groupName => $vars) {
		$action = $this->createAction(array($this, 'switchGroup'), $groupName);
		$this->menuFrame->addItem($groupName, $action);
	    }
	    $this->first = true;
	}

	$this->items = array();
	$i = 0;
	if (isset($groupVars[$groupName])) {
	    foreach ($groupVars[$groupName] as $var) {
		$item = new \ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSetting($i, $var, $this->getRecipient());
		$this->pagerFrame->addItem($item);
		$this->items[] = $item;
		$i++;
	    }
	}
    }

    public function switchGroup($login, $groupName) {
	$this->populate($this->configManager, $groupName);
	$this->redraw();
    }

    public function applySettings($login, $args) {
	print_r($args);
	foreach($this->items as $item){
	    $var = $item->getVar();
	    if($var != null){
		$var->setValue($item->getVarValue($args));
	    }
	}
	$this->configManager->check();
	$this->populate($this->configManager, $this->currentGroup);
	$this->redraw();
    }

}

?>
