<?php
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

namespace ManiaLivePlugins\eXpansion\AutoLoad\Gui\Windows;


use ManiaLive\PluginHandler\PluginHandler;
use ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad;
use ManiaLivePlugins\eXpansion\AutoLoad\Gui\Controls\Plugin;
use ManiaLivePlugins\eXpansion\Core\types\config\MetaData;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;

class PluginList extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    /**
     * @var Pager
     */
    public $pagerFrame = null;

    /**
     * @var PluginHandler
     */
    private $pluginHandler = null;

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

	$this->pluginHandler = PluginHandler::getInstance();
    }

    public function onResize($oldX, $oldY)
    {
	parent::onResize($oldX, $oldY);
	$this->pagerFrame->setSize($this->getSizeX() - 3, $this->getSizeY() - 8);
    }

    /**
     * @param AutoLoad   $autoLoader
     * @param MetaData[] $availablePlugins
     */
    public function populate(AutoLoad $autoLoader, $availablePlugins)
    {
	$this->pagerFrame->clearItems();
	$this->items = array();

	$i = 0;
	foreach($availablePlugins as $metaData){
	    $control = new Plugin($i++, $autoLoader, $metaData, $this->getRecipient(), $this->pluginHandler->isLoaded($metaData->getPlugin()));
	    $this->items[] = $control;
	    $this->pagerFrame->addItem($control);
	}
    }

    public function destroy() {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;
        $this->pagerFrame->destroy();
        $this->clearComponents();
        parent::destroy();
    }
}