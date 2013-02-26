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

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
		
		$this->adminGroups = AdminGroups::getInstance();
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
		
		$this->group_add = new Inputbox("group_name");
		$this->group_add->setLabel(__("New Group Name : "));
		$this->group_add->setText("");
		$this->group_add->setScale(0.8);
		$this->mainFrame->addComponent($this->group_add);

		$this->button_add = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
		$this->button_add->setText(__("Add"));
		$this->button_add->setAction($this->createAction(array($this, 'click_add')));
		$this->button_add->setScale(0.8);
		$this->mainFrame->addComponent($this->button_add);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 4, $this->sizeY - 27);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(8, -17);
		
		$this->group_add->setSize($this->sizeX - 20, 7);
		$this->group_add->setPosition(4, -12);

		$this->button_add->setSize(30, 5);
		$this->button_add->setPosition($this->sizeX - 35, -12);
    }

    function onShow() {
		$this->pager->clearItems();
        $this->populateList();
    }

    function populateList() {

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize(120, 4);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->pager->addItem($frame);

        $label = new \ManiaLib\Gui\Elements\Label(35, 4);
        $label->setAlign('left', 'center');
        $label->setText(__('$wGroup Name'));
        $label->setScale(0.8);
        $frame->addComponent($label);

        $label = new \ManiaLib\Gui\Elements\Label(20, 4);
        $label->setAlign('left', 'center');
        $label->setText(__('$wNb Players'));
        $label->setScale(0.8);
        $frame->addComponent($label);

        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->adminGroups->getGroupList() as $group) {
            $this->pager->addItem(new GroupItem($group, $this, $login));
        }
    }
	function click_add($login2, $args) {
		$groupName = $args['group_name'];
		
		if($groupName != ""){
			$this->adminGroups->addGroup($login2, $groupName);
		}
		
		$this->group_add->setText("");
		$this->onShow();
		$this->redraw($login2);
	}

    public function changePermission($login, $group) {
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Permissions::Create($login);
        $window->setGroup($group);
        $window->setTitle(__('Admin Group Permission - %s', $group->getGroupName()));
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function playerList($login, $group) {
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Players::Create($login);
        $window->setGroup($group);
        $window->setTitle(__('Admin Group Players - %s', $group->getGroupName()));
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
    }

}

?>
