<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\GroupItem;

/**
 * Description of Groups
 *
 * @author oliverde8
 */
class Groups extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {
	
	private $pager;

	protected function onConstruct() {
		parent::onConstruct();
		$config = \ManiaLive\DedicatedApi\Config::getInstance();

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
        $label->setText('Group Name');
        $label->setScale(0.8);
		$frame->addComponent($label);
		
		$label = new \ManiaLib\Gui\Elements\Label(20, 4);
		$label->setAlign('left', 'center');
        $label->setText('Nb Players');
        $label->setScale(0.8);
		$frame->addComponent($label);
		
		$x=0;
        $login = $this->getRecipient();
        foreach (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::$groupList as $group){
            $this->pager->addItem(new GroupItem($group, $this, $login));
		}
	}
	
	public function changePermission($login, $group){
		\ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Permissions::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Permissions::Create($login);
		$window->setGroup($group);
        $window->setTitle('Admin Group Permission - '.$group->getGroupName());
        $window->setSize(80, 100);
        $window->centerOnScreen();
        $window->show();
	}
	
	public function addPlayer($login, $group){
		echo $group->getGroupName()."\n";
	}
	
	public function playerList($login, $group){
		
	}

}

?>
