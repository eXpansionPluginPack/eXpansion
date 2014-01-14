<?php

namespace ManiaLivePlugins\eXpansion\Core\types;

use ManiaLive\Utilities\Console;

/**
 * Description of MaxPlugin
 *
 * @author oliverde8
 */
class ExpPlugin extends BasicPlugin {
    
    /**
     * The actual Version of the Expansion Pack
     * @return type
     */
    public static function getMaxVersion() {
        return \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION;     
    }

    public function exp_onInit() {        
        
    }

}

?>
