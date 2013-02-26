<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\AdminItem;

/**
 * Description of Permissions
 *
 * @author oliverde8
 */
class Players extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {
	
	private $adminGroups;
	private $pager;
	private $group;
	
	private $button_add;
	private $login_add;
	
	private $permissions = array();
	
	protected function onConstruct() {
		parent::onConstruct();
		$config = \ManiaLive\DedicatedApi\Config::getInstance();
		
		$this->adminGroups = AdminGroups::getInstance();
		
		$this->pager = new \ManiaLive\Gui\Controls\Pager();
		$this->mainFrame->addComponent($this->pager);
		
		$this->login_add = new Inputbox("login");
        $this->login_add->setLabel(_("Login : "));
        $this->login_add->setText("");
        $this->mainFrame->addComponent($this->login_add);
		
		$this->button_add = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
		$this->button_add->setText(_("Add"));
		$this->button_add->setAction($this->createAction(array($this, 'click_add')));
		$this->mainFrame->addComponent($this->button_add);
		
	}
	
	public function setGroup($g){
		$this->group = $g;
	}


	function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 25);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -17);
		
		$this->login_add->setSize($this->sizeX - 20, 7);
		$this->login_add->setPosition(4, -12);
		
		$this->button_add->setSize(30, 5);
		$this->button_add->setPosition($this->sizeX-35, -12);
    }
	
	function onShow() {
		$this->pager->clearItems();
        $this->populateList();
    }

    function populateList() {
		foreach ($this->group->getGroupUsers() as $admin) {		
			$this->pager->addItem(new AdminItem($admin, $this, $this->getRecipient()));
		}
	}
	
	function click_add($login2, $args){
		$login = $args['login'];
		if(AdminGroups::isInList($login)){
			$message = array(_('%adminerror%Player is already in the admin group : %variable%%1 %adminerror%. Plz remove firsty', AdminGroups::getAdmin($login)->getGroup()->getGroupName()));
			\ManiaLive\PluginHandler\PluginHandler::getInstance()->callPublicMethod(null, 'eXpansion\AdminGroups', 'exp_chatSendServerMessage', $message);
		}else{
			$this->group->addAdmin($login);
		}
		
		$this->login_add->setText("");
		$this->redraw($login2);	
	}
	
	function click_remove($login, $admin){
		$this->adminGroups->removeFromGroup($login, $this->group, $admin);
		$this->onShow();
		$this->redraw();
		
		$windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();

		foreach ($windows as $window) {
			$login = $window->getRecipient();
			$window->onShow();
			$window->redraw($login);
		}
	}
	
	function __destruct() {
        $this->permissions = array();
    }
	
}

?>
