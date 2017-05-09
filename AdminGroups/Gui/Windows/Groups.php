<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Group;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\GroupItem;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of Groups
 *
 * @author oliverde8
 */
class Groups extends Window
{
    /** @var  Pager */
    protected $pager;
    /** @var  Inputbox */
    protected $group_add;
    /** @var  Button */
    protected $button_add;
    protected $items = array();

    /**
     *
     */
    protected function onConstruct()
    {
        parent::onConstruct();

        $this->pager = new Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->group_add = new Inputbox("group_name");
        $this->group_add->setLabel(__(AdminGroups::$txt_nwGroupNameL));
        $this->group_add->setText("");
        $this->mainFrame->addComponent($this->group_add);

        $this->button_add = new Button();
        $this->button_add->setText(__(AdminGroups::$txt_add));
        $this->button_add->setAction($this->createAction(array($this, 'clickAdd')));
        $this->mainFrame->addComponent($this->button_add);
    }

    /**
     * @param $oldX
     * @param $oldY
     */
    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 12);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(0, -7);

        $this->group_add->setSize($this->sizeX - 45, 7);
        $this->group_add->setPosition(0, -3);

        $this->button_add->setPosition($this->sizeX - 40, -3);
    }

    /**
     *
     */
    public function onShow()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();

        $this->group_add->setLabel(__(AdminGroups::$txt_nwGroupNameL, $this->getRecipient()));
        $this->button_add->setText(__(AdminGroups::$txt_add, $this->getRecipient()));

        $this->populateList();
    }

    /**
     *
     */
    public function populateList()
    {

        $frame = new Frame();
        $frame->setSize(120, 4);
        $frame->setLayout(new Line());
        $this->pager->addItem($frame);

        $label = new Label(35, 4);
        $label->setAlign('left', 'center');
        $label->setText(__(AdminGroups::$txt_groupName, $this->getRecipient()));
        $frame->addComponent($label);

        $label = new Label(40, 4);
        $label->setAlign('left', 'center');
        $label->setText(__(AdminGroups::$txt_nbPlayers, $this->getRecipient()));
        $frame->addComponent($label);

        $x = 0;
        $login = $this->getRecipient();
        $adminGroups = AdminGroups::getInstance();
        foreach ($adminGroups->getGroupList() as $group) {
            $this->items[$x] = new GroupItem($x, $group, $this, $login);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    /**
     * @param $login2
     * @param $args
     */
    public function clickAdd($login2, $args)
    {
        $groupName = $args['group_name'];
        /** @var AdminGroups $adminGroups */
        $adminGroups = AdminGroups::getInstance();
        if ($groupName != "") {
            $adminGroups->addGroup($login2, $groupName);
        }

        $this->group_add->setText("");
        $this->onShow();
        $this->redraw($login2);

        $windows = Groups::GetAll();

        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
        }
    }

    /**
     * @param $login
     * @param Group $group
     */
    public function changePermission($login, $group)
    {
        Permissions::Erase($login);
        /** @var Permissions $window */
        $window = Permissions::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_permissionsTitle, $login, $group->getGroupName()));
        $window->setSize(90, 100);
        $window->centerOnScreen();
        $window->show();
    }

    /**
     * @param $login
     * @param Group $group
     */
    public function playerList($login, $group)
    {
        Players::Erase($login);
        /** @var Players $window */
        $window = Players::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_playersTitle, $login, $group->getGroupName()));
        $window->setSize(88, 100);
        $window->centerOnScreen();
        $window->show();
    }

    /**
     * @param $login
     * @param Group $group
     */
    public function inheritList($login, $group)
    {
        Inherits::Erase($login);
        /** @var Inherits $window */
        $window = Inherits::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_permissionsTitle, $login, $group->getGroupName()));
        $window->setSize(74, 100);
        $window->centerOnScreen();
        $window->show();
    }

    /**
     * @param $login
     * @param Group $group
     */
    public function deleteGroup($login, $group)
    {
        $adminGroups = AdminGroups::getInstance();
        $adminGroups->removeGroup($login, $group);
        $this->onShow();
        $this->redraw($login);

        $windows = Groups::GetAll();
        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
        }
    }

    /**
     *
     */
    public function refreshAll()
    {
        /** @var Players[] $windows */
        $windows = Players::GetAll();
        foreach ($windows as $window) {
            $window->setGroup(AdminGroups::getInstance()->getGroup($window->getGroup()->getGroupName()));
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
        }

        /** @var Inherits[] $windows */
        $windows = Inherits::GetAll();
        foreach ($windows as $window) {
            $window->setGroup(AdminGroups::getInstance()->getGroup($window->getGroup()->getGroupName()));
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
        }

        /** @var Permissions[] $windows */
        $windows = Permissions::GetAll();
        foreach ($windows as $window) {
            $window->setGroup(AdminGroups::getInstance()->getGroup($window->getGroup()->getGroupName()));
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
        }
    }

    /**
     *
     */
    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        $this->pager->destroy();
        $this->group_add->destroy();
        $this->button_add->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
