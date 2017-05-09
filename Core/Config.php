<?php
namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLib\Utils\Singleton;

class Config extends Singleton
{

    public $debug = false;
    public $analytics = true;
    public $language = null;
    public $defaultLanguage = null;
    public $Colors_admin_error = '$d10'; // error message color for admin
    public $Colors_error = '$d10'; // general error message color
    public $Colors_admin_action = '$dee'; // admin actions color
    public $Colors_variable = '$fff'; // generic variable color
    public $Colors_record = '$3af'; // all other local records
    public $Colors_record_top = '$bdd'; // top5 local records
    public $Colors_dedirecord = '$3af'; // dedimania records
    public $Colors_rank = '$dee'; // used in record messages and widgets for rank
    public $Colors_time = '$fff'; // used for record messages and widgets
    public $Colors_rating = '$bdd'; // map ratings color
    public $Colors_queue = '$bdd'; // map queue messages
    public $Colors_personalmessage = '$3bd'; // personal messages
    public $Colors_admingroup_chat = '$dde'; // admin chat channel
    public $Colors_donate = '$5a5'; // donate
    public $Colors_player = '$cdd'; // used in joinleave-messages
    public $Colors_joinmsg = '$bdd'; // used in joinleave-messages
    public $Colors_leavemsg = '$998'; // used in joinleave-messages
    public $Colors_music = '$9ad'; // music box
    public $Colors_emote = '$bdd'; // emotes
    public $Colors_quiz = '$5d3'; // quiz
    public $Colors_question = '$db1'; // quiz answer
    public $Colors_vote = '$9da'; // votes
    public $Colors_info = '$bdd'; // votes
    public $Colors_vote_success = '$5d3'; // vote success
    public $Colors_vote_failure = '$d10'; // vote failure
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
