<?php

namespace ManiaLivePlugins\eXpansion\Core;

class Config extends \ManiaLib\Utils\Singleton {

    public $debug = false;
    public $analytics = true;
    public $language = null;
    public $defaultLanguage = null;
    public $Colors_admin_error = '$d44'; // error message color for admin
    public $Colors_error = '$f00'; // general error message color
    public $Colors_admin_action = '$6af'; // admin actions color
    public $Colors_variable = '$eee'; // generic variable color
    public $Colors_record = '$3af'; // all other local records
    public $Colors_record_top = '$1F0'; // top5 local records
    public $Colors_dedirecord = '$3af'; // dedimania records
    public $Colors_rank = '$ff0'; // used in record messages and widgets for rank
    public $Colors_time = '$fff'; // used for record messages and widgets
    public $Colors_rating = '$fb3'; // map ratings color
    public $Colors_queue = '$8af'; // map queue messages
    public $Colors_personalmessage = '$0ff'; // personal messages
    public $Colors_admingroup_chat = '$f60'; // admin chat channel
    public $Colors_donate = '$0af'; // donate
    public $Colors_player = '$0af'; // used in joinleave-messages
    public $Colors_music = '$f0a'; // music box
    public $Colors_emote = '$9f0'; // music box
    public $Colors_quiz = '$3e3'; // quiz
    public $Colors_question = '$fa0'; // quiz answer
    public $Colors_vote = '$0f0'; // votes
    public $Colors_info = '$3af'; // votes
    public $Colors_vote_success = '$0f0'; // vote success
    public $Colors_vote_failure = '$f00'; // vote failure
    public $time_dynamic_max = '7:00'; // dynamic timelimit max time for /ta dynamic <x>
    public $time_dynamic_min = '4:00'; // dynamic timelimit min time for /ta dynamic <x>
    public $API_Version = '2013-04-16'; //ApiVersion can be 2011-10-06 for TM and 2013-04-16 for SM Add in config
    public $enableRanksCalc = true; // enable calculation of player ranks on checkpoints
    public $mapBase = "";
    public $defaultMatchSettingsFile = "eXpansion_autosave.txt";
    public $dedicatedConfigFile = "dedicated_cfg.txt";
    public $blackListSettingsFile = "blacklist.txt";
    public $guestListSettingsFile = "guestlist.txt";
    public $saveSettingsFile = "casualRace";
    public $contact = "YOUR@EMAIL.COM";
    public $disableGameMode = array();
    public $netLostTime = 4000;   // time in milliseconds for lastresponse time, used to determine netlost
    public $roundsPoints = array(10, 8, 6, 5, 4, 3, 2, 1);
    public $quitDialogManialink = "";
    public $useWhitelist = false;

}

?>