<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

use ManiaLivePlugins\eXpansion\AdminGroups\Group;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of GroupItem
 *
 * @author oliverde8
 */
class GroupItem extends \ManiaLive\Gui\Control {

	private $group;
	
	private $gui_name;
	private $gui_nbPlayers;
	private $plistButton;
	private $aPlayerButton;
	private $permiButton;
	
	private $action_changePermissions;
	private $action_addPlayer;
	private $action_playerList;
	private $action_removeGroup;

	private $frame;
	 
	function __construct(Group $group, $controller, $login) {
		$this->group = $group;
		$sizeX = 120;
        $sizeY = 4;
		
		$this->action_changePermissions = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'changePermission'), $group);
		$this->action_addPlayer = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'addPlayer'), $group);
		$this->action_playerList = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'playerList'), $group);
		
		$this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
		
		$this->gui_name = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->gui_name->setAlign('left', 'center');
        $this->gui_name->setText($group->getGroupName());
        $this->gui_name->setScale(0.8);
        $this->frame->addComponent($this->gui_name);
		
		$this->gui_nbPlayers = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->gui_nbPlayers->setAlign('left', 'center');
        $this->gui_nbPlayers->setText(sizeof($group->getGroupUsers()));
        $this->gui_nbPlayers->setScale(0.8);
        $this->frame->addComponent($this->gui_nbPlayers);
		
		if(AdminGroups::hasPermission($login, 'group_admin') || (
				AdminGroups::hasPermission($login, 'own_group') && AdminGroups::getAdmin($login)->getGroup()->getGroupName() == $group->getGroupName())){
			
			$this->plistButton = new MyButton(24, 6);
            $this->plistButton->setAction($this->action_playerList);
            $this->plistButton->setText("Player List");
            $this->plistButton->setScale(0.6);
            $this->frame->addComponent($this->plistButton);
			
			$this->aPlayerButton = new MyButton(24, 6);
            $this->aPlayerButton->setAction($this->action_addPlayer);
            $this->aPlayerButton->setText("Add Player");
            $this->aPlayerButton->setScale(0.6);
            $this->frame->addComponent($this->aPlayerButton);
			
			$this->permiButton = new MyButton(24, 6);
            $this->permiButton->setAction($this->action_changePermissions);
            $this->permiButton->setText("Change Permissions");
            $this->permiButton->setScale(0.6);
            $this->frame->addComponent($this->permiButton);
		}
		
		$this->addComponent($this->frame);

		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;
		$this->setSize($sizeX, $sizeY);
	}
	
	function __destruct() {
		ActionHandler::getInstance()->deleteAction($this->action_changePermissions);
		ActionHandler::getInstance()->deleteAction($this->action_addPlayer);
		ActionHandler::getInstance()->deleteAction($this->action_playerList);
	}

}

?>
