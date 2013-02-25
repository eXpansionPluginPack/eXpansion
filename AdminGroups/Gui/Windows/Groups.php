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
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 4, $this->sizeY - 20);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(8, -10);
    }

    function onShow() {
        $this->populateList();
    }

    function populateList() {

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize(120, 4);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->pager->addItem($frame);

        $label = new \ManiaLib\Gui\Elements\Label(35, 4);
        $label->setAlign('left', 'center');
        $label->setText(_('Group Name'));
        $label->setScale(0.8);
        $frame->addComponent($label);

        $label = new \ManiaLib\Gui\Elements\Label(20, 4);
        $label->setAlign('left', 'center');
        $label->setText(_('Nb Players'));
        $label->setScale(0.8);
        $frame->addComponent($label);

        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->adminGroups->getGroupList() as $group) {
            $this->pager->addItem(new GroupItem($group, $this, $login));
        }
    }

    public function changePermission($login, $group) {
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Permissions::Create($login);
        $window->setGroup($group);
        $window->setTitle(_('Admin Group Permission - %s', $group->getGroupName()));
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function playerList($login, $group) {
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Players::Create($login);
        $window->setGroup($group);
        $window->setTitle(_('Admin Group Players - %s', $group->getGroupName()));
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
    }

}

?>
