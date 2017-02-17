<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\Admin;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as MyButton;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

/**
 * Description of GroupItem
 *
 * @author oliverde8
 */
class AdminItem extends Control
{

    private $plistButton;

    public function __construct($indexNumber, Admin $admin, $controller, $login)
    {
        $sizeX = 75;
        $sizeY = 6;

        $actionRemove = $this->createAction(array($controller, 'clickRemove'), $admin);

        $frame = new Frame();
        $frame->setSize($sizeX, $sizeY);
        $frame->setLayout(new Line());

        $this->addComponent(new ListBackGround($indexNumber, $sizeX, $sizeY));

        $gui_name = new Label(40, 4);
        $gui_name->setAlign('left', 'center');
        $gui_name->setText($admin->getLogin());
        $gui_name->setScale(0.8);
        $frame->addComponent($gui_name);

        $player = Storage::getInstance()->getPlayerObject($admin->getLogin());
        $gui_nick = new Label(32, 4);
        $gui_nick->setAlign('left', 'center');
        $gui_nick->setText($player != null ? $player->nickName : "");
        $gui_nick->setTextColor("fff");
        $gui_nick->setScale(0.8);

        $frame->addComponent($gui_nick);

        if (AdminGroups::hasPermission($login, Permission::ADMINGROUPS_ADMIN_ALL_GROUPS) && !$admin->isReadOnly()) {

            $this->plistButton = new MyButton(30, 4);
            $this->plistButton->setAction($actionRemove);
            $this->plistButton->setText(__(AdminGroups::$txt_rmPlayer, $login));
            $this->plistButton->setScale(0.6);
            $frame->addComponent($this->plistButton);
        }

        $this->addComponent($frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    // manialive 3.1 override to do nothing.
    public function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */
    public function erase()
    {
        if ($this->plistButton != null) {
            $this->plistButton->destroy();
        }
        $this->plistButton = null;
        $this->destroyComponents();
        parent::destroy();
    }
}
