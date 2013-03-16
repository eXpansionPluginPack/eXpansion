<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd;

/**
 * Description of HelpItem
 *
 * @author oliverde8
 */
class HelpItem extends \ManiaLive\Gui\Control {
    
    private $moreButton;
    
    function __construct(AdminCmd $cmd, $controller, $login) {
        
        $this->action = $this->createAction(array($this, 'cmdMore'), $cmd);
        
        $this->setSize(120, 4);
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize(120, 4);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        
        $gui_cmd = new \ManiaLib\Gui\Elements\Label(50*(.8/.6), 4);
        $gui_cmd->setAlign('left', 'center');
        $gui_cmd->setText(__($cmd->getCmd(), $login));
        $gui_cmd->setScale(0.6);
        $frame->addComponent($gui_cmd);
        
        $gui_desc = new \ManiaLib\Gui\Elements\Label(100, 4);
        $gui_desc->setAlign('left', 'center');
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
    
    public function destroy() {
        $this->moreButton->destroy();
        $this->clearComponents();
        parent::destroy();
    }
    
    public function cmdMore($login, $cmd){
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
