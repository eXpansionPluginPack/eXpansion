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

namespace ManiaLivePlugins\eXpansion\Widgets_ChatEnhancement;


use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Chat\MetaData as ChatMetaData;
use ManiaLivePlugins\eXpansion\Core\Events\PluginSettingChange;
use ManiaLivePlugins\eXpansion\Core\Events\PluginSettingChangeListener;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

class Widgets_ChatEnhancement extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements PluginSettingChangeListener
{

    private $action_chatLog;
    private $action_chatStatus;

    public function exp_onLoad()
    {
    }

    public function exp_onReady()
    {
        /** @var ActionHandler $actionH */
        $actionH = ActionHandler::getInstance();

        $this->action_chatLog = $actionH->createAction(array($this, "showChatLog"));
        $this->action_chatStatus = $actionH->createAction(array($this, "toogleChatStatus"));

        $this->enableDedicatedEvents();
        $this->updateWidget();

        Dispatcher::register(PluginSettingChange::getClass(), $this);
    }

    public function updateWidget($login = null)
    {
        $localRecs = Gui\Widgets\Chat::GetAll();
        if ($login == null) {
            //Gui\Widgets\AroundMe::EraseAll();
            $panelMain = Gui\Widgets\Chat::Create($login, true, $this->action_chatLog, $this->action_chatStatus);
            $panelMain->setPosition(-161, -66);
            $panelMain->show();
        } else if (isset($localRecs[0]) && !$localRecs[0]->isDestroyed()) {
            $localRecs[0]->show($login);
        }
    }

    public function hideWidget()
    {
        Gui\Widgets\Chat::EraseAll();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        if (!$wasWarmUp) {
            $this->hideWidget();
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->hideWidget();
        $this->updateWidget();
    }

    public function showChatLog($login)
    {
        if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\Chatlog\Chatlog')) {
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Chatlog\Chatlog', 'showLog', $login);
        }
    }

    public function toogleChatStatus($login)
    {
        if (AdminGroups::hasPermission($login, Permission::game_settings)) {
            if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\Chat\Chat')) {
                $var = ChatMetaData::getInstance()->getVariable('publicChatActive');
                $var->setRawValue(!$var->getRawValue());

                $this->exp_chatSendServerMessage("#admin_action#Public chat is now #variable#" . ($var->getRawValue() ? "Enable" : "Disable"));

            } else {
                $this->exp_chatSendServerMessage("#admin_error#Custom Chat plugin needs to be enabled", $login);
            }
        }
    }

    /**
     * @param string   $pluginId
     * @param Variable $var
     *
     * @return mixed
     */
    public function onPluginSettingsChange($pluginId, Variable $var)
    {
        if ($pluginId == '\ManiaLivePlugins\eXpansion\Chat\Chat' && $var->getName() == "publicChatActive") {
            $this->hideWidget();
            $this->updateWidget();
        }
    }

    public function exp_onUnload()
    {
        parent::exp_onUnload();

        /** @var ActionHandler $actionH */
        $actionH = ActionHandler::getInstance();

        $actionH->deleteAction($this->action_chatLog);
        $actionH->deleteAction($this->action_chatStatus);

        Gui\Widgets\Chat::EraseAll();
        Dispatcher::unregister(PluginSettingChange::getClass(), $this);
    }
}