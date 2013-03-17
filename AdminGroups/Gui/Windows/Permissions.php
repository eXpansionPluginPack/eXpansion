<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of Permissions
 *
 * @author oliverde8
 */
class Permissions extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $adminGroups;
    private $pager;
    private $group;
    private $button_ok;
    private $button_cancel;
    private $action_ok;
    private $action_cancel;
    
    private $permissions = array();

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();

        $this->adminGroups = AdminGroups::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->button_ok = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_ok->setText(__("OK"));
        $this->action_ok = $this->createAction(array($this, 'click_ok'));
        $this->button_ok->setAction($this->action_ok);
        $this->mainFrame->addComponent($this->button_ok);

        $this->button_cancel = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_cancel->setText(__("Cancel"));
        $this->action_cancel = $this->createAction(array($this, 'click_cancel'));
        $this->button_cancel->setAction($this->action_cancel);
        $this->mainFrame->addComponent($this->button_cancel);
    }

    public function setGroup($g) {
        $this->group = $g;
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 12);
        $this->pager->setPosition(1, -1);

        $centerX = $this->sizeX / 2 - 10;
        $this->button_ok->setPosition($centerX + 5, -$this->sizeY + 5);
        $this->button_cancel->setPosition($centerX + 30, -$this->sizeY + 5);
    }

    function onShow() {
        $this->populateList();
    }

    function populateList() {
        foreach ($this->permissions as $item)
            $item->destroy();
        $this->pager->clearItems();
        $this->permissions = array();
        
        $x=0;
        foreach ($this->adminGroups->getPermissionList() as $key => $value) {
            $cBox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4, 68);
            $cBox->setStatus($this->group->hasPermission($key));
            $cBox->setText($key);
            $cBox->setScale(0.8);
            
            $frame = new \ManiaLive\Gui\Controls\Frame();
            $frame->setSize(68, 4);
            $frame->addComponent(new ListBackGround($x++, 68, 4));
            $frame->addComponent($cBox);
            
            $this->permissions[$key] = $frame;
            $this->pager->addItem($frame);
        }
    }

    function click_ok($login) {
        $newPermissions = array();
        foreach ($this->permissions as $key => $val) {
            $newPermissions[$key] = $val->getStatus();
        }
        $this->adminGroups->changePermissionOfGroup($login, $this->group, $newPermissions);
        $this->Erase($login);
    }

    function click_cancel() {
        $this->Erase($this->getRecipient());
    }

    public function destroy() {
        foreach ($this->permissions as $item)
            $item->destroy();

        $this->permissions = null;
        $this->pager->destroy();
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->action_ok);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->action_cancel);
        
        $this->button_cancel->destroy();
        $this->button_ok->destroy();
                
        parent::destroy();
    }

}

?>
