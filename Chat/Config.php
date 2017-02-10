<?php

namespace ManiaLivePlugins\eXpansion\Chat;

use ManiaLib\Utils\Singleton;

class Config extends Singleton
{

    public $publicChatColor = '$ff0';
    public $otherServerChatColor = '$0d0';
    public $adminChatColor = '$ff0';
    public $adminSign = "";
    public $chatSeparator = '$0af»$z$s ';
    public $allowMPcolors = true;
    public $publicChatActive = true;
    public $enableSpectatorChat = false;
    public $useProfanityFilter = false;
    public $useChannels = false;
    public $channels = array("English", "German", "French");
}
