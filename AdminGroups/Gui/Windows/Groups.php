<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\GroupItem;
use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of Groups
 *
 * @author oliverde8
 */
class Groups extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $adminGroups;
    private $pager;
    private $group_add;
    private $button_add;
    private $items = array();

    protected function onConstruct() {
        parent::onConstruct();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->group_add = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("group_name");
        $this->group_add->setLabel(__(AdminGroups::$txt_nwGroupNameL));
        $this->group_add->setText("");
        $this->group_add->setScale(0.8);
        $this->mainFrame->addComponent($this->group_add);

        $this->button_add = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_add->setText(__(AdminGroups::$txt_add));
        $this->button_add->setAction($this->createAction(array($this, 'click_add')));
        $this->button_add->setScale(0.8);
        $this->mainFrame->addComponent($this->button_add);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 4, $this->sizeY - 12);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(0, -7);

        $this->group_add->setSize($this->sizeX * (1 / 0.8) - 20, 7);
        $this->group_add->setPosition(0, -3);

        $this->button_add->setSize(30, 5);
        $this->button_add->setPosition($this->sizeX * (1 / 0.8) - 45 * (1 / 0.8), -3);
    }

    function onShow() {
        foreach ($this->items as $item) {
            $item->erase();
        }


        $this->pager->clearItems();
        $this->items = array();

        $this->group_add->setLabel(__(AdminGroups::$txt_nwGroupNameL, $this->getRecipient()));
        $this->button_add->setText(__(AdminGroups::$txt_add, $this->getRecipient()));

        $this->populateList();
    }

    function populateList() {

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize(120, 4);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->pager->addItem($frame);

        $label = new \ManiaLib\Gui\Elements\Label(35, 4);
        $label->setAlign('left', 'center');
        $label->setText(__(AdminGroups::$txt_groupName, $this->getRecipient()));
        $label->setScale(0.8);
        $frame->addComponent($label);

        $label = new \ManiaLib\Gui\Elements\Label(20, 4);
        $label->setAlign('left', 'center');
        $label->setText(__(AdminGroups::$txt_nbPlayers, $this->getRecipient()));
        $label->setScale(0.8);
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

    function click_add($login2, $args) {
        $groupName = $args['group_name'];
        $adminGroups = AdminGroups::getInstance();
        if ($groupName != "") {
            $adminGroups->addGroup($login2, $groupName);
        }

        $this->group_add->setText("");
        $this->onShow();
        $this->redraw($login2);

        $windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();

        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
        }
    }

    public function changePermission($login, $group) {
        \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Permissions::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Permissions::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_permissionsTitle, $login, $group->getGroupName()));
        $window->setSize(74, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function playerList($login, $group) {
        \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Players::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Players::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_playersTitle, $login, $group->getGroupName()));
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
    }
    
    public function inheritList($login, $group){
        \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Inherits::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Inherits::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_permissionsTitle, $login, $group->getGroupName()));
        $window->setSize(74, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function deleteGroup($login, $group) {
        $adminGroups = AdminGroups::getInstance();
        $adminGroups->removeGroup($login, $group);
        $this->onShow();
        $this->redraw($login);

        $windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();

        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
        }
    }

    public function destroy() {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        $this->pager->destroy();
        $this->group_add->destroy();
        $this->button_add->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
