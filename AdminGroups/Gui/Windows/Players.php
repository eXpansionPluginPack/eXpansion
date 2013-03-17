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
    private $action_add;
    private $items = array();

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();

        $this->adminGroups = AdminGroups::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->login_add = new Inputbox("login");
        $this->login_add->setLabel(__("Login : "));
        $this->login_add->setText("");
        $this->login_add->setScale(0.8);
        $this->mainFrame->addComponent($this->login_add);

        $this->button_add = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_add->setText(__("Add"));
        $this->action_add = $this->createAction(array($this, 'click_add'));
        $this->button_add->setAction($this->action_add);
        $this->button_add->setScale(0.8);
        $this->mainFrame->addComponent($this->button_add);
    }

    public function setGroup($g) {
        $this->group = $g;
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 4, $this->sizeY - 12);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(0, -7);

        $this->login_add->setSize($this->sizeX*(1/0.8) - 20, 7);
        $this->login_add->setPosition(0, -3);

        $this->button_add->setSize(30, 5);
        $this->button_add->setPosition($this->sizeX*(1/0.8) - 45*(1/0.8), -3);
    }

    function onShow() {
        foreach ($this->items as $item)
            $item->destroy();
        $this->pager->clearItems();
        $this->items = array();

        $this->button_add->setText(__(AdminGroups::$txt_add, $this->getRecipient()));
        $this->populateList();
    }

    function populateList() {
        $x = 0;
        foreach ($this->group->getGroupUsers() as $admin) {
            $this->items[$x] = new AdminItem($x,$admin, $this, $this->getRecipient());
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    function click_add($login2, $args) {
        $login = $args['login'];

        if ($login != "") {
            $this->adminGroups->addToGroup($login2, $this->group, $login);
        }

        $this->login_add->setText("");
        $this->onShow();
        $this->redraw($login2);
    }

    function click_remove($login, $admin) {
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

    public function destroy() {
        $this->login_add->destroy();
        $this->button_add->destroy();
        foreach ($this->items as $item)
            $item->destroy();
        $this->items = null;        
        parent::destroy();
    }

}

?>
