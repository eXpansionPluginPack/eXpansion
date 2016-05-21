<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\AdminItem;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;

/**
 * Description of Permissions
 *
 * @author oliverde8
 */
class Players extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;
    protected $group;
    protected $button_add;
    protected $button_select;
    protected $login_add;
    protected $action_add;
    private $action_select;
    protected $items = array();

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->addComponent($this->pager);


        $line = new \ManiaLive\Gui\Controls\Frame(4, -6);
        $layout = new \ManiaLib\Gui\Layouts\Line();
        $layout->setMargin(2);
        $line->setLayout($layout);

        $this->login_add = new Inputbox("login", 40);
        $this->login_add->setLabel(__("Login : ", $login));
        $this->login_add->setText("");
        $line->addComponent($this->login_add);

        $this->button_add = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_add->setText(__("Add", $login));
        $this->action_add = $this->createAction(array($this, 'clickAdd'));
        $this->button_add->setAction($this->action_add);
        $line->addComponent($this->button_add);

        $this->button_select = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
        $this->button_select->setText(__("Select", $login));
        $this->action_select = $this->createAction(array($this, 'clickSelect'));
        $this->button_select->setAction($this->action_select);
        $line->addComponent($this->button_select);

        $this->addComponent($line);
    }

    public function setGroup($g)
    {
        $this->group = $g;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 4, $this->sizeY - 12);
        $this->pager->setPosition(0, -7);
    }

    public function onShow()
    {
        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();

        $this->button_add->setText(__(AdminGroups::$txt_add, $this->getRecipient()));
        $this->populateList();
    }

    public function populateList()
    {
        $x = 0;
        foreach ($this->group->getGroupUsers() as $admin) {
            $this->items[$x] = new AdminItem($x, $admin, $this, $this->getRecipient());
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function clickAdd($login2, $args)
    {
        $adminGroups = AdminGroups::getInstance();
        $login = $args['login'];

        if ($login != "") {
            $adminGroups->addToGroup($login2, $this->group, $login);
        }

        $windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();
        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
            $window->refreshAll();
        }
    }

    public function clickSelect($login)
    {
        $window = \ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection::Create($login);
        $window->setController($this);
        $window->setTitle('Select Player to add to ' . $this->group->getGroupName());
        $window->setSize(85, 100);
        $window->populateList(array($this, 'selectPlayer'), 'select');
        $window->centerOnScreen();
        $window->show();
    }

    public function selectPlayer($login, $newlogin)
    {
        $this->clickAdd($login, array('login' => $newlogin));
        \ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection::Erase($login);
    }

    public function clickRemove($login, $admin)
    {
        $adminGroups = AdminGroups::getInstance();
        $adminGroups->removeFromGroup($login, $this->group, $admin);


        $windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();
        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
            $window->refreshAll();
        }
    }

    public function destroy()
    {
        $this->login_add->destroy();
        $this->button_add->destroy();
        foreach ($this->items as $item)
            $item->erase();
        $this->items = array();
        parent::destroy();
    }

}
