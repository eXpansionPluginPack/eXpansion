<?php

namespace ManiaLivePlugins\eXpansion\Dedimania_Script;

class _Config extends \ManiaLib\Utils\Singleton {

    public $login = null;
    public $code = null;
    public $recordMsg = '%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record!  #rank#%2$s: #time#%3$s #dedirecord#(#rank#%4$s #time#-%5$s#dedirecord#)'; // %1$s = nickname, %2$s = place, %3$s = time, %4$s = old place %5$s = difference 
    public $newRecordMsg = '%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record! #time#%3$s';
	public $equalRecordMsg = '%1$s#dedirecord# equaled the #rank#%2$s#dedirecord#. Dedimania Record! #time#%3$s';
    public $noRecordMsg = '#dedirecord#No dedimania records found for the map!';
    public $show_record_msg_to_all = true;
    public $show_welcome_msg = true;
    public $disableMessages = false;
}

?>
