<?php

namespace ManiaLivePlugins\eXpansion\Core;

class Config extends \ManiaLib\Utils\Singleton {

    public $debug = false;
    public $language = null;
    public $defaultLanguage = null;
    public $Colors_admin_error = '$d44';  // error message color for admin
    public $Colors_error = '$f00';   // general error message color
    
    public $Colors_admin_action = '$6af'; // admin actions color
    public $Colors_variable = '$fff'; // generic variable color
    
    public $Colors_record = '$0af'; // all other local records
    public $Colors_record_top = '$0e0'; // top5 local records
    public $Colors_dedirecord = '$0af'; // dedimania records            
    public $Colors_rank = '$ff0'; // used in record messages for rank    
    public $Colors_time = '$fff';
    
    public $Colors_rating = '$fff'; // map ratings color
    public $Colors_queue = '$fff';  // map queue messages
        
    public $Colors_personalmessage = '$c0c'; // personal messages
    public $Colors_admingroup_chat = '$6af'; // admin chat channel
    
    public $Colors_donate = '$0af'; // donate
    public $Colors_player = '$z$s$0af';  // used in joinleave-messages
    
    public $Colors_music = '$fff';       // music box
    
    public $Colors_quiz = '$z$s$3e3';    // quiz
    public $Colors_question = '$z$s$o$fa0';  // quiz answer
    
    public $Colors_vote = '$0f0';  // votes
    public $Colors_vote_success = '$0f0';  // vote success
    public $Colors_vote_failure = '$f00';  // vote failure
    
    public $time_dynamic_max = '7:00';  // dynamic timelimit max time for /ta dynamic <x>
    public $time_dynamic_min = '4:00';  // dynamic timelimit min time for /ta dynamic <x>
    
    public $API_Version = '2011-10-06'; //ApiVersion can be 2011-10-06 for TM and 2013-04-16 for SM Add in config 
    
    public $enableRanksCalc = true;  // enable calculation of player ranks on checkpoints
    
}

?>