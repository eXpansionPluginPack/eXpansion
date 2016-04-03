<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Controls;

use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Controls\Recorditem;

class DediItem extends Recorditem
{

    function __construct($index, $record, $login, $highlite = false)
    {
        $outrec = new \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record();
        $outrec->login = $record['Login'];
        $outrec->time = $record['Best'];
        $outrec->nickName = $record['NickName'];

        parent::__construct($index, $outrec, $login, $highlite);
    }

}

?>

