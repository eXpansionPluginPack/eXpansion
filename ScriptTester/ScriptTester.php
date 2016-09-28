<?php

namespace ManiaLivePlugins\eXpansion\ScriptTester;

use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\ScriptTester\Gui\Widgets\ScriptEditor;

/**
 * Description of Netstat
 *
 * @author Petri
 */
class ScriptTester extends ExpPlugin
{

    private $cmd_edit;
    private $actionId;

    public function eXpOnReady()
    {

        $ahandler = ActionHandler::getInstance();
        $this->actionId = $ahandler->createAction(array($this, "apply"));

        $admingroup = AdminGroups::getInstance();
        $this->cmd_edit = AdminGroups::addAdminCommand("editor", $this, 'showEditor', Permission::SERVER_ADMIN);
    }

    public function showEditor($login)
    {
        $widget = ScriptEditor::Create($login);
        $widget->setActionId($this->actionId);
        $widget->show();
    }

    public function eXpOnUnload()
    {
        parent::eXpOnUnload();
        $admingroup = AdminGroups::getInstance();
        $admingroup->removeAdminCommand($this->cmd_edit);
        $this->cmd_edit = null;
    }

    public function apply($login, $data = array())
    {
        Gui\Widgets\TestWidget::EraseAll();

        $var = MetaData::getInstance()->getVariable('tester_manialink');
        $var->setRawValue($data['manialink']);

        $var = MetaData::getInstance()->getVariable('tester_maniascript');
        $var->setRawValue($data['script']);

        \ManiaLivePlugins\eXpansion\Core\ConfigManager::getInstance()->check();


        try {
            $widget = Gui\Widgets\TestWidget::create($login);
            $widget->setXmlData($data['manialink']);
            $widget->setScriptContent($data['script']);
            $widget->setSize(100, 40);
            $widget->setPosition(-50, 60);
            $widget->show();
        } catch (\Exception $e) {
            $this->eXpChatSendServerMessage("Error: " . $e->getMessage(), $login);
        }
    }
}
