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
    private $action_changePermissions;
    private $action_playerList;    
    private $plistButton;
    private $permiButton;

    function __construct(Group $group, $controller, $login) {
        $this->group = $group;
        $sizeX = 120;
        $sizeY = 4;

        $this->action_changePermissions = $this->createAction(array($controller, 'changePermission'), $group);
        $this->action_playerList = $this->createAction(array($controller, 'playerList'), $group);

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

        if (AdminGroups::hasPermission($login, 'group_admin') || (
                AdminGroups::hasPermission($login, 'own_group') && AdminGroups::getAdmin($login)->getGroup()->getGroupName() == $group->getGroupName())) {

            $this->plistButton = new MyButton(30, 6);
            $this->plistButton->setAction($this->action_playerList);
            $this->plistButton->setText(__(AdminGroups::$txt_playerList, $login));
            $this->plistButton->setScale(0.6);
            $frame->addComponent($this->plistButton);

            $this->permiButton = new MyButton(40, 6);
            $this->permiButton->setAction($this->action_changePermissions);
            $this->permiButton->setText(__(AdminGroups::$txt_permissionList, $login));
            $this->permiButton->setScale(0.6);
            $frame->addComponent($this->permiButton);
        }

        $this->addComponent($frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    public function destroy() {
        $this->plistButton->destroy();
        $this->permiButton->destroy();
        $this->clearComponents();


        parent::destroy();
    }

}

?>
