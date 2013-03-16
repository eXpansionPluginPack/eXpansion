<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd;

/**
 * Description of HelpItem
 *
 * @author oliverde8
 */
class HelpItem extends \ManiaLive\Gui\Control {
    function __construct(AdminCmd $cmd, $controller, $login) {
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
        
        $this->addComponent($frame);
    }
}

?>
