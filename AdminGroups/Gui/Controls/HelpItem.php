<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\CmdMore;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as MyButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

/**
 * Description of HelpItem
 *
 * @author oliverde8
 */
class HelpItem extends Control
{
    protected $moreButton;
    protected $action;

    public function __construct($indexNumber, AdminCmd $cmd, $controller, $login)
    {
        $this->action = $this->createAction(array($this, 'cmdMore'), $cmd);

        $this->setSize(116, 4);
        $frame = new Frame();
        $frame->setSize($this->getSizeX(), $this->getSizeY());
        $frame->setLayout(new Line());

        $this->addComponent(new ListBackGround($indexNumber, $this->getSizeX(), $this->getSizeY()));

        $gui_cmd = new Label(50 * (.8 / .6), 4);
        $gui_cmd->setAlign('left', 'center');
        $gui_cmd->setText(__($cmd->getCmd(), $login));
        $gui_cmd->setScale(0.6);
        $frame->addComponent($gui_cmd);

        $gui_desc = new Label(
            ($this->getSizeX() - ($gui_cmd->getSizeX() / (.8 / .6))) * (1 / .6) - 8,
            4
        );
        $gui_desc->setAlign('left', 'center');
        if ($cmd->getHelp() != null) {
            $gui_desc->setText(__($cmd->getHelp(), $login));
        }
        $gui_desc->setScale(0.6);
        $frame->addComponent($gui_desc);

        $this->moreButton = new MyButton(30, 6);
        $this->moreButton->setAction($this->action);
        $this->moreButton->setText(__(AdminGroups::$txt_descMore, $login));
        $this->moreButton->setScale(0.4);
        $frame->addComponent($this->moreButton);

        $this->addComponent($frame);
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
        $this->moreButton->destroy();
        $this->destroyComponents();
        parent::destroy();
    }


    public function cmdMore($login, $cmd)
    {
        CmdMore::Erase($login);
        /** @var CmdMore $window */
        $window = CmdMore::Create($login);
        $window->setCommand($cmd);
        $window->setTitle(__(\eXpGetMessage("Admin Commands Extended Help"), $login));
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }
}
