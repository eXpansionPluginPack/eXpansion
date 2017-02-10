<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSetting;
use ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSettingsMenu;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of ExpSettings
 *
 * @author De Cramer Oliver
 */
class ExpSettings extends Window
{

    /** @var ExpSettingsMenu */
    public $menuFrame = null;
    /** @var Pager */
    public $pagerFrame = null;
    public $actions = array();
    public $items = array();
    /** @var  ConfigManager */
    public $configManager;

    /** @var string */
    protected $currentGroup = "";
    protected $first = false;
    protected $confName = "main";

    /** @var  Button */
    protected $button_validate;
    /** @var  Button */
    protected $button_cancel;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->menuFrame = new ExpSettingsMenu();

        $this->pagerFrame = new Pager();
        $this->pagerFrame->setPosY(3);

        $this->mainFrame->addComponent($this->pagerFrame);
        $this->mainFrame->addComponent($this->menuFrame);

        $this->button_validate = new Button();
        $this->button_validate->setText("Save");
        $this->button_validate->setAction($this->createAction(array($this, 'applySettings')));
        $this->mainFrame->addComponent($this->button_validate);
    }

    /**
     * @param $oldX
     * @param $oldY
     */
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
            /** @var Variable $var */
            foreach ($groupVars[$this->currentGroup] as $var) {
                if ($var->getVisible()) {
                    $item = new ExpSetting($i, $var, $this->getRecipient(), $this);
                    $this->pagerFrame->addItem($item);
                    $this->items[] = $item;
                    $i++;
                }
            }
        }
    }

    /**
     * @param $login
     * @param $groupName
     */
    public function switchGroup($login, $groupName)
    {
        $this->populate($this->configManager, $groupName, $this->confName);
        $this->redraw();
    }

    /**
     * @param $login
     * @param null $args
     */
    public function applySettings($login, $args = null)
    {
        /** @var ExpSetting $item */
        foreach ($this->items as $item) {
            $var = $item->getVar();
            if ($var != null) {
                $var->setValue($item->getVarValue($args));
            }
        }
        $this->configManager->check();
        $this->populate($this->configManager, $this->currentGroup, $this->confName);
        $this->redraw();
        $msg = eXpGetMessage("Settings are now saved!");
        Gui::showNotice($msg, $login);
    }

    /**
     *
     */
    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;
        parent::destroy();
    }
}
