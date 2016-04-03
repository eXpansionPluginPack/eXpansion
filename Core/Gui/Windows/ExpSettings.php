<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use ManiaLivePlugins\eXpansion\Core\ConfigManager;

/**
 * Description of ExpSettings
 *
 * @author De Cramer Oliver
 */
class ExpSettings extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    /**
     *
     * @var \ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSettingsMenu
     */
    public $menuFrame = null;
    public $pagerFrame = null;
    public $actions = array();
    public $items = array();
    public $configManager;
    protected $currentGroup = "";
    protected $first = false;
    protected $confName = "main";

    protected $button_validate = null;
    protected $button_cancel = null;

    protected function onConstruct()
    {
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

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->menuFrame->setSizeX($this->getSizeX() / 4);
        $this->menuFrame->setSizeY($this->getSizeY());

        $this->pagerFrame->setPosX($this->getSizeX() / 4);
        $this->pagerFrame->setSize($this->getSizeX() * 3 / 4 - 3, $this->getSizeY() - 8);

        $this->button_validate->setPosX($this->getSizeX() - $this->button_validate->getSizeX());
        $this->button_validate->setPosY(-$this->getSizeY() + 5);
    }

    /**
     * @param ConfigManager $configs The config manager instance
     * @param string $groupName The name of the group to show
     * @param string $confName The conf name.
     *
     * @see ConfigManager getGroupedVariables
     */
    public function populate(ConfigManager $configs, $groupName, $confName = "main")
    {
        $this->configManager = $configs;
        $this->currentGroup = $groupName;
        $this->confName = $confName;

        $this->refreshInfo();
    }

    public function refreshInfo()
    {


        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;
        $this->pagerFrame->clearItems();

        $groupVars = $this->configManager->getGroupedVariables($this->confName);

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
        if (isset($groupVars[$this->currentGroup])) {
            foreach ($groupVars[$this->currentGroup] as $var) {
                if ($var->getVisible()) {
                    $item = new \ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSetting($i, $var, $this->getRecipient(), $this);
                    $this->pagerFrame->addItem($item);
                    $this->items[] = $item;
                    $i++;
                }
            }
        }
    }

    public function switchGroup($login, $groupName)
    {
        $this->populate($this->configManager, $groupName, $this->confName);
        $this->redraw();
    }

    public function applySettings($login, $args = null)
    {
        foreach ($this->items as $item) {
            $var = $item->getVar();
            if ($var != null) {
                $var->setValue($item->getVarValue($args));
            }
        }
        $this->configManager->check();
        $this->populate($this->configManager, $this->currentGroup, $this->confName);
        $this->redraw();
        $msg = exp_getMessage("Settings are now saved!");
        \ManiaLivePlugins\eXpansion\Gui\Gui::showNotice($msg, $login);
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;
        parent::destroy();
    }

}

?>
