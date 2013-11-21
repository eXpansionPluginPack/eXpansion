<?php

namespace ManiaLivePlugins\eXpansion\Core;

class Config extends \ManiaLib\Utils\Singleton {

    public $debug = false;
    public $language = null;
    public $defaultLanguage = null;
    public $Colors_admin_error = '$f44';
    public $Colors_admin_action = '$0ae';
    public $Colors_variable = '$eee';
    public $Colors_record = '$0f3';
    public $Colors_rank = '$fe5';
    public $Colors_rating = '$fb3';
    public $Colors_queue = '$8af';
    public $API_Version = '2013-05-16'; //ApiVersion can be 2011-10-06 for TM and 2013-04-16 for SM Add in config 

}

?>