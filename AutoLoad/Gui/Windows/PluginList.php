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

use ManiaLib\Gui\Elements\Label;
use ManiaLive\PluginHandler\PluginHandler;
use ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad;
use ManiaLivePlugins\eXpansion\AutoLoad\Gui\Controls\Plugin;
use ManiaLivePlugins\eXpansion\Core\types\config\MetaData;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;

class PluginList extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    /**
     * @var Inputbox
     */
    protected $input_name;
    protected $input_author;

    /**
     * @var String
     */
    protected $value_name;
    protected $value_author;

    /**
     * @var Dropdown
     */
    protected $select_group;

    /**
     * @var Label
     */
    protected $label_group;

    /**
     * @var String
     */
    protected $value_group;

    protected $elements = array();

    /**
     * @var Button
     */
    protected $button_search;

    /**
     * @var Pager
     */
    public $pagerFrame = null;

    /**
     * @var PluginHandler
     */
    protected $pluginHandler = null;

    /**
     * @var Plugin[]
     */
    protected $items = array();

    /**
     * @var MetaData[]
     */
    protected $pluginList = array();

    /**
     * @var AutoLoad
     */
    protected $autoLoad;

    public $firstDisplay = true;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->input_name = new Inputbox('name');
        $this->input_name->setSizeX(25);
        $this->input_name->setLabel(__("Name", $login));
        $this->input_name->setPositionX(-3);
        $this->mainFrame->addComponent($this->input_name);

        $this->input_author = new Inputbox('author');
        $this->input_author->setSizeX(25);
        $this->input_author->setPositionX(23);
        $this->input_author->setLabel(__("Author", $login));
        $this->mainFrame->addComponent($this->input_author);

        $this->label_group = new Label();
        $this->label_group->setText(__("Group", $login));
        $this->label_group->setPosition(49, 4);
        $this->label_group->setScale(0.8);
        $this->mainFrame->addComponent($this->label_group);

        $this->select_group = new Dropdown("group", array('Select'), 0, 25);
        $this->select_group->setPositionX(49);
        $this->mainFrame->addComponent($this->select_group);

        $this->button_search = new Button(20);
        $this->button_search->setPositionX(87);
        $this->button_search->setText(__("Search", $login));
        $this->button_search->colorize('0a0');
        $this->button_search->setAction($this->createAction(array($this, "doSearch")));
        $this->mainFrame->addComponent($this->button_search);

        $this->pagerFrame = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->pagerFrame->setPosY(-3);

        $this->mainFrame->addComponent($this->pagerFrame);

        $this->pluginHandler = PluginHandler::getInstance();
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pagerFrame->setSize($this->getSizeX() - 3, $this->getSizeY() - 11);
    }

    /**
     * @param AutoLoad $autoLoader
     * @param MetaData[] $availablePlugins
     */
    public function populate(AutoLoad $autoLoader, $availablePlugins)
    {

        $this->pluginList = $availablePlugins;
        $this->autoLoad = $autoLoader;
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;

        $this->pagerFrame->clearItems();

        $this->items = array();

        $groups = array();

        $i = 0;

        $groups['All'] = true;

        foreach ($availablePlugins as $metaData) {
            if ($this->firstDisplay) {
                foreach ($metaData->getGroups() as $name) {
                    if ($name != "Core") {
                        $groups[$name] = true;
                    }
                }
            }

            $text = $this->input_name->getText();
            if (!empty($text) && strpos(strtoupper($metaData->getName()), strtoupper($text)) === false) {
                continue;
            }

            $text = $this->input_author->getText();
            if (!empty($text) && strpos(strtoupper($metaData->getAuthor()), strtoupper($text)) === false) {
                continue;
            }

            if (!empty($this->value_group) && $this->value_group != "All" && !in_array($this->value_group, $metaData->getGroups())) {
                continue;
            }

            // hide core plugins as you can't really load/unload them
            if (in_array("Core", $metaData->getGroups())) {
                continue;
            }

            $metaData->checkAll();
            $control = new Plugin($i++, $autoLoader, $metaData, $this->getRecipient(), $this->pluginHandler->isLoaded($metaData->getPlugin()));
            $this->items[] = $control;
            $this->pagerFrame->addItem($control);
        }

        if ($this->firstDisplay) {
            $groups = array_keys($groups);
            sort($groups, SORT_STRING);
            $this->select_group->addItems($groups);
            $this->elements = $groups;
        }

        $this->firstDisplay = false;
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;
        $this->pagerFrame->destroy();
        $this->destroyComponents();

        $this->autoLoad = null;

        parent::destroy();
    }

    public function doSearch($login, $params)
    {
        $this->input_name->setText($params['name']);
        $this->input_author->setText($params['author']);
        $this->value_group = $params['group'] == "" ? "" : $this->elements[$params['group']];

        $this->populate($this->autoLoad, $this->pluginList);
        $this->select_group->setSelected($params['group']);
        $this->redraw($login);
    }

}
