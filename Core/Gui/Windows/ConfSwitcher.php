<?php
/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
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

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;


use ManiaLive\Gui\Controls\Pager;
use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Core\Gui\Controls\ConfElement;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class ConfSwitcher extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window{

    /**
     * @var Pager
     */
    public $pagerFrame = null;

    /**
     * @var ConfigManager
     */
    private $configManager = null;

    /**
     * @var Plugin[]
     */
    private $items = array();

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->pagerFrame = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->pagerFrame->setPosY(3);

        $this->mainFrame->addComponent($this->pagerFrame);

        $this->configManager = ConfigManager::getInstance();
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pagerFrame->setSize($this->getSizeX() - 3, $this->getSizeY() - 8);
    }


    public function populate(Variable $var)
    {
        $this->pagerFrame->clearItems();
        $this->items = array();

        $helper = Helper::getPaths();

        if (is_dir(ConfigManager::dirName)) {
            $subFiles = scandir(ConfigManager::dirName);
            $i = 0;
            foreach($subFiles as $file){
                if($helper->fileHasExtension($file, '.user.exp')){
                    $item = new ConfElement($i, $file, $file === $var->getRawValue(), $this->getRecipient());
                    $i++;
                    $this->items[] = $item;
                    $this->pagerFrame->addItem($item);
                }
            }
        }
    }

} 