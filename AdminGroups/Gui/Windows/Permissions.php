<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of Permissions
 *
 * @author oliverde8
 */
class Permissions extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {
	private $adminGroups;
    private $pager;
    private $group;
    private $button_ok;
    private $button_cancel;
    private $permissions = array();

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
		
		$this->adminGroups = AdminGroups::getInstance();
		
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->button_ok = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_ok->setText(_("OK"));
        $this->button_ok->setAction($this->createAction(array($this, 'click_ok')));
        $this->mainFrame->addComponent($this->button_ok);

        $this->button_cancel = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_cancel->setText(_("Cancel"));
        $this->button_cancel->setAction($this->createAction(array($this, 'click_cancel')));
        $this->mainFrame->addComponent($this->button_cancel);
    }

    public function setGroup($g) {
        $this->group = $g;
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 25);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -10);

        $centerX = $this->sizeX / 2 - 10;
        $this->button_ok->setPosition($centerX + 5, -$this->sizeY + 5);
        $this->button_cancel->setPosition($centerX + 30, -$this->sizeY + 5);
    }

    function onShow() {
		$this->pager->clearComponents();
        $this->populateList();
    }

    function populateList() {
        $this->pager->clearItems();
        
        foreach ($this->adminGroups->getPermissionList() as $key => $value) {
            $cBox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4, 68);
            $cBox->setStatus($this->group->hasPermission($key));
            $cBox->setText($key);             
            $this->pager->addItem($cBox);
            $this->permissions[$key] = $cBox;
        }
    }

    function click_ok($login) {
        foreach ($this->permissions as $key => $val) {
            $this->group->addPermission($key, $val->getStatus());
        }
        $this->Erase($login);
    }

    function click_cancel() {
        $this->Erase($this->getRecipient());
    }

    function __destruct() {
        $this->permissions = array();
    }

}

?>
