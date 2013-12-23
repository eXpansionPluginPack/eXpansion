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
     * The Version of the Expansion Pack
     * @var type 
     */
    private static $version = '0.0.1';

    /**
     * The actual Version of the Expansion Pack
     * @return type
     */
    public static function getMaxVersion() {
        return self::$version;
    }

    public function exp_onInit() {
        $this->setVersion(self::$version);
        parent::exp_onInit();
        //Setting up the global Version of the eXpansion plugin pack
    }

}

?>
