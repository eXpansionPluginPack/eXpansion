<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

use ManiaLivePlugins\eXpansion\AdminGroups\Admin;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of GroupItem
 *
 * @author oliverde8
 */
class GroupItem extends \ManiaLive\Gui\Control {

	private $group;

	private $action_remove;
	 
	function __construct(Admin $admin, $controller, $login) {
		$this->group = $group;
		$sizeX = 120;
        $sizeY = 4;
		
		$this->action_remove = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'click_remove'), $admin);
		
		$frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize($sizeX, $sizeY);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
		
		$gui_name = new \ManiaLib\Gui\Elements\Label(35, 4);
        $gui_name->setAlign('left', 'center');
        $gui_name->setText($group->getGroupName());
        $gui_name->setScale(0.8);
        $frame->addComponent($gui_name);
		
		$gui_nbPlayers = new \ManiaLib\Gui\Elements\Label(20, 4);
        $gui_nbPlayers->setAlign('center', 'center');
        $gui_nbPlayers->setText(sizeof($group->getGroupUsers()));
        $gui_nbPlayers->setScale(0.8);
        $frame->addComponent($gui_nbPlayers);
		
		if(AdminGroups::hasPermission($login, 'group_admin')){
			
			$plistButton = new MyButton(24, 6);
            $plistButton->setAction($this->action_remove);
            $plistButton->setText(_("Remove"));
            $plistButton->setScale(0.6);
            $frame->addComponent($plistButton);
		}
		
		$this->addComponent($frame);

		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;
		$this->setSize($sizeX, $sizeY);
	}
	
	function __destruct() {
		ActionHandler::getInstance()->deleteAction($this->action_remove);
	}

}

?>
