<?php

namespace ManiaLivePlugins\eXpansion\Helpers;

/**
 * Description of PlayerObj
 *
 * @author Reaby
 */
class PlayerObj
{

    /**
     * Parses spectatorinfos from numeric value
     *
     * @param mixed|integer $spectatorInfo
     * @return \Maniaplanet\DedicatedServer\Structures\Player
     */
    public static function parseSpecStatus($spectatorInfo)
    {
        $number = 00000;
        if (is_object($spectatorInfo))
            $number = $spectatorInfo->spectatorStatus;
        if (is_numeric($spectatorInfo))
            $number = $spectatorInfo;
        $obj = new \Maniaplanet\DedicatedServer\Structures\Player();
        $obj->currentTargetId = floor($number / 10000);
        $obj->autoTarget = intval(substr($number, -4, 1));
        $obj->pureSpectator = intval(substr($number, -3, 1));
        $obj->tempSpectator = intval(substr($number, -2, 1));
        $obj->spectator = intval(substr($number, -1, 1));
        return $obj;
    }

}
