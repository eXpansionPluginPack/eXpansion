<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of CmdMore
 *
 * @author oliverde8
 */
class CmdMore extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {
   
    private $label_cmd, $label_desc, $label_descm, $label_aliases;
    private $content_cmd, $content_desc, $content_descm, $content_aliases;
    
    private $cmd;
    
    protected function onConstruct() {
        parent::onConstruct();
        $this->label_cmd = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->label_cmd->setAlign('left', 'center');
        $this->label_cmd->setScale(0.8);
        $this->label_cmd->setPosition(2,-1);
        $this->mainFrame->addComponent($this->label_cmd);
        
        $this->label_desc = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->label_desc->setAlign('left', 'center');
        $this->label_desc->setScale(0.8);
        $this->label_desc->setPosY(-1);
        $this->mainFrame->addComponent($this->label_desc);
        
        $this->label_descm = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->label_descm->setAlign('left', 'center');
        $this->label_descm->setScale(0.8);
        $this->label_descm->setPosition(2,-10);
        $this->mainFrame->addComponent($this->label_descm);
        
        $this->label_aliases = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->label_aliases->setAlign('left', 'center');
        $this->label_aliases->setScale(0.8);
        $this->label_aliases->setPosY(-10);
        $this->mainFrame->addComponent($this->label_aliases);
        
        $this->content_cmd = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_cmd->setAlign('left', 'center');
        $this->content_cmd->setScale(0.8);
        $this->content_cmd->setPosition(2,-6);
        $this->mainFrame->addComponent($this->content_cmd);
        
        $this->content_desc = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_desc->setAlign('left', 'center');
        $this->content_desc->setScale(0.8);
        $this->content_desc->setPosY(-6);
        $this->mainFrame->addComponent($this->content_desc);
        
        $this->content_descm = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_descm->setAlign('left', 'center');
        $this->content_descm->setScale(0.8);
        $this->content_descm->setPosition(2,-15);
        $this->content_descm->setMaxline(100);
        $this->mainFrame->addComponent($this->content_descm);
        
        $this->content_aliases = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_aliases->setAlign('left', 'center');
        $this->content_aliases->setScale(0.8);
        $this->content_aliases->setPosY(-15);
        $this->content_aliases->setMaxline(100);
        $this->mainFrame->addComponent($this->content_aliases);
    }
    
    public function setCommand(\ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $command){
        $this->cmd = $command;
    }
    
    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        
        $this->label_cmd->setSizeX($this->getSizeX()/2-2);
        
        $this->label_desc->setPosX($this->getSizeX()/2+1);
        $this->label_desc->setSizeX($this->getSizeX()/2-2);
        
        $this->label_descm->setSizeX($this->getSizeX()/2-2);
        
        $this->label_aliases->setPosX($this->getSizeX()/2+1);
        $this->label_aliases->setSizeX($this->getSizeX()/2-2);
        
        $this->content_cmd->setSizeX($this->getSizeX()/2-2);
        
        $this->content_desc->setPosX($this->getSizeX()/2+1);
        $this->content_desc->setSizeX($this->getSizeX()/2-2);
        
        $this->content_descm->setSizeX($this->getSizeX()/2-2);
        $this->content_descm->setSizeY($this->getSizeY()-12);
        
        $this->content_aliases->setPosX($this->getSizeX()/2+1);
        $this->content_aliases->setSizeX($this->getSizeX()/2-2);
        $this->content_aliases->setSizeY($this->getSizeY()-12);
    }
    
    function onShow() {
        
        $this->label_cmd->setText('$w'.__(AdminGroups::$txt_command, $this->getRecipient()));
        $this->label_desc->setText('$w'.__(AdminGroups::$txt_description, $this->getRecipient()));
        $this->label_descm->setText('$w'.__(AdminGroups::$txt_descMore, $this->getRecipient()));
        $this->label_aliases->setText('$w'.__(AdminGroups::$txt_aliases, $this->getRecipient()));
        
        $this->content_cmd->setText('\admin '.$this->cmd->getCmd());
        $this->content_desc->setText(__($this->cmd->getHelp(),$this->getRecipient()));
        $this->content_descm->setText(__($this->cmd->getHelpMore(),$this->getRecipient()));
        
        $aliases = "";
        $i = 1;
        foreach($this->cmd->getAliases() as $alias){
            $aliases .= '$b'.$i.')$z '.$alias."\n";
            $i++;
        }
        $this->content_aliases->setText($aliases);
    }
    
}

?>
