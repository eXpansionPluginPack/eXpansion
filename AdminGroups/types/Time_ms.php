<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\types;

/**
 * Description of Time_ms
 *
 * @author oliverde8
 */
class Time_ms extends \ManiaLivePlugins\eXpansion\AdminGroups\types\absChecker
{

    public function check($data)
    {
        $v = explode(':', $data);

        return isset($v[0]) && isset($v[1]) && is_numeric($v[0]) && is_numeric($v[1]);
    }

    public function getErrorMsg()
    {
        return 'use time in format #variable#m:ss';
    }
}
