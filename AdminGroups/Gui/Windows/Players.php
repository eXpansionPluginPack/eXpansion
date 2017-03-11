<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Group;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\AdminItem;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of Permissions
 *
 * @author oliverde8
 */
class Players extends Window
{
    /** @var  Pager */
    protected $pager;
    /** @var  Group */
    protected $group;
    /** @var  Button */
    protected $button_add;
    /** @var  Button */
    protected $button_select;
    /** @var Inputbox */
    protected $login_add;

    protected $action_add;
    private $action_select;

    /** @var AdminItem[] */
    protected $items = array();

    /**
     *
     */
    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $line = new Frame(0, 0);
        $layout = new Line();
        $layout->setMargin(2);
        $line->setLayout($layout);

        $this->login_add = new Inputbox("login", 40);
        $this->login_add->setLabel(__("Login : ", $login));
        $this->login_add->setText("");
        $line->addComponent($this->login_add);

        $this->button_add = new Button(20, 5);
        $this->button_add->setText(__("Add", $login));
        $this->action_add = $this->createAction(array($this, 'clickAdd'));
        $this->button_add->setAction($this->action_add);
        $line->addComponent($this->button_add);

        $this->button_select = new Button(20, 5);
        $this->button_select->setText(__("Select", $login));
        $this->action_select = $this->createAction(array($this, 'clickSelect'));
        $this->button_select->setAction($this->action_select);
        $line->addComponent($this->button_select);

        $this->mainFrame->addComponent($line);

        $this->pager = new Pager();
        $this->pager->setPosition(0, 4);
        $this->mainFrame->addComponent($this->pager);
    }

    /**
     * @param $g
     */
    public function setGroup($g)
    {
        $this->group = $g;
    }


    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param $oldX
     * @param $oldY
     */
    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX + 1, $this->sizeY - 10);
        $this->pager->setPosition(0, -7);
    }

    /**
     *
     */
    public function onShow()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->pager->clearItems();
        $this->items = array();

        $this->button_add->setText(__(AdminGroups::$txt_add, $this->getRecipient()));
        $this->populateList();
    }

    /**
     *
     */
    public function populateList()
    {
        $x = 0;
        foreach ($this->group->getGroupUsers() as $admin) {
            $this->items[$x] = new AdminItem($x, $admin, $this, $this->getRecipient());
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    /**
     * @param $login2
     * @param $args
     */
    public function clickAdd($login2, $args)
    {
        $adminGroups = AdminGroups::getInstance();
        $login = $args['login'];

        if ($login != "") {
            $adminGroups->addToGroup($login2, $this->group, $login);
        }

        /** @var Groups[] $windows */
        $windows = Groups::GetAll();
        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
            $window->refreshAll();
        }
    }

    /**
     * @param $login
     */
    public function clickSelect($login)
    {
        /** @var PlayerSelection $window */
        $window = PlayerSelection::Create($login);
        $window->setController($this);
        $window->setTitle('Select Player to add to ' . $this->group->getGroupName());
        $window->setSize(85, 100);
        $window->populateList(array($this, 'selectPlayer'), 'select');
        $window->centerOnScreen();
        $window->show();
    }

    /**
     * @param $login
     * @param $newlogin
     */
    public function selectPlayer($login, $newlogin)
    {
        $this->clickAdd($login, array('login' => $newlogin));
        PlayerSelection::Erase($login);
    }

    /**
     * @param $login
     * @param $admin
     */
    public function clickRemove($login, $admin)
    {
        $adminGroups = AdminGroups::getInstance();
        $adminGroups->removeFromGroup($login, $this->group, $admin);

        /** @var Groups[] $windows */
        $windows = Groups::GetAll();
        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $window->onShow();
            $window->redraw($login);
            $window->refreshAll();
        }
    }

    /**
     *
     */
    public function destroy()
    {
        $this->login_add->destroy();
        $this->button_add->destroy();
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = array();
        parent::destroy();
    }
}
