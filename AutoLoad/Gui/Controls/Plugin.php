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

namespace ManiaLivePlugins\eXpansion\AutoLoad\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad;
use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Core\Gui\Windows\ExpSettings;
use ManiaLivePlugins\eXpansion\Core\types\config\MetaData;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

class Plugin extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    /**
     * @var AutoLoad
     */
    protected $autoLoad;

    /**
     * @var MetaData
     */
    protected $metaData;

    /**
     * @var Button
     */
    protected $button_running, $button_titleComp, $button_gameComp, $button_otherComp, $button_more, $button_start;

    /**
     * @var \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround
     */
    protected $bg;

    /**
     * @var Label
     */
    protected $label_name, $label_author;

    /**
     * @var Quad
     */
    protected $icon_name, $icon_author;

    /**
     * @var ConfigManager
     */
    protected $configManger = null;

    public function __construct($indexNumber, AutoLoad $autoload, MetaData $plugin, $login, $isLoaded)
    {

        $this->metaData = $plugin;
        $this->autoLoad = $autoload;
        $toggleAction = $this->createAction(array($this, "togglePlugin"));
        $this->configManger = ConfigManager::getInstance();

        $this->bg = new ListBackGround($indexNumber, 120, 4);
        $this->addComponent($this->bg);
        $guiConfig = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

        $titleCompatible = $plugin->checkTitleCompatibility();
        $gameCompatible = $plugin->checkGameCompatibility();
        $otherCompatible = $plugin->checkOtherCompatibility();
        $isInStart = $autoload->isInStartList($plugin->getPlugin());

        $this->button_running = new Button(8, 8);
        $this->button_running->setIcon('Icons64x64_1', 'GenericButton');
        if ($isLoaded) {
            $this->button_running->colorize('0f0');
        } else {
            if ($isInStart) {
                $this->button_running->colorize('ff0');
            } else {
                $this->button_running->colorize('f00');
            }
        }
        $this->button_running->setAction($toggleAction);
        $this->addComponent($this->button_running);

        $this->label_name = new Label(40, 4);
        //$this->label_name->setScale(0.8);
        $this->label_name->setTextSize(2);
        $this->label_name->setText($plugin->getName() == "" ? $plugin->getPlugin() : $plugin->getName());
        $this->label_name->setPosition(8, 3);
        $this->addComponent($this->label_name);

        $this->label_author = new Label(40, 4);
        $this->label_author->setStyle("TextCardScores2");
        $this->label_author->setTextSize(1);
        //$this->label_author->setScale(0.8);
        $this->label_author->setText('$i' . $plugin->getDescription());
        $this->label_author->setPosition(8, -0.5);
        $this->addComponent($this->label_author);

        $this->button_titleComp = new Button(7, 7);
        $this->button_titleComp->setIcon('Icons64x64_1', 'GenericButton');
        $this->button_titleComp->setDescription(__($this->getTitleDescriptionText($titleCompatible), $login), 100);
        if ($titleCompatible) {
            $this->button_titleComp->colorize('090');
        } else {
            $this->button_titleComp->colorize('f00');
        }
        $this->addComponent($this->button_titleComp);

        $this->button_gameComp = new Button(7, 7);
        $this->button_gameComp->setIcon('Icons64x64_1', 'GenericButton');
        $this->button_gameComp->setDescription(__($this->getGameDescriptionText($gameCompatible), $login), 100);
        if ($gameCompatible) {
            $this->button_gameComp->colorize('090');
        } else {
            $this->button_gameComp->colorize('f00');
        }
        $this->addComponent($this->button_gameComp);

        $this->button_otherComp = new Button(7, 7);
        $this->button_otherComp->setIcon('Icons64x64_1', 'GenericButton');
        $this->button_otherComp->setDescription(__($this->getOtherDescriptionText($otherCompatible), $login), 100, 5, sizeof($otherCompatible) + 1);
        if (empty($otherCompatible)) {
            $this->button_otherComp->colorize('090');
        } else {
            $this->button_otherComp->colorize('f00');
        }
        $this->addComponent($this->button_otherComp);

        $this->button_more = new Button(22, 7);
        //$this->button_more->setIcon("Icons128x128_1", "Options");
        $this->button_more->setText(__("Settings", $login));
        $this->button_more->setAction($this->createAction(array($this, 'showPluginSettings')));
        $configs = $this->configManger->getGroupedVariables($this->metaData->getPlugin());
        if (!empty($configs)) {
            $this->addComponent($this->button_more);
        }

        $this->button_start = new Button(14, 7);
        $this->button_start->setAction($toggleAction);
        $this->button_start->setText(__($this->getStartText($isLoaded, $isInStart), $login));

        if ($this->getStartText($isLoaded, $isInStart) == "Start") {
            $this->button_start->colorize("0D0");
        } else {
            $this->button_start->colorize("F00");
        }
        $this->addComponent($this->button_start);

        $this->setSize(122, 8);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->label_name->setSizeX(($this->getSizeX() - $this->label_name->getPosX() - 5 * 8 - 7) / 1);
        $this->label_author->setSizeX(($this->getSizeX() - $this->label_author->getPosX() - 5 * 8 - 7) / 1);

        $this->bg->setSize($this->getSizeX() + 3, $this->getSizeY());

        $this->button_titleComp->setPositionX($this->getSizeX() - 5 * 5 - 4);
        $this->button_gameComp->setPositionX($this->getSizeX() - 5 * 4 - 3);
        $this->button_otherComp->setPositionX($this->getSizeX() - 5 * 3 - 2);

        $this->button_more->setPositionX($this->getSizeX() - 5 * 8 - 7);
        $this->button_start->setPositionX($this->getSizeX() - 8 * 1 - 2);
    }

    private function getRunningDescriptionText($running, $inStart)
    {
        if ($running) {
            return "Plugin is running. Click to unload!";
        } else {
            if ($inStart) {
                return "Plugin not compatible with game mode, title or server settings.\n Plugin will be enabled when possible.";
            } else {
                return "Plugin not running. Click to load!";
            }
        }
    }

    private function getTitleDescriptionText($titleCompatible)
    {
        if ($titleCompatible) {
            return "This plugin is compatible with the current Title";
        } else {
            return "This plugin isn't compatible with the current Title";
        }
    }

    private function getGameDescriptionText($gameCompatible)
    {
        if ($gameCompatible) {
            return "This plugin is compatible with the current Game mode";
        } else {
            return "This plugin isn't compatible with the current Game mode";
        }
    }

    private function getOtherDescriptionText($otherCompatibility)
    {
        if (empty($otherCompatibility)) {
            return "This plugin is is compatible with current installation";
        } else {
            return "This plugin has a few compatibility issues : \n" . implode("\n", $otherCompatibility);
        }
    }

    private function getStartText($started, $inStart)
    {
        if ($inStart || $started) {
            return "Stop";
        } else {
            return "Start";
        }
    }

    public function togglePlugin($login)
    {
        $this->autoLoad->togglePlugin($login, $this->metaData);
    }

    public function showPluginSettings($login)
    {
        ExpSettings::Erase($login);
        /** @var ExpSettings $win */
        $win = ExpSettings::Create($login);
        $win->setTitle("Expansion Settings");
        $win->centerOnScreen();
        $win->setSize(140, 100);
        $win->populate($this->configManger, 'General', $this->metaData->getPlugin());
        $win->show();
    }

}
