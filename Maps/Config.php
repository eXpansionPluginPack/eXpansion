<?php

namespace ManiaLivePlugins\eXpansion\Maps;

class Config extends \ManiaLib\Utils\Singleton {

    public $bufferSize = 5;

    public $showNextMapWidget = true;
    public $showEndMatchNotices = true;

    public $msg_addQueue = '#variable#%1$s  #queue#has been added to the map queue by #variable#%3$s#queue#, in the #variable#%5$s #queue#position';  // '%1$s' = Map Name, '%2$s' = Map author %, '%3$s' = nickname, '%4$s' = login, '%5$s' = # in queue
    public $msg_nextQueue = '#queue#Next map will be #variable#%1$s  #queue#by #variable#%2$s#queue#, as requested by #variable#%3$s';  // '%1$s' = Map Name, '%2$s' = Map author %, '%3$s' = nickname, '%4$s' = login
    public $msg_nextMap = '#queue#Next map will be #variable#%1$s  #queue#by #variable#%2$s#queue#';  // '%1$s' = Map Name, '%2$s' = Map author
    public $msg_queueNow = '#queue#Map changed to #variable#%1$s  #queue#by #variable#%2$s#queue#, as requested by #variable#%3$s';  // '%1$s' = Map Name, '%2$s' = Map author %, '%3$s' = nickname, '%4$s' = login

}
?>
