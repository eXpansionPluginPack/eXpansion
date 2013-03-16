<?php

namespace ManiaLivePlugins\eXpansion\Widgets_PersonalBest;

use \ManiaLive\Event\Dispatcher;

use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

use ManiaLivePlugins\eXpansion\Widgets_PersonalBest\Gui\Widgets\PBPanel;

/**
 * Description of Widgets_PersonalBest
 *
 * @author oliverde8
 */
class Widgets_PersonalBest extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {
    
    public function exp_onLoad() {
        parent::exp_onLoad();
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_PERSONAl_BEST);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_NEW_RECORD);
    }
    
    public function exp_onReady() {
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }
    
    function onPlayerConnect($login, $isSpectator) {
        $this->displayWidget($login);
    }
    
    public function onPersonalBestRecord(Record $record){
        $this->redrawRecordWidget($record->login, $record);
    }
    
    public function onNewRecord($records){
        foreach ($this->storage->players as $player)
            $this->redrawWidget($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->redrawWidget($player->login, true);   
    }
    
     public function redrawWidget($login = null){
        $record = null;
        if($login != null){
            $record = $this->callPublicMethod('eXpansion\\LocalRecords', 'getCurrentChallangePlayerRecord', $login);
        }
        $this->redrawRecordWidget($login, $record);
    }
    
    public function displayWidget($login = null){
        $record = null;
        if($login != null){
            $record = $this->callPublicMethod('eXpansion\\LocalRecords', 'getCurrentChallangePlayerRecord', $login);
        }
        $this->displayRecordWidget($login, $record);
    }
    
    function redrawRecordWidget($login, $record){
        $panel = PBPanel::Get($login);
     //   echo "Redraw for : $login";
        if(isset($panel[0])){
         //   echo "Succes";
            $panel[0]->setRecord($record);
            $panel[0]->redraw($login);
        }
       // echo "\n";
    }
    
    function displayRecordWidget($login, $record) {
        if ($login == null)
            PBPanel::EraseAll();
        else
            PBPanel::Erase($login);

        $info = PBPanel::Create($login);
        $info->setRecord($record);
        $info->setSize(30, 6);
        $info->setPosition(135, -76);
        $info->show();
    }
}

?>
