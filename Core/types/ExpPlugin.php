<?php
namespace ManiaLivePlugins\eXpansion\Core\types;

use ManiaLivePlugins\eXpansion\Core\Core;

/**
 * Description of MaxPlugin
 *
 * @author oliverde8
 */
class ExpPlugin extends BasicPlugin
{

    /**
     * The actual Version of the Expansion Pack
     *
     * @return type
     */
    public static function getMaxVersion()
    {
        return Core::EXP_VERSION;
    }

    public function eXpOnInit()
    {

    }
}
