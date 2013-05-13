<?php

namespace ManiaLivePlugins\eXpansion\AutoLoad;

use ManiaLive\Utilities\Console;

class AutoLoad extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {
    
    
    private $plugins = array('eXpansion\Core'
                                    , 'eXpansion\AdminGroups'
                                    , 'eXpansion\Menu'
                                    , 'eXpansion\Adm'
                                    , 'eXpansion\Chat'
                                    , 'eXpansion\Chat_Admin'
                                    , 'eXpansion\CheckpointCount'
                                    , 'eXpansion\Database'
                                    , 'eXpansion\Emotes'
                                    , 'eXpansion\IdleKick'
                                    , 'eXpansion\LocalRecords'
                                    , 'eXpansion\ManiaExchange'
                                    , 'eXpansion\MapRatings'
                                    , 'eXpansion\Maps'
                                    , 'eXpansion\Notifications'
                                    , 'eXpansion\PersonalMessages'
                                    , 'eXpansion\Players'
                                    , 'eXpansion\Votes'
                                    , 'eXpansion\Widgets_PersonalBest'
                                    , 'eXpansion\Widgets_Record'
                            );
    
    public function exp_onReady(){
        
        Console::println("[eXpansion Pack]AutoLoading eXpansion pack ... ");
        
        //We Need the plugin Handler
        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
        
        $recheck = array();
        $lastSize = 0;
        
        $recheck = $this->loadPlugins($this->plugins, $pHandler);
        
        do{
            $lastSize = sizeof($recheck);
            $recheck = $this->loadPlugins($this->plugins, $pHandler);
        }while(!empty($recheck) && $lastSize != sizeof($recheck));
        
        if(!empty($recheck)){
            Console::println("[eXpansion Pack]AutoLoading eXpansion pack FAILED !! ");
            Console::println("[eXpansion Pack]All required plugins couldn't be loaded : ");
            foreach ($recheck as $pname){
                Console::println("[eXpansion Pack]....................".$pname);
            }
        }
        
    }
    
    public function loadPlugins($list, \ManiaLive\PluginHandler\PluginHandler $pHandler){
        $recheck = array();
        
        foreach ($list as $pname){
            try{
                if(!$pHandler->isLoaded($pname)){
                    Console::println("\n[eXpansion Pack]AutoLoading : Trying to Load $pname ... ");
                    if(!$pHandler->load($pname)){
                        Console::println("[eXpansion Pack]..............................FAIL -> will retry");
                        $recheck[] = $pname;
                    }else{
                        Console::println("[eXpansion Pack]..............................SUCESS");
                    }
                }
            } catch(\Exception $ex){
                echo "STRANGE\n";
                $recheck[] = $pname;
            }
        }
    }
}

?>
