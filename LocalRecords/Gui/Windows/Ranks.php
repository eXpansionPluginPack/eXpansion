<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows;

use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\RankItem;

use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of Records
 *
 * @author oliverde8
 */
class Ranks extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {
    
    private $frame;
    private $label_rank, $label_nick, $label_wins, $label_score, $label_finish, $label_nbRecords, $label_ptime, $label_lastRec;
    private $widths = array(1,6,2,2,2,2,3,3);
    
    private $pager;    
    private $items = array();
    
    protected function onConstruct() {
        parent::onConstruct();
        $sizeX = 100;
        $scaledSizes = Gui::getScaledSize($this->widths, $sizeX/.8);
        
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->pager->setPosX(0);
        $this->pager->setPosY(-4);
        $this->mainFrame->addComponent($this->pager);
        
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->mainFrame->addComponent($this->frame);

        $this->label_rank = new \ManiaLib\Gui\Elements\Label($scaledSizes[0], 4);
        $this->label_rank->setAlign('left', 'center');
        $this->label_rank->setScale(0.8);
        $this->frame->addComponent($this->label_rank);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 4);
        $this->label_nick->setAlign('left', 'center');
        $this->label_nick->setScale(0.8);
        $this->frame->addComponent($this->label_nick);
        
        $this->label_wins = new \ManiaLib\Gui\Elements\Label($scaledSizes[2], 4);
        $this->label_wins->setAlign('left', 'center');
        $this->label_wins->setScale(0.8);
        $this->frame->addComponent($this->label_wins);
        
        $this->label_score = new \ManiaLib\Gui\Elements\Label($scaledSizes[3], 4);
        $this->label_score->setAlign('left', 'center');
        $this->label_score->setScale(0.8);
        $this->frame->addComponent($this->label_score);
        
        $this->label_finish = new \ManiaLib\Gui\Elements\Label($scaledSizes[4], 4);
        $this->label_finish->setAlign('left', 'center');
        $this->label_finish->setScale(0.8);
        $this->frame->addComponent($this->label_finish);
        
        $this->label_nbRecords = new \ManiaLib\Gui\Elements\Label($scaledSizes[5], 4);
        $this->label_nbRecords->setAlign('left', 'center');
        $this->label_nbRecords->setScale(0.8);
        $this->frame->addComponent($this->label_nbRecords);
        
        $this->label_ptime = new \ManiaLib\Gui\Elements\Label($scaledSizes[6], 4);
        $this->label_ptime->setAlign('left', 'center');
        $this->label_ptime->setScale(0.8);
        $this->frame->addComponent($this->label_ptime);
        
        $this->label_lastRec = new \ManiaLib\Gui\Elements\Label($scaledSizes[7], 4);
        $this->label_lastRec->setAlign('left', 'center');
        $this->label_lastRec->setScale(0.8);
        $this->frame->addComponent($this->label_lastRec);
    }
    
    public function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX()/0.8) - 5);
        
        $this->label_rank->setSizeX($scaledSizes[0]);
        $this->label_nick->setSizeX($scaledSizes[1]);
        $this->label_wins->setSizeX($scaledSizes[2]);
        $this->label_score->setSizeX($scaledSizes[3]);
        $this->label_finish->setSizeX($scaledSizes[4]);
        $this->label_nbRecords->setSizeX($scaledSizes[5]);
        $this->label_ptime->setSizeX($scaledSizes[6]);
        $this->label_lastRec->setSizeX($scaledSizes[7]);
        $this->pager->setSize($this->getSizeX()-4, $this->getSizeY()-7);
        foreach ($this->items as $item)
            $item->setSizeX($this->getSizeX());
    }
    
    public function onDraw() {
        $this->label_rank->setText(__(LocalRecords::$txt_rank,$this->getRecipient()));
        $this->label_nick->setText(__(LocalRecords::$txt_nick,$this->getRecipient()));
        $this->label_wins->setText(__(LocalRecords::$txt_wins,$this->getRecipient()));
        $this->label_score->setText(__(LocalRecords::$txt_score,$this->getRecipient()));
        $this->label_finish->setText(__(LocalRecords::$txt_nbFinish,$this->getRecipient()));
        $this->label_nbRecords->setText(__(LocalRecords::$txt_nbRecords,$this->getRecipient()));
        $this->label_ptime->setText(__(LocalRecords::$txt_ptime,$this->getRecipient()));
        $this->label_lastRec->setText(__(LocalRecords::$txt_lastRec,$this->getRecipient()));
    }
    
    public function destroy(){
        foreach ($this->items as $item) {
           $item->erase();            
        }        
        $this->items = null;
        $this->pager->destroy();
        $this->clearComponents();                
        parent::destroy();
    }


    public function populateList($ranks, $limit){
        $x = 0;
        $login = $this->getRecipient();
        
        while($x < $limit && $x < sizeof($ranks)){
            $this->items[$x] = new RankItem($x, $login, $ranks[$x], $this->widths);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }
}

?>
