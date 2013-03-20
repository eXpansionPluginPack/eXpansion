<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

class Config extends \ManiaLib\Utils\Singleton {
    
    public $color_dedirecord = '$0b3';    
    public $recordMsg = '%1$s#dedirecord# secured the #rank#%2$s#dedirecord#. Dedimania Record!  time:#rank#%3$s #dedirecord#$n(#rank#%4$s#dedirecord#)!';
    public $newRecordMsg = '%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record!  time:#rank#%3$s';
    public $upgradeMsg = '#dedirecord#Dedimania global ranking limited to top #rank#15#dedirecord# for basic accounts.  $l[http://dedimania.net/tm2stats/?do=donation]Click here$l for more info..';
    public $supportMsg = '#dedirecord#Thank You for supporting Dedimania!  Your max record ranking is set at #rank#%s$1#dedirecord#.  $l[http://dedimania.net/tm2stats/?do=donation]Click here$l for more info..';
    public $show_record_msg_to_all = true;
    public $show_welcome_msg = true;
}

?>
