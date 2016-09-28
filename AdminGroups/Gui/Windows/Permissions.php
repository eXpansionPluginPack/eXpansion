<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Group;

/**
 * Description of Permissions
 *
 * @author oliverde8
 */
class Permissions extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $adminGroups;
    protected $pager;
    protected $group;
    protected $button_ok;
    protected $button_cancel;
    protected $action_ok;
    protected $action_cancel;
    protected $items = array();
    protected $permissions = array();

    protected function onConstruct()
    {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->button_ok = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_ok->setText(__("OK"));
        $this->action_ok = $this->createAction(array($this, 'clickOk'));
        $this->button_ok->setAction($this->action_ok);
        $this->mainFrame->addComponent($this->button_ok);

        $this->button_cancel = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_cancel->setText(__("Cancel"));
        $this->action_cancel = $this->createAction(array($this, 'clickCancel'));
        $this->button_cancel->setAction($this->action_cancel);
        $this->mainFrame->addComponent($this->button_cancel);
    }

    public function setGroup(Group $g)
    {
        $this->group = $g;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 12);
        $this->pager->setPosition(1, -1);

        $centerX = $this->sizeX / 2 - 10;
        $this->button_ok->setPosition($centerX + 5, -$this->sizeY + 5);
        $this->button_cancel->setPosition($centerX + 30, -$this->sizeY + 5);
    }

    public function onShow()
    {
        $this->populateList();
    }

    public function populateList()
    {
        foreach ($this->permissions as $item) {
            $item[0]->destroy();
            if ($item[1] != null) {
                $item[1]->destroy();
            }
        }
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->permissions = array();
        $this->items = array();
        $x = 0;
        $adminGroups = AdminGroups::getInstance();
        foreach ($adminGroups->getPermissionList() as $key => $value) {
            $cPermission = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4, 60);
            $cPermission->setStatus($this->group->hasPermission($key));
            $cPermission->setText('$fff' . __(AdminGroups::getPermissionTitleMessage($key), $this->getRecipient()));
            $cPermission->setScale(0.8);

            $cInherit = null;

            $inheritances = $this->group->getInherits();
            if (!empty($inheritances)) {
                $cInherit = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4, 15, $cPermission);
                $cInherit->setText('$fff' . __(AdminGroups::$txt_inherits, $this->getRecipient()) . "?");
                $cInherit->setScale(0.8);
                if ($this->group->getPermission($key) == AdminGroups::UNKNOWN_PERMISSION) {
                    $cPermission->SetIsWorking(false);
                    $cInherit->setStatus(true);
                } else {
                    $cInherit->setStatus(false);
                }
            }

            $this->permissions[$key] = array($cPermission, $cInherit);
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\CheckboxItem(
                $x,
                $cPermission,
                $cInherit
            );
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function clickOk($login)
    {
        $newPermissions = array();
        foreach ($this->permissions as $key => $val) {
            $inheritance = $val[1];
            $permission = $val[0];

            if ($inheritance == null) {
                $newPermissions[$key] = $permission->getStatus() == false ?
                    AdminGroups::UNKNOWN_PERMISSION : AdminGroups::HAVE_PERMISSION;
            } else {
                if ($inheritance->getStatus()) {
                    $newPermissions[$key] = AdminGroups::UNKNOWN_PERMISSION;
                } else {
                    $newPermissions[$key] = $permission->getStatus() == false ?
                        AdminGroups::NO_PERMISSION : AdminGroups::HAVE_PERMISSION;
                }
            }
        }

        $adminGroups = AdminGroups::getInstance();
        $adminGroups->changePermissionOfGroup($login, $this->group, $newPermissions);

        $windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();
        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
            $window->refreshAll();
        }
    }

    public function clickCancel()
    {
        $this->Erase($this->getRecipient());
    }

    public function destroy()
    {
        foreach ($this->permissions as $item) {
            $item[0]->destroy();
            if ($item[1] != null) {
                $item[1]->destroy();
            }
        }
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->permissions = null;
        $this->items = array();
        $this->pager->destroy();
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->action_ok);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->action_cancel);

        $this->button_cancel->destroy();
        $this->button_ok->destroy();

        parent::destroy();
    }
}

