<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

class Config extends \ManiaLib\Utils\Singleton {

    public $login = null;
    public $code = null;
    public $color_dedirecord = '$0b3';    
    public $recordMsg = '%1$s#dedirecord# secured the #rank#%2$s#dedirecord#. Dedimania Record!  time:#rank#%3$s #dedirecord#$n(#rank#%4$s#dedirecord#)!';
    public $newRecordMsg = '%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record!  time:#rank#%3$s';
    public $noRecordMsg = '#dedirecord#No dedimania records found for the map!';
    public $show_record_msg_to_all = true;
    public $show_welcome_msg = true;
    public $disableMessages = false;
}

?>
