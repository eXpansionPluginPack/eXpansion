<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\HelpItem;
use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of Help
 *
 * @author oliverde8
 */
class Help extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $adminGroups;
    private $pager;
    private $items = array();
    
    private $label_cmd, $label_desc;
    
    protected function onConstruct() {
        parent::onConstruct();
        $this->adminGroups = AdminGroups::getInstance();
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
        
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize(120, 4);
        $frame->setPosY(0);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->mainFrame->addComponent($frame);

        $this->label_cmd = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->label_cmd->setAlign('left', 'center');
        $this->label_cmd->setScale(0.8);
        $frame->addComponent($this->label_cmd);

        $this->label_desc = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->label_desc->setAlign('left', 'center');
        $this->label_desc->setScale(0.8);
        $frame->addComponent($this->label_desc);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 13);
        $this->pager->setStretchContentX($this->sizeX - 4);
        $this->pager->setPosition(0, -4);
    }

    function onShow() {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->pager->clearItems();
        $this->items = array();

        $this->label_cmd->setText(__(AdminGroups::$txt_command, $this->getRecipient()));
        $this->label_desc->setText(__(AdminGroups::$txt_description, $this->getRecipient()));
        
        $this->populateList();
    }

    function populateList() {

        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->adminGroups->getAdminCommands() as $cmd) {
            $this->items[$x] = new HelpItem($x, $cmd, $this, $login);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }
}
?>
