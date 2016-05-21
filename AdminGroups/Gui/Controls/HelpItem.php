<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

/**
 * Description of HelpItem
 *
 * @author oliverde8
 */
class HelpItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $moreButton;

    function __construct($indexNumber, AdminCmd $cmd, $controller, $login)
    {
        $this->action = $this->createAction(array($this, 'cmdMore'), $cmd);

        $this->setSize(116, 4);
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize($this->getSizeX(), $this->getSizeY());
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->addComponent(new ListBackGround($indexNumber, $this->getSizeX(), $this->getSizeY()));

        $gui_cmd = new \ManiaLib\Gui\Elements\Label(50 * (.8 / .6), 4);
        $gui_cmd->setAlign('left', 'center');
        $gui_cmd->setText(__($cmd->getCmd(), $login));
        $gui_cmd->setScale(0.6);
        $frame->addComponent($gui_cmd);

        $gui_desc = new \ManiaLib\Gui\Elements\Label(($this->getSizeX() - ($gui_cmd->getSizeX() / (.8 / .6))) * (1 / .6) - 8, 4);
        $gui_desc->setAlign('left', 'center');
        if ($cmd->getHelp() != null)
            $gui_desc->setText(__($cmd->getHelp(), $login));
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
    function destroy()
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
        \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\CmdMore::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\CmdMore::Create($login);
        $window->setCommand($cmd);
        $window->setTitle(__(AdminGroups::$txt_helpTitle, $login));
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }

}

?>
