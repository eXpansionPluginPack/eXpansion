<?php
namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Core\Gui\Controls\ConfElement;
use ManiaLivePlugins\eXpansion\Core\MetaData;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class ConfSwitcher extends Window
{

    /**
     * @var Pager
     */
    public $pagerFrame = null;

    /**
     * @var ConfigManager
     */
    private $configManager = null;

    /**
     * @var ConfElement[]
     */
    protected $items = array();

    /** @var  Inputbox */
    protected $input;

    /** @var  Button */
    protected $buttonSave;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->pagerFrame = new Pager();
        $this->pagerFrame->setPosY(-2);

        $this->mainFrame->addComponent($this->pagerFrame);

        $this->configManager = ConfigManager::getInstance();

        $this->input = new Inputbox("name", 35, true);
        $this->input->setScale(0.8);
        $this->mainFrame->addComponent($this->input);

        $this->buttonSave = new Button(20, 5);
        $this->buttonSave->setText(__("Save"));
        $this->buttonSave->setAction($this->createAction(array($this, 'saveAction')));
        $this->buttonSave->setScale(0.8);
        $this->mainFrame->addComponent($this->buttonSave);

    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pagerFrame->setSize($this->getSizeX() - 3, $this->getSizeY() - 8);

        $this->input->setSize($this->sizeX * (1 / 0.8) - 60, 7);
        $this->input->setPosition(0, -3);

        $this->buttonSave->setSize(30, 5);
        $this->buttonSave->setPosition($this->sizeX * (1 / 0.8) - 60 * (1 / 0.8), -3);
    }


    public function populate(Variable $var)
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;

        $this->pagerFrame->clearItems();
        $this->items = array();

        $this->populateFromDir($var, ConfigManager::DIRNAME, true);
        $this->populateFromDir($var, 'libraries/ManiaLivePlugins/eXpansion/Core/defaultConfigs', false);
    }

    public function populateFromDir(Variable $var, $dir, $diff)
    {
        $helper = Helper::getPaths();

        if (is_dir($dir)) {
            $subFiles = scandir($dir);
            $i = 0;
            foreach ($subFiles as $file) {
                if ($helper->fileHasExtension($file, '.user.exp')) {
                    $item = new ConfElement($i, $file, $file == ($var->getRawValue() . '.user.exp'), $diff, $this->getRecipient(), $dir);
                    $i++;
                    $this->items[] = $item;
                    $this->pagerFrame->addItem($item);
                }
            }
        }
    }

    public function saveAction($login, $params)
    {
        $name = $params['name'];
        if ($name != "") {
            $name .= '.user.exp';
            /** @var ConfigManager $confManager */
            $confManager = ConfigManager::getInstance();

            $confManager->saveSettingsIn($name);
            $var = MetaData::getInstance()->getVariable('saveSettingsFile');
            $var->hideConfWindow($login);
            $var->showConfWindow($login);
        }
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;
        $this->pagerFrame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
