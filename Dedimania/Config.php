<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

class Config extends \ManiaLib\Utils\Singleton {

    public $login = null;
    public $code = null;
    public $color_dedirecord = '$0b3';    
    public $recordMsg = '%1$s#dedirecord# improved the #rank#%2$s#dedirecord#. Dedimania Record!  #variable#%2$s: #rank#%3$s #dedirecord#(#variable#$n%4$s-#rank#%5$s#dedirecord#$n)'; // %1$s = nickname, %2$s = place, %3$s = time, %4$s = old place %5$s = difference 
    public $newRecordMsg = '%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record! #variable#%2$s: #rank#%3$s';
    public $noRecordMsg = '#dedirecord#No dedimania records found for the map!';
    public $show_record_msg_to_all = true;
    public $show_welcome_msg = true;
    public $disableMessages = false;
}

?>
