<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange;

class Config extends \ManiaLib\Utils\Singleton
{

    public $mxVote_enable = true;   // allow players/admins to queue tracks from MX
    public $mxVote_ratio = .5;      // vote ratio required to pass, 0.00 - 1.00
    public $mxVote_timeout = 60;     // '0' for server default, '1' for indefinite, otherwise set number of desired seconds
    public $mxVote_voters = 1;      // '0' means only active players, '1' means any player, '2' is for everybody, pure spectators included
    public $iconMx = 'http://mania-exchange.com/Content/images/planet_mx_logo.png';
    public $iconAward = '';
    public $iconVisit = 'http://mania-exchange.com/Content/images/planet_mx_logo.png';
    public $juke_newmaps = true;  // whaever newly added maps should be put to jukebox

}
