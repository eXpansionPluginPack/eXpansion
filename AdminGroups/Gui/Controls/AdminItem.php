<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\AdminGroups\Admin;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use \ManiaLivePlugins\eXpansion\AdminGroups\Permission;

/**
 * Description of GroupItem
 *
 * @author oliverde8
 */
class AdminItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $admin;

    private $action_remove;

    private $plistButton;

    function __construct($indexNumber, Admin $admin, $controller, $login)
    {
        $this->group = $admin;
        $sizeX = 75;
        $sizeY = 6;

        $this->action_remove = $this->createAction(array($controller, 'click_remove'), $admin);

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize($sizeX, $sizeY);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->addComponent(new ListBackGround($indexNumber, $sizeX, $sizeY));

        $gui_name = new \ManiaLib\Gui\Elements\Label(40, 4);
        $gui_name->setAlign('left', 'center');
        $gui_name->setText($admin->getLogin());
        $gui_name->setScale(0.8);
        $frame->addComponent($gui_name);

        $player = \ManiaLive\Data\Storage::getInstance()->getPlayerObject($admin->getLogin());
        $gui_nick = new \ManiaLib\Gui\Elements\Label(32, 4);
        $gui_nick->setAlign('left', 'center');
        $gui_nick->setText($player != null ? $player->nickName : "");
        $gui_nick->setTextColor("fff");
        $gui_nick->setScale(0.8);

        $frame->addComponent($gui_nick);

        if (AdminGroups::hasPermission($login, Permission::ADMINGROUPS_ADMIN_ALL_GROUPS) && !$admin->isReadOnly()) {

            $this->plistButton = new MyButton(30, 4);
            $this->plistButton->setAction($this->action_remove);
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
    function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    public function erase()
    {
        if ($this->plistButton != null)
            $this->plistButton->destroy();
        $this->plistButton = null;
        $this->destroyComponents();
        parent::destroy();
    }

}

?>
