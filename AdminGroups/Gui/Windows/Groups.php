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
        $config = \ManiaLive\DedicatedApi\Config::getInstance();

        $this->adminGroups = AdminGroups::getInstance();
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
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
        $this->pager->setSize($this->sizeX - 4, $this->sizeY - 27);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(8, -12);

        $this->group_add->setSize($this->sizeX - 20, 7);
        $this->group_add->setPosition(4, -7);

        $this->button_add->setSize(30, 5);
        $this->button_add->setPosition($this->sizeX - 35, -7);
    }

    function onShow() {
        foreach ($this->items as $item) 
        {
            $item->destroy();
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
        $label->setText(__(AdminGroups::$txt_groupName,$this->getRecipient()));
        $label->setScale(0.8);
        $frame->addComponent($label);

        $label = new \ManiaLib\Gui\Elements\Label(20, 4);
        $label->setAlign('left', 'center');
        $label->setText(__(AdminGroups::$txt_nbPlayers, $this->getRecipient()));
        $label->setScale(0.8);
        $frame->addComponent($label);

        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->adminGroups->getGroupList() as $group) {
            $this->items[$x] = new GroupItem($group, $this, $login);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    function click_add($login2, $args) {
        $groupName = $args['group_name'];

        if ($groupName != "") {
            $this->adminGroups->addGroup($login2, $groupName);
        }

        $this->group_add->setText("");
        $this->onShow();
        $this->redraw($login2);
    }

    public function changePermission($login, $group) {
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Permissions::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_permissionsTitle, $login, $group->getGroupName()));
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function playerList($login, $group) {
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Players::Create($login);
        $window->setGroup($group);
        $window->setTitle(__(AdminGroups::$txt_playersTitle, $login, $group->getGroupName()));
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
    }
    
    public function deleteGroup($login, $group){
        $this->adminGroups->removeGroup($login, $group);
        $this->onShow();
        $this->redraw($login);
    }
    
    public function destroy() {
        $this->group_add->destroy();
        $this->button_add->destroy();
        
        parent::destroy();
    }
}

?>
