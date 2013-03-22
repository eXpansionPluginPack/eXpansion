<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
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
    private $action_deleteGroup;
    private $plistButton;
    private $permiButton;
    private $deleteButton;

    function __construct($indexNumber, Group $group, $controller, $login) {
        $this->group = $group;
        $sizeX = 95;
        $sizeY = 4;

        $this->action_changePermissions = $this->createAction(array($controller, 'changePermission'), $group);
        $this->action_playerList = $this->createAction(array($controller, 'playerList'), $group);
        $this->action_deleteGroup = $this->createAction(array($controller, 'deleteGroup'), $group);

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize($sizeX, $sizeY);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        
        $this->addComponent(new ListBackGround($indexNumber, $sizeX, $sizeY));

        $gui_name = new \ManiaLib\Gui\Elements\Label(35*(0.8/0.6), 4);
        $gui_name->setAlign('left', 'center');
        $gui_name->setText($group->getGroupName());
        $gui_name->setScale(0.6);
        $frame->addComponent($gui_name);

        $gui_nbPlayers = new \ManiaLib\Gui\Elements\Label(20*(0.8/0.6), 4);
        $gui_nbPlayers->setAlign('center', 'center');
        $gui_nbPlayers->setText(sizeof($group->getGroupUsers()));
        $gui_nbPlayers->setScale(0.6);
        $frame->addComponent($gui_nbPlayers);

        if (AdminGroups::hasPermission($login, 'group_admin') || (
                AdminGroups::hasPermission($login, 'own_group') && AdminGroups::getAdmin($login)->getGroup()->getGroupName() == $group->getGroupName())) {

            $this->plistButton = new MyButton(30, 6);
            $this->plistButton->setAction($this->action_playerList);
            $this->plistButton->setText(__(AdminGroups::$txt_playerList, $login));
            $this->plistButton->setScale(0.4);
            $frame->addComponent($this->plistButton);

            $this->permiButton = new MyButton(40, 6);
            $this->permiButton->setAction($this->action_changePermissions);
            $this->permiButton->setText(__(AdminGroups::$txt_permissionList, $login));
            $this->permiButton->setScale(0.4);
            $frame->addComponent($this->permiButton);
            
            $this->deleteButton = new MyButton(40, 6);
            $this->deleteButton->setAction($this->action_deleteGroup);
            $this->deleteButton->setText(__(AdminGroups::$txt_deletegroup, $login));
            $this->deleteButton->setScale(0.4);
            $frame->addComponent($this->deleteButton);
        }

        $this->addComponent($frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    public function destroy() {
        if($this->permiButton != null){
            $this->permiButton->destroy();
            $this->plistButton->destroy();
            $this->deleteButton->destroy();
        }
        $this->permiButton=null;
        $this->plistButton=null;
        $this->deleteButton=null;
        $this->clearComponents();


        parent::destroy();
    }

}

?>
