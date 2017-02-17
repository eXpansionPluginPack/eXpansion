<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

class Config extends \ManiaLib\Utils\Singleton
{

    public $login = null;
    public $code = null;
    public $show_record_msg_to_all = true;
    public $show_welcome_msg = true;
    public $disableMessages = false;
    public $noRedirectTreshold = 30;
    public $allowBannedPlayersToJoin = false;
}
