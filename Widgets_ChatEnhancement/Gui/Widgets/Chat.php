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

namespace ManiaLivePlugins\eXpansion\Widgets_ChatEnhancement\Gui\Widgets;

use ManiaLib\Gui\Elements\UIConstructionSimple_Buttons;
use ManiaLive\PluginHandler\PluginHandler;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;
use ManiaLivePlugins\eXpansion\Chat\MetaData as ChatMetaData;

class Chat extends PlainWidget
{

    private $chatLogIcon;
    private $chatState;

    public function onConstruct()
    {
        parent::onConstruct();

        /** @var PluginHandler $phandler */
        $phandler = PluginHandler::getInstance();

        $params = func_get_args();

        if ($phandler->isLoaded('\ManiaLivePlugins\eXpansion\Chatlog\Chatlog')) {
            $this->chatLogIcon = new Button(8, 8);
            $this->chatLogIcon->setIcon('UIConstruction_Buttons', 'Text');
            $this->chatLogIcon->setDescription('Display Chat History');
            $this->chatLogIcon->setAction($params[0]);
            $this->addComponent($this->chatLogIcon);
        }

        $chatEnabled = true;
        if ($phandler->isLoaded('\ManiaLivePlugins\eXpansion\Chat\Chat')) {
            /** @var ChatMetaData $chatMeta */
            $chatMeta = ChatMetaData::getInstance();
            if (!$chatMeta->getVariable('publicChatActive')->getRawValue()) {
                $chatEnabled = false;
            }
        }

        $this->chatState = new Button(4, 4);
        $this->chatState->setIcon('Icons64x64_1', $chatEnabled ? 'LvlGreen' : 'LvlRed');
        $this->chatState->setDescription('Is public chat active');
        $this->chatState->setAction($params[1]);
        $this->chatState->setPositionX(2);
        $this->chatState->setPositionY(-4);
        $this->chatState->setPositionZ(30);
        $this->addComponent($this->chatState);
    }

} 