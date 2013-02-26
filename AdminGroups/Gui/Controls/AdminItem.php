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
class AdminItem extends \ManiaLive\Gui\Control {

	private $admin;

	private $action_remove;
	 
	function __construct(Admin $admin, $controller, $login) {
		$this->group = $admin;
		$sizeX = 120;
        $sizeY = 4;
		
		$this->action_remove = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'click_remove'), $admin);
		
		$frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize($sizeX, $sizeY);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
		
		$gui_name = new \ManiaLib\Gui\Elements\Label(35, 4);
        $gui_name->setAlign('left', 'center');
        $gui_name->setText($admin->getLogin());
        $gui_name->setScale(0.8);
        $frame->addComponent($gui_name);
		
		$player = \ManiaLive\Data\Storage::getInstance()->getPlayerObject($admin->getLogin());
		$gui_nick = new \ManiaLib\Gui\Elements\Label(20, 4);
        $gui_nick->setAlign('left', 'center');
        $gui_nick->setText($player!=null?$player->nickName:"");
        $gui_nick->setScale(0.8);
        
		$frame->addComponent($gui_nick);
		
		if(AdminGroups::hasPermission($login, 'group_admin')){
			
			$plistButton = new MyButton(24, 6);
            $plistButton->setAction($this->action_remove);
            $plistButton->setText(__("Remove"));
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
