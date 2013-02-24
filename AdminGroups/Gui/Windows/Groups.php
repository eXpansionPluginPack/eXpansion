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
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(8, -10);
    }
	
	function onShow() {
        $this->populateList();
    }

    function populateList() {
		
		$x=0;
        $login = $this->getRecipient();
        foreach (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::$groupList as $group){
            $this->pager->addItem(new GroupItem($group, $this, $login));
		}
	}
	
	public function changePermission($login, $group){
		
	}
	
	public function addPlayer($login, $group){
		
	}
	
	public function playerList($login, $group){
		
	}

}

?>
